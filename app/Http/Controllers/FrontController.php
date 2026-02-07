<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\OpenAiAuth;
use App\Services\AuthorizeNetService;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Http\Request;
use PHPExperts\RESTSpeaker\RESTSpeaker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Country;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\EmailLog;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\Banner;
use App\Models\HomePage;
use App\Models\HomePage2Section;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Enquiry;
use App\Models\UserActivity;
use App\Models\UserLocation;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Coupon;
use App\Models\CancelOrderReason;
use App\Models\ProductVariation;
use App\Models\VariationAttribute;
use App\Models\UserWishlist;
use App\Models\UserReview;
use App\Models\UserView;
use App\Services\Schema\ProductSchemaService;

use Auth;
use Session;
use Helper;
use Hash;
use stripe;
use DB;
use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set("Asia/Calcutta");

class FrontController extends Controller
{
    /* home */
    public function home(Request $request)
    {
        if(session('shipping_country') == ''){
            $request->session()->put('shipping_country', 'United States');
        }        

        $data['banners1']                   = Banner::where('status', '=', 1)->where('section', '=', 1)->orderBy('id', 'DESC')->get();
        $data['banners2']                   = Banner::where('status', '=', 1)->where('section', '=', 2)->orderBy('id', 'DESC')->get();
        $data['sections2']                  = HomePage2Section::where('status', '=', 1)->where('section', '=', 3)->orderBy('id', 'ASC')->get();
        $data['home_page']                  = HomePage::where('status', '=', 1)->where('id', '=', 1)->first();
        $data['products']                   = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->orderBy('id', 'DESC')->limit(5)->get();
        $data['sections5']                  = HomePage2Section::where('status', '=', 1)->where('section', '=', 5)->orderBy('id', 'ASC')->get();
        // Helper::pr($data['sections5']);

        $title                          = 'Home';
        $page_name                      = 'home';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* home */
    /* category */
    public function category($slug)
    {
        $data['getCategory']            = Category::where('slug', '=', $slug)->first();
        $parent_id                      = (($data['getCategory']) ? $data['getCategory']->id : 0);
        $data['subcategory']            = Category::where('parent_id', '=', $parent_id)->get();

        // $data['products']               = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->where('main_category', '=', $parent_id)->orderBy('id', 'DESC')->get();
        $data['productCount']           = Product::where('status', '=', 1)->where('main_category', '=', $parent_id)->orderBy('id', 'DESC')->count();
        $data['products']               = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->where('main_category', '=', $parent_id)->orderBy('id', 'DESC')->paginate(12);

        $data['cat']                    = $data['getCategory'];

        $title                          = (($data['getCategory']) ? $data['getCategory']->category_name : "");
        $page_name                      = 'category';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* category */
    /* sub category */
    public function subcategory(Request $request, $slug1, $slug2)
    {
        $data['slug1']                   = $slug1;
        $data['slug2']                   = $slug2;
        $data['getCategory']            = Category::where('slug', '=', $slug1)->first();
        $parent_id                      = (($data['getCategory']) ? $data['getCategory']->id : 0);

        $data['subcategory']            = Category::where('slug', '=', $slug2)->first();
        $child_id                       = (($data['subcategory']) ? $data['subcategory']->id : 0);

        // $data['products']               = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->where('main_category', '=', $parent_id)->where('sub_category', '=', $child_id)->orderBy('id', 'DESC')->get();
        $data['productCount']           = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->where('main_category', '=', $parent_id)->where('sub_category', '=', $child_id)->orderBy('id', 'DESC')->count();
        $data['products']               = Product::select('id', 'name', 'slug', 'discounted_price', 'cover_image')->where('status', '=', 1)->where('main_category', '=', $parent_id)->where('sub_category', '=', $child_id)->orderBy('id', 'DESC')->paginate(9);

        $data['parent_id']              = $parent_id;
        $data['child_id']               = $child_id;
        $data['minPrice']               = Product::where('status', '=', 1)->where('sub_category', '=', $child_id)->min('discounted_price');
        $data['maxPrice']               = Product::where('status', '=', 1)->where('sub_category', '=', $child_id)->max('discounted_price');
        $data['filter_by']              = '';
        $data['category_filter']        = [];

        $data['cat']                    = $data['subcategory'];

        if ($request->isMethod('post')) {
            $postData       = $request->all();
            // Helper::pr($postData);
            $parent_id  = $request->parent_id;
            $child_id   = $request->child_id;
            $min_price  = $request->min_price;
            $max_price  = $request->max_price;
            $getAttrs   = Attribute::select('id', 'name')->where('parent_category', '=', $parent_id)->where('sub_category_id', '=', $child_id)->where('status', '!=', 3)->get();
            $childIds   = [];
            $category_filter   = [];
            if ($getAttrs) {
                foreach ($getAttrs as $getAttr) {
                    if (array_key_exists('attr_vals' . $getAttr->id, $postData)) {
                        $attr_vals  = $postData['attr_vals' . $getAttr->id];
                        if (!empty($attr_vals)) {
                            for ($a = 0; $a < count($attr_vals); $a++) {
                                $category_filter[]      = $attr_vals[$a];
                                $attr_vals_array        = explode("-", $attr_vals[$a]);
                                $childIds[]             = $attr_vals_array[1];
                            }
                        }
                    }
                }
            }
            $childIdString = implode(",", $childIds);
            $productIds = ProductAttribute::select('product_id')->whereIn('product_attribute_value_id', $childIds)->groupBy('product_id')->get();
            $getProducts = [];
            if (!empty($productIds)) {
                foreach ($productIds as $productId) {
                    $getSingleProduct   = Product::select('id', 'name', 'slug', 'cover_image', 'short_description', 'base_price')->where('id', '=', $productId->product_id)->first();
                    if ($getSingleProduct) {
                        if (($getSingleProduct->base_price >= $min_price) && ($getSingleProduct->base_price <= $max_price)) {
                            $getProducts[]        = $getSingleProduct;
                        }
                    }
                }
            }
            $data['getProducts']            = $getProducts;
            $data['category_filter']        = $category_filter;
            $data['minPrice']               = $min_price;
            $data['maxPrice']               = $max_price;
        }
        $title                          = (($data['subcategory']) ? $data['subcategory']->category_name : "");
        $page_name                      = 'sub-category';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function productSorting(Request $request, $slug, $id)
    {
        $postData                       = $request->all();
        $filter_by                      = $request->filter_by;
        if ($filter_by == '') {
            return redirect(url('sub-category/' . $slug . '/' . $id));
        }
        $filter_by_array                = explode('-', $filter_by);
        $sort_field                     = $filter_by_array[0];
        if ($sort_field == 'price') {
            $orderField = 'base_price';
        } else {
            $orderField = 'name';
        }
        $orderType                      = $filter_by_array[1];
        $data['filter_by']              = $filter_by;
        $id                             = Helper::decoded($id);
        $data['slug']                   = $slug;
        $data['id']                     = $id;
        $data['getCategory']            = Category::where('id', '=', $id)->first();
        $data['getProducts']            = Product::select('id', 'name', 'slug', 'cover_image', 'short_description', 'base_price')->where('status', '=', 1)->where('sub_category', '=', $id)->orderBy($orderField, $orderType)->get();
        $data['minPrice']               = Product::where('status', '=', 1)->where('sub_category', '=', $id)->min('base_price');
        $data['maxPrice']               = Product::where('status', '=', 1)->where('sub_category', '=', $id)->max('base_price');
        $data['parent_id']              = $data['getCategory']->parent_id;
        $data['child_id']               = $data['getCategory']->id;
        $data['category_filter']        = [];
        $data['cat']                    = $data['getCategory'];

        $title                          = (($data['getCategory']) ? $data['getCategory']->category_name : "Parent Category");
        $page_name                      = 'sub-category';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* sub category */
    /* product details */
    public function productDetails(Request $request, $slug, $id)
    {
        $id                             = Helper::decoded($id);
        $data['slug']                   = $slug;
        $data['product']                = Product::where('id', '=', $id)->first();
        $data['product_id']             = $id;
        $data['product_slug']           = $slug;
        $data['product_images']         = ProductImage::select('image')->where('product_id', '=', $id)->get();

        $data['reviewCount']            = UserReview::where('product_id', '=', $id)->where('status', '=', 1)->count();
        $data['reviewSum']              = UserReview::where('product_id', '=', $id)->where('status', '=', 1)->sum('rating');
        $data['avgRating']              = (($data['reviewCount'] > 0) ? ($data['reviewSum'] / $data['reviewCount']) : 0);

        if($data['product']){
            if($data['product']->meta_title != ''){
                $meta_title = $data['product']->meta_title;
            } else {
                $meta_title = $data['product']->name;
            }

            if($data['product']->meta_description != ''){
                $meta_description = $data['product']->meta_description;
            } else {
                $meta_description = (($data['product']->short_description !='')?$data['product']->short_description:$data['product']->long_description);
            }

            if($data['product']->meta_keywords != ''){
                $meta_keywords = $data['product']->meta_keywords;
            } else {
                $meta_keywords = $data['product']->tags;
            }
            $cat = [
                'meta_title'        => $meta_title,
                'meta_description'  => $meta_description,
                'meta_keywords'     => $meta_keywords,
            ];
            $data['cat']                    = (object)$cat;
        } else {
            $data['cat']                    = $data['product'];
        }

        // variation
        $dropdownValues = [];
        $getProductparentAttrs = VariationAttribute::select('parent_attr_id')->where('status', '=', 1)->where('product_id', '=', $id)->groupBy('parent_attr_id')->get();
        if ($getProductparentAttrs) {
            foreach ($getProductparentAttrs as $getProductparentAttr) {
                $parent_attr_id = $getProductparentAttr->parent_attr_id;
                $getAttributeName = Attribute::select('name')->where('status', '=', 1)->where('id', '=', $parent_attr_id)->first();
                $getProductparentAttrVals = VariationAttribute::select('attribute_id')->where('status', '=', 1)->where('product_id', '=', $id)->where('parent_attr_id', '=', $parent_attr_id)->get();
                // Helper::pr($getProductparentAttrVals);
                $attr_vals = [];
                if ($getProductparentAttrVals) {
                    foreach ($getProductparentAttrVals as $getProductparentAttrVal) {
                        // $dropdownValues['variation' . $parent_attr_id][] = $parent_attr_id . '/' . $getProductparentAttrVal->attribute_id;
                        $getAttributeValName = AttributeValue::select('attr_value')->where('status', '=', 1)->where('id', '=', $getProductparentAttrVal->attribute_id)->first();
                        $attr_vals[] = [
                            'attr_val_id'   => $getProductparentAttrVal->attribute_id,
                            'attr_val_name' => (($getAttributeValName) ? $getAttributeValName->attr_value : ''),
                        ];
                    }
                }
                $dropdownValues[] = [
                    'attr_id'   => $parent_attr_id,
                    'attr_name' => (($getAttributeName) ? $getAttributeName->name : ''),
                    'attr_vals' => $attr_vals,
                ];
            }
        }

        // Function to remove duplicates from nested arrays
        foreach ($dropdownValues as &$attribute) {
            $uniqueVals = [];
            foreach ($attribute['attr_vals'] as $value) {
                // Use attr_val_id and attr_val_name as a unique key
                $key = $value['attr_val_id'] . '_' . $value['attr_val_name'];
                if (!isset($uniqueVals[$key])) {
                    $uniqueVals[$key] = $value;
                }
            }
            // Replace attr_vals with unique values
            $attribute['attr_vals'] = array_values($uniqueVals);
        }
        $data['variations'] = $dropdownValues;
        // Helper::pr($data['variations']);
        // variation

        if ($request->isMethod('post')) {
            $postData       = $request->all();
            $fields         = [
                'user_id'       => $postData['user_id'],
                'product_id'    => $postData['product_id'],
                'name'          => $postData['name'],
                'email'         => $postData['email'],
                'rating'        => $postData['stars'],
                'title'         => $postData['title'],
                'comment'       => $postData['comment'],
            ];
            // Helper::pr($fields);
            UserReview::insert($fields);
            $uId                                = $postData['user_id'];
            $getUser                            = User::where('id', '=', $uId)->first();
            $product_id                         = $postData['product_id'];
            $getProduct                         = Product::where('id', '=', $product_id)->first();
            /* email functionality */
            $mailData['getProduct']     = $getProduct;
            $mailData['getReview']      = $fields;
            $mailData['mailHeader']     = 'Review successfully submitted on ' . $mailData['getProduct']->name;
            $message                    = view('email-templates.review-submit', $mailData);
            $generalSetting             = GeneralSetting::find('1');
            $subject                    = $generalSetting->site_name . ' :: Review successfully submitted on ' . $mailData['getProduct']->name;
            $this->sendMail($generalSetting->system_email, $subject, $message);
            /* email functionality */
            /* email log save */
            $postData2 = [
                'name'                  => $getUser->first_name . ' ' . $getUser->last_name,
                'email'                 => $getUser->email,
                'subject'               => $subject,
                'message'               => $message
            ];
            EmailLog::insertGetId($postData2);
            /* email log save */
            $currentUrl = url('product-details/' . Helper::encoded($postData['product_id']));
            return redirect($currentUrl)->with('success_message', 'Product Review Submitted Successfully. Wait For Admin Approval !!!');
        }

        /* similar products */
        $sub_category           = (($data['product']) ? $data['product']->sub_category : '');
        $sqlQuery               = "SELECT id FROM products WHERE status = 1 AND id!=$id AND sub_category = '$sub_category' ORDER BY rand() LIMIT 5";
        $getSimilarProductIds   = DB::select($sqlQuery);
        $similarProducts = [];
        if ($getSimilarProductIds) {
            foreach ($getSimilarProductIds as $getProductId) {
                $getProduct     = Product::where('id', '=', $getProductId->id)->first();
                $reviewCount    = UserReview::where('product_id', '=', $getProductId->id)->where('status', '=', 1)->count();
                $reviewSum      = UserReview::where('product_id', '=', $getProductId->id)->where('status', '=', 1)->sum('rating');
                $avgRating      = (($reviewCount > 0) ? ($reviewSum / $reviewCount) : 0);
                $similarProducts[]  = [
                    'id'            => $getProductId->id,
                    'slug'          => (($getProduct) ? $getProduct->slug : ''),
                    'name'          => (($getProduct) ? $getProduct->name : ''),
                    'discounted_price'    => (($getProduct) ? $getProduct->discounted_price : ''),
                    // 'markup_price'  => (($getProduct)?$getProduct->markup_price:''),
                    // 'short_description'  => (($getProduct)?$getProduct->short_description:''),
                    'cover_image'   => (($getProduct) ? $getProduct->cover_image : ''),
                    'review_count'  => $reviewCount,
                    'avg_rating'    => $avgRating,
                ];
            }
        }
        $data['similar_products']       = $similarProducts;
        /* similar products */

        $title                          = 'Product Details';
        $page_name                      = 'product-details';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function getSizeWiseAttributes(Request $request)
    {
        $apiStatus          = TRUE;
        $apiMessage         = '';
        $apiResponse        = [];
        $apiExtraField      = '';
        $apiExtraData       = '';
        $postData           = $request->all();
        $productId          = $postData['productId'];
        $sizeId             = $postData['sizeId'];
        $maincat            = $postData['maincat'];
        $subcat             = $postData['subcat'];
        $child_id_size      = explode("/", $postData['child_id_size']);
        $attrValId          = $child_id_size[1];
        $getProductAttr     = ProductAttribute::select('unit_price')->where('product_id', '=', $productId)->where('product_attribute_id', '=', $sizeId)->where('product_attribute_value_id', '=', $attrValId)->first();
        $getSizeAttrVal     = AttributeValue::select('attr_value')->where('parent_category', '=', $maincat)->where('sub_category_id', '=', $subcat)->where('attr_id', '=', $sizeId)->where('id', '=', $attrValId)->first();
        $attr_value = '';
        if ($getSizeAttrVal) {
            $attr_value             = $getSizeAttrVal->attr_value;
            $getOtherAttrVals       = AttributeValue::select('id', 'attr_id', 'attr_value')->where('parent_category', '=', $maincat)->where('sub_category_id', '=', $subcat)->where('attr_id', '!=', $sizeId)->where('ref_val', '=', $attr_value)->get();
            $attrs                  = [];
            if ($getOtherAttrVals) {
                foreach ($getOtherAttrVals as $getOtherAttrVal) {
                    $attrs[]        = [
                        'id'            => $getOtherAttrVal->id,
                        'attr_id'       => $getOtherAttrVal->attr_id,
                        'attr_value'    => $getOtherAttrVal->attr_value,
                    ];
                }
            }
        }
        $apiResponse = [
            'product_price' => number_format((($getProductAttr) ? $getProductAttr->unit_price : 0.00), 2, '.', ''),
            'attrs'         => $attrs,
        ];
        // Helper::pr($apiResponse);
        $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
    }
    public function makeWishlist($pro_id)
    {
        $uId                                = session('user_id');
        $getUser                            = User::where('id', '=', $uId)->first();
        $product_id                         = Helper::decoded($pro_id);
        $getProduct                         = Product::where('id', '=', $product_id)->first();

        $checkWishlist                      = UserWishlist::where('user_id', '=', $uId)->where('product_id', '=', $product_id)->count();
        if ($checkWishlist) {
            $msg = $getProduct->name . ' Removed From Wishlist Successfully';
            UserWishlist::where('user_id', '=', $uId)->where('product_id', '=', $product_id)->delete();

            /* email functionality */
            $mailData['getProduct']     = $getProduct;
            $mailData['mailHeader']     = $mailData['getProduct']->name . ' removed successfully from wishlist';
            $message                    = view('email-templates.wishlist', $mailData);
            $generalSetting             = GeneralSetting::find('1');
            $subject                    = $generalSetting->site_name . ' [' . $mailData['getProduct']->name . '] removed successfully from wishlist';
            $this->sendMail($getUser->email, $subject, $message);
            /* email functionality */
            /* email log save */
            $postData2 = [
                'name'                  => $getUser->first_name . ' ' . $getUser->last_name,
                'email'                 => $getUser->email,
                'subject'               => $subject,
                'message'               => $message
            ];
            EmailLog::insertGetId($postData2);
            /* email log save */
        } else {
            $msg = $getProduct->name . ' Added Into Wishlist Successfully';
            $fields = [
                'user_id'       => $uId,
                'product_id'    => $product_id,
            ];
            UserWishlist::insert($fields);

            /* email functionality */
            $mailData['getProduct']     = $getProduct;
            $mailData['mailHeader']     = $mailData['getProduct']->name . ' added successfully into wishlist';
            $message                    = view('email-templates.wishlist', $mailData);
            $generalSetting             = GeneralSetting::find('1');
            $subject                    = $generalSetting->site_name . ' [' . $mailData['getProduct']->name . '] added successfully into wishlist';
            $this->sendMail($getUser->email, $subject, $message);
            /* email functionality */
            /* email log save */
            $postData2 = [
                'name'                  => $getUser->first_name . ' ' . $getUser->last_name,
                'email'                 => $getUser->email,
                'subject'               => $subject,
                'message'               => $message
            ];
            EmailLog::insertGetId($postData2);
            /* email log save */
        }

        $currentUrl = url('product/' . (($getProduct) ? $getProduct->slug : '') . '/' . Helper::encoded((($getProduct) ? $getProduct->id : '')));
        return redirect($currentUrl)->with('success_message', $msg);
    }
    /* product details */
    /* add to cart & order place */
    public function addToCart(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData                                   = $request->all();
            $generalSetting                             = GeneralSetting::find('1');

            $tax_percent                                = $generalSetting->tax_percent;
            $domestic_free_shipping_min_amount          = $generalSetting->domestic_free_shipping_min_amount;
            $domestic_shipping_single_item              = $generalSetting->domestic_shipping_single_item;
            $domestic_shipping_multiple_item            = $generalSetting->domestic_shipping_multiple_item;
            $international_shipping_single_item         = $generalSetting->international_shipping_single_item;
            $international_shipping_multiple_item       = $generalSetting->international_shipping_multiple_item;

            $product_id                                 = $postData['product_id'];
            $product_qty                                = $postData['product_qty'];
            $product_rate                               = $postData['product_price'];
            // $attr_id                                    = $postData['attr_id'];
            $variationsArray                            = ((array_key_exists("variations", $postData)) ? $postData['variations'] : []);

            $getProduct         = DB::table('products')
                ->join('categories', 'products.sub_category', '=', 'categories.id')
                ->select('products.*', 'categories.category_name as sub_category_name')
                ->where('products.status', '=', 1)
                ->where('products.id', '=', $product_id)
                ->first();
            if ($getProduct) {
                $generalSetting             = GeneralSetting::find('1');

                $parent_id                  = [];
                $parent_id_val              = [];
                $child_id                   = [];
                $child_id_val               = [];

                $userAgent                  = $request->header('User-Agent', 'unknown');
                $acceptLanguage             = $request->header('Accept-Language', 'en');
                $clientIp                   = $request->ip();
                $deviceId                   = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                $checkProductInCart         = OrderDetail::where('cust_device_id', '=', $deviceId)->where('product_id', '=', $product_id)->where('is_cart', '=', 1)->first();

                /* variation add */
                $attrName       = [];
                $variation_name = '';
                $variation_id   = 0;
                if (!empty($variationsArray)) {
                    for ($v = 0; $v < count($variationsArray); $v++) {
                        $getVariationAttrVal = AttributeValue::select('attr_value')->where('id', '=', $variationsArray[$v])->first();
                        if ($getVariationAttrVal) {
                            $attrName[] = $getVariationAttrVal->attr_value;
                        }
                    }
                    $variationCount = count($variationsArray);
                    $productVariationIds = DB::table('variation_attributes')
                        ->whereIn('attribute_id', $variationsArray) // Match the attribute IDs
                        ->select('product_variation_id')
                        ->groupBy('product_variation_id') // Group by product_variation_id
                        ->havingRaw('COUNT(DISTINCT attribute_id) = ' . $variationCount) // Ensure both attribute_ids exist
                        ->pluck('product_variation_id'); // Get the product_variation_ids
                    // Helper::pr($productVariationIds);
                    $variation_name     = implode(', ', $attrName);
                    $variation_id       = (($productVariationIds) ? $productVariationIds[0] : 0);
                    $product_price      = $product_rate;
                } else {
                    $checkProductVariation = ProductVariation::where('product_id', '=', $product_id)->orderBy('price', 'asc')->first();
                    if ($checkProductVariation) {
                        $getVariationAttrs = VariationAttribute::select('value')->where('product_id', '=', $product_id)->where('product_variation_id', '=', $checkProductVariation->id)->get();
                        if ($getVariationAttrs) {
                            foreach ($getVariationAttrs as $getVariationAttr) {
                                $attrName[] = $getVariationAttr->value;
                            }
                        }
                        $variation_name     = implode(', ', $attrName);
                        $variation_id       = $checkProductVariation->id;
                        $product_price      = $checkProductVariation->price;
                    } else {
                        $variation_name     = '';
                        $variation_id       = 0;
                        $product_price      = $product_rate;
                    }
                }
                /* variation add */
                $total                      = ($product_price * $product_qty);
                $shipping_amt               = 0;
                $tax_amt                    = (($total * $tax_percent) / 100);
                $net_amt                    = ($total + $shipping_amt + $tax_amt);
                if ($checkProductInCart) {
                    $fields = [
                        'parent_id'         => json_encode($parent_id),
                        'parent_id_val'     => json_encode($parent_id_val),
                        'child_id'          => json_encode($child_id),
                        'child_id_val'      => json_encode($child_id_val),
                        'variation_id'      => $variation_id,
                        'variation_name'    => $variation_name,
                        'rate'              => $product_price,
                        'qty'               => $product_qty,
                        'total'             => $total,
                        'subtotal'          => $total,
                        'amount_after_disc' => $total,
                        'shipping_amt'      => $shipping_amt,
                        'tax_amt'           => $tax_amt,
                        'net_amt'           => $net_amt,
                        'is_cart'           => 1,
                    ];
                    // Helper::pr($fields);
                    OrderDetail::where('id', '=', $checkProductInCart->id)->update($fields);
                    $msg = 'Product Successfully Updated Into Cart !!!';
                } else {
                    $fields = [
                        'cust_device_id'    => $deviceId,
                        'product_id'        => $product_id,
                        'parent_id'         => json_encode($parent_id),
                        'parent_id_val'     => json_encode($parent_id_val),
                        'child_id'          => json_encode($child_id),
                        'child_id_val'      => json_encode($child_id_val),
                        'variation_id'      => $variation_id,
                        'variation_name'    => $variation_name,
                        'rate'              => $product_price,
                        'qty'               => $product_qty,
                        'total'             => $total,
                        'subtotal'          => $total,
                        'amount_after_disc' => $total,
                        'shipping_amt'      => $shipping_amt,
                        'tax_amt'           => $tax_amt,
                        'net_amt'           => $net_amt,
                        'is_cart'           => 1,
                    ];
                    // Helper::pr($fields);
                    OrderDetail::insert($fields);
                    $msg = 'Product Successfully Added Into Cart !!!';
                }
                /* view analytics track */
                    $userAgent                      = $request->header('User-Agent', 'unknown');
                    $acceptLanguage                 = $request->header('Accept-Language', 'en');
                    $clientIp                       = $request->ip();
                    $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                    $viewData = [
                        'device_id'     => $deviceId,
                        'page'          => 'add to cart',
                        'product_id'    => 0,
                    ];
                    UserView::insert($viewData);
                /* view analytics track */

                /* update all cart due to multiple items */
                    $country = session('shipping_country');
                    $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');
                    $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
                    
                    if($cartItems){
                        foreach($cartItems as $cartItem){
                            $cart_id = $cartItem->id;
                            $amount_after_disc = $cartItem->amount_after_disc;
                            $product_qty = $cartItem->qty;

                            $country = session('shipping_country');
                            if ($country != '') {
                                if ($country != 'United States') {
                                    if ($cartItemCount > 1) {
                                        $shipping_rate = $international_shipping_multiple_item;
                                    } else {
                                        $shipping_rate = $international_shipping_single_item;
                                    }
                                    $shipping_amt = ($product_qty * $shipping_rate);
                                } else {
                                    if ($cartItemCount > 1) {
                                        $shipping_rate = $domestic_shipping_multiple_item;
                                    } else {
                                        $shipping_rate = $domestic_shipping_single_item;
                                    }
                                    $shipping_amt = ($product_qty * $shipping_rate);
                                }
                            } else {
                                $shipping_amt = 0;
                            }                            

                            $tax_amt    = $cartItem->tax_amt;
                            $net_amt    = ($amount_after_disc + $shipping_amt + $tax_amt);

                            $updateCartData = [
                                'shipping_amt'  => $shipping_amt,
                                'net_amt'       => $net_amt,
                            ];
                            OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                        }

                        // check cart value over 999 or not the in USA free shipping
                            if($country == 'United States'){
                                $cartValue          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('net_amt');
                                // echo $cartValue;die;
                                if($cartValue > $generalSetting->domestic_free_shipping_min_amount){
                                    foreach($cartItems as $cartItem){
                                        $cart_id            = $cartItem->id;
                                        $amount_after_disc  = $cartItem->amount_after_disc;
                                        $product_qty        = $cartItem->qty;
                                        $tax_amt            = $cartItem->tax_amt;
                                        $shipping_amt       = 0;
                                        $net_amt            = ($amount_after_disc + $shipping_amt + $tax_amt);

                                        $updateCartData = [
                                            'shipping_amt'  => $shipping_amt,
                                            'net_amt'       => $net_amt,
                                        ];
                                        OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                                    }
                                }
                            }
                        // check cart value over 999 or not the in USA free shipping
                    }
                /* update all cart due to multiple items */
                return redirect(url('cart'))->with('success_message', $msg);
            } else {
                $msg = 'Product Not Found !!!';
                return redirect(url('cart'))->with('error_message', $msg);
            }
        }
    }
    public function createDeviceFingerprint()
    {
        $userAgent          = $_SERVER['HTTP_USER_AGENT'];
        $acceptLanguage     = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $ipAddress          = $_SERVER['REMOTE_ADDR'];
        $fingerprint        = $userAgent . $acceptLanguage . $ipAddress;
        return md5($fingerprint);
    }
    public function cart(Request $request)
    {
        $deviceId                       = $this->createDeviceFingerprint();
        $data['deviceId']               = $deviceId;
        $data['cartItems']              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
        if ($request->isMethod('post')) {
            $generalSetting                             = GeneralSetting::find('1');
            $domestic_free_shipping_min_amount          = $generalSetting->domestic_free_shipping_min_amount;
            $domestic_shipping_single_item              = $generalSetting->domestic_shipping_single_item;
            $domestic_shipping_multiple_item            = $generalSetting->domestic_shipping_multiple_item;
            $international_shipping_single_item         = $generalSetting->international_shipping_single_item;
            $international_shipping_multiple_item       = $generalSetting->international_shipping_multiple_item;

            $postData       = $request->all();
            if ($postData['mode'] == 'coupon') {
                $coupon_code = $postData['coupon_code'];
                $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');

                if ($coupon_code != '') {
                    $getCoupon = Coupon::where('coupon_code', '=', $coupon_code)->where('status', '!=', 3)->first();
                    if ($getCoupon) {
                        if ($getCoupon->status) {
                            if ($getCoupon->end_date >= date('Y-m-d')) {
                                $discount_type      = $getCoupon->discount_type;
                                $discount_amount    = $getCoupon->discount_amount;
                                $minimum_amount     = $getCoupon->minimum_amount;
                                $category           = $getCoupon->category;
                                $totalCartValue     = OrderDetail::select('subtotal')->where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('subtotal');
                                // Helper::pr($totalCartValue);
                                if ($totalCartValue >= $minimum_amount) {
                                    if ($category <= 0) {
                                        $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
                                        if ($cartItems) {
                                            foreach ($cartItems as $cartItem) {
                                                $product_qty = $cartItem->qty;
                                                $subtotal = $cartItem->subtotal;
                                                if ($discount_type == 'PERCENTAGE') {
                                                    $discAmt = (($subtotal * $discount_amount) / 100);
                                                } else {
                                                    $discAmt = $discount_amount;
                                                }
                                                $amount_after_disc = ($subtotal - $discAmt);
                                                $generalSetting = GeneralSetting::find('1');
                                                $shipping_charge_percent = $generalSetting->shipping_charge_percent;
                                                $tax_percent    = $generalSetting->tax_percent;                                                

                                                $country = session('shipping_country');
                                                if ($country != '') {
                                                    if ($country != 'United States') {
                                                        if ($cartItemCount > 1) {
                                                            $shipping_rate = $international_shipping_multiple_item;
                                                        } else {
                                                            $shipping_rate = $international_shipping_single_item;
                                                        }
                                                        $shipping_amt = ($product_qty * $shipping_rate);
                                                    } else {
                                                        if ($cartItemCount > 1) {
                                                            $shipping_rate = $domestic_shipping_multiple_item;
                                                        } else {
                                                            $shipping_rate = $domestic_shipping_single_item;
                                                        }
                                                        $shipping_amt = ($product_qty * $shipping_rate);
                                                    }
                                                } else {
                                                    $shipping_amt = 0;
                                                }
                                                // shipping amount calculate
                                                $tax_amt        = (($amount_after_disc * $tax_percent) / 100);
                                                $net_amt        = ($amount_after_disc + $shipping_amt + $tax_amt);
                                                $couponData = [
                                                    'coupon_code'       => $coupon_code,
                                                    'disc_type'         => $discount_type,
                                                    'disc_amount'       => $discAmt,
                                                    'amount_after_disc' => $amount_after_disc,
                                                    'shipping_amt'      => $shipping_amt,
                                                    'tax_amt'           => $tax_amt,
                                                    'net_amt'           => $net_amt,
                                                ];
                                                OrderDetail::where('id', '=', $cartItem->id)->update($couponData);
                                            }
                                            $request->session()->put('is_coupon', 1);
                                            $request->session()->put('sess_coupon_code', $coupon_code);
                                            $request->session()->put('sess_disc_type', $discount_type);
                                        }
                                        return redirect(url('cart/'))->with('success_message', 'Coupon Applied Successfully !!!');
                                    } else {
                                        $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
                                        if ($cartItems) {
                                            $p = 0;
                                            foreach ($cartItems as $cartItem) {
                                                $getProduct = Product::where('id', '=', $cartItem->product_id)->first();
                                                if ($getProduct) {
                                                    if ($getProduct->sub_category == $category) {
                                                        $product_qty = $cartItem->qty;
                                                        $subtotal = $cartItem->subtotal;
                                                        if ($discount_type == 'PERCENTAGE') {
                                                            $discAmt = (($subtotal * $discount_amount) / 100);
                                                        } else {
                                                            $discAmt = $discount_amount;
                                                        }
                                                        $amount_after_disc = ($subtotal - $discAmt);
                                                        $generalSetting = GeneralSetting::find('1');
                                                        $shipping_charge_percent = $generalSetting->shipping_charge_percent;
                                                        $tax_percent    = $generalSetting->tax_percent;

                                                        $country = session('shipping_country');
                                                        if ($country != '') {
                                                            if ($country != 'United States') {
                                                                if ($cartItemCount > 1) {
                                                                    $shipping_rate = $international_shipping_multiple_item;
                                                                } else {
                                                                    $shipping_rate = $international_shipping_single_item;
                                                                }
                                                                $shipping_amt = ($product_qty * $shipping_rate);
                                                            } else {
                                                                if ($cartItemCount > 1) {
                                                                    $shipping_rate = $domestic_shipping_multiple_item;
                                                                } else {
                                                                    $shipping_rate = $domestic_shipping_single_item;
                                                                }
                                                                $shipping_amt = ($product_qty * $shipping_rate);
                                                            }
                                                        } else {
                                                            $shipping_amt = 0;
                                                        }
                                                        // shipping amount calculate
                                                        $tax_amt        = (($amount_after_disc * $tax_percent) / 100);
                                                        $net_amt        = ($amount_after_disc + $shipping_amt + $tax_amt);
                                                        $couponData = [
                                                            'coupon_code'       => $coupon_code,
                                                            'disc_type'         => $discount_type,
                                                            'disc_amount'       => $discAmt,
                                                            'amount_after_disc' => $amount_after_disc,
                                                            'shipping_amt'      => $shipping_amt,
                                                            'tax_amt'           => $tax_amt,
                                                            'net_amt'           => $net_amt,
                                                        ];
                                                        OrderDetail::where('id', '=', $cartItem->id)->update($couponData);
                                                        $p++;
                                                    }
                                                }
                                            }
                                            if ($p > 0) {
                                                $request->session()->put('is_coupon', 1);
                                                $request->session()->put('sess_coupon_code', $coupon_code);
                                                $request->session()->put('sess_disc_type', $discount_type);
                                                return redirect(url('cart/'))->with('success_message', 'Coupon Applied Successfully !!!');
                                            } else {
                                                return redirect(url('cart/'))->with('error_message', 'Coupon Code Not Applied Due To Product Category Mismatched !!!');
                                            }
                                        }
                                    }
                                } else {
                                    return redirect(url('cart/'))->with('error_message', 'Cart Value Should Be Minimum ' . $minimum_amount . ' To Apply This Coupon Code !!!');
                                }
                            } else {
                                return redirect(url('cart/'))->with('error_message', 'Coupon Code Expired !!!');
                            }
                        } else {
                            return redirect(url('cart/'))->with('error_message', 'Coupon Code Deactivated !!!');
                        }
                    } else {
                        return redirect(url('cart/'))->with('error_message', 'Coupon Code Not Available !!!');
                    }
                } else {
                    return redirect(url('cart/'))->with('error_message', 'Please Enter Coupon Code !!!');
                }
            }
            if ($postData['mode'] == 'shipping') {
                $s_country = $postData['s_country'];
                $request->session()->put('shipping_country', $s_country);
                $country = session('shipping_country');

                /* update all cart due to multiple items */
                    $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');
                    $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
                    
                    if($cartItems){
                        foreach($cartItems as $cartItem){
                            $cart_id = $cartItem->id;
                            $amount_after_disc = $cartItem->amount_after_disc;
                            $product_qty = $cartItem->qty;

                            if ($country != '') {
                                if ($country != 'United States') {
                                    if ($cartItemCount > 1) {
                                        $shipping_rate = $international_shipping_multiple_item;
                                    } else {
                                        $shipping_rate = $international_shipping_single_item;
                                    }
                                    $shipping_amt = ($product_qty * $shipping_rate);
                                } else {
                                    if ($cartItemCount > 1) {
                                        $shipping_rate = $domestic_shipping_multiple_item;
                                    } else {
                                        $shipping_rate = $domestic_shipping_single_item;
                                    }
                                    $shipping_amt = ($product_qty * $shipping_rate);
                                }
                            } else {
                                $shipping_amt = 0;
                            }

                            $tax_amt    = $cartItem->tax_amt;
                            $net_amt    = ($amount_after_disc + $shipping_amt + $tax_amt);

                            $updateCartData = [
                                'shipping_amt'  => $shipping_amt,
                                'net_amt'       => $net_amt,
                            ];
                            OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                        }

                        // check cart value over 999 or not the in USA free shipping
                            if($country == 'United States'){
                                $cartValue          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('net_amt');
                                // echo $cartValue;die;
                                if($cartValue > $generalSetting->domestic_free_shipping_min_amount){
                                    foreach($cartItems as $cartItem){
                                        $cart_id            = $cartItem->id;
                                        $amount_after_disc  = $cartItem->amount_after_disc;
                                        $product_qty        = $cartItem->qty;
                                        $tax_amt            = $cartItem->tax_amt;
                                        $shipping_amt       = 0;
                                        $net_amt            = ($amount_after_disc + $shipping_amt + $tax_amt);

                                        $updateCartData = [
                                            'shipping_amt'  => $shipping_amt,
                                            'net_amt'       => $net_amt,
                                        ];
                                        OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                                    }
                                }
                            }
                        // check cart value over 999 or not the in USA free shipping
                    }
                    
                /* update all cart due to multiple items */
                return redirect(url('cart'))->with('success_message', 'Shipping price updated successfully');
            }
        }

        $data['countries']              = Country::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $title                          = 'Cart';
        $page_name                      = 'cart';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function removeCoupon(Request $request)
    {
        $deviceId               = $this->createDeviceFingerprint();
        $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();

        $generalSetting                             = GeneralSetting::find('1');
        $domestic_free_shipping_min_amount          = $generalSetting->domestic_free_shipping_min_amount;
        $domestic_shipping_single_item              = $generalSetting->domestic_shipping_single_item;
        $domestic_shipping_multiple_item            = $generalSetting->domestic_shipping_multiple_item;
        $international_shipping_single_item         = $generalSetting->international_shipping_single_item;
        $international_shipping_multiple_item       = $generalSetting->international_shipping_multiple_item;

        $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');
        if ($cartItems) {
            foreach ($cartItems as $cartItem) {
                $product_qty = $cartItem->qty;
                $subtotal   = $cartItem->subtotal;
                $amount_after_disc = $subtotal;
                $generalSetting = GeneralSetting::find('1');
                $shipping_charge_percent = $generalSetting->shipping_charge_percent;
                $tax_percent    = $generalSetting->tax_percent;

                $country = session('shipping_country');
                if ($country != '') {
                    if ($country != 'United States') {
                        if ($cartItemCount > 1) {
                            $shipping_rate = $international_shipping_multiple_item;
                        } else {
                            $shipping_rate = $international_shipping_single_item;
                        }
                        $shipping_amt = ($product_qty * $shipping_rate);
                    } else {
                        if ($cartItemCount > 1) {
                            $shipping_rate = $domestic_shipping_multiple_item;
                        } else {
                            $shipping_rate = $domestic_shipping_single_item;
                        }
                        $shipping_amt = ($product_qty * $shipping_rate);
                    }
                } else {
                    $shipping_amt = 0;
                }
                // shipping amount calculate
                $tax_amt        = (($amount_after_disc * $tax_percent) / 100);
                $net_amt        = ($amount_after_disc + $shipping_amt + $tax_amt);
                $couponData = [
                    'coupon_code'       => '',
                    'disc_type'         => NULL,
                    'disc_amount'       => 0,
                    'amount_after_disc' => $amount_after_disc,
                    'shipping_amt'      => $shipping_amt,
                    'tax_amt'           => $tax_amt,
                    'net_amt'           => $net_amt,
                ];
                OrderDetail::where('id', '=', $cartItem->id)->update($couponData);
            }
            $request->session()->forget(['is_coupon', 'sess_coupon_code', 'sess_disc_type']);
        }
        return redirect(url('cart/'))->with('success_message', 'Coupon Removed Successfully !!!');
    }
    public function cartItemRemove(Request $request, $id)
    {
        $userAgent                  = $request->header('User-Agent', 'unknown');
        $acceptLanguage             = $request->header('Accept-Language', 'en');
        $clientIp                   = $request->ip();
        $deviceId                   = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);

        $generalSetting                             = GeneralSetting::find('1');

        $tax_percent                                = $generalSetting->tax_percent;
        $domestic_free_shipping_min_amount          = $generalSetting->domestic_free_shipping_min_amount;
        $domestic_shipping_single_item              = $generalSetting->domestic_shipping_single_item;
        $domestic_shipping_multiple_item            = $generalSetting->domestic_shipping_multiple_item;
        $international_shipping_single_item         = $generalSetting->international_shipping_single_item;
        $international_shipping_multiple_item       = $generalSetting->international_shipping_multiple_item;        

        $country = session('shipping_country');

        $id = Helper::decoded($id);
        OrderDetail::where('id', '=', $id)->delete();

        /* update all cart due to multiple items */
            $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');
            $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
           
            if($cartItems){
                foreach($cartItems as $cartItem){
                    $cart_id            = $cartItem->id;
                    $amount_after_disc  = $cartItem->amount_after_disc;
                    $product_qty        = $cartItem->qty;

                    if ($country != '') {
                        if ($country != 'United States') {
                            if ($cartItemCount > 1) {
                                $shipping_rate = $international_shipping_multiple_item;
                            } else {
                                $shipping_rate = $international_shipping_single_item;
                            }
                            $shipping_amt = ($product_qty * $shipping_rate);
                        } else {
                            if ($cartItemCount > 1) {
                                $shipping_rate = $domestic_shipping_multiple_item;
                            } else {
                                $shipping_rate = $domestic_shipping_single_item;
                            }
                            $shipping_amt = ($product_qty * $shipping_rate);
                        }
                    } else {
                        $shipping_amt = 0;
                    }

                    $tax_amt = $cartItem->tax_amt;
                    $net_amt    = ($amount_after_disc + $shipping_amt + $tax_amt);

                    $updateCartData = [
                        'shipping_amt'  => $shipping_amt,
                        'net_amt'       => $net_amt,
                    ];
                    OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                }

                // check cart value over 999 or not the in USA free shipping
                    if($country == 'United States'){
                        $cartValue          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('net_amt');
                        // echo $cartValue;die;
                        if($cartValue > $generalSetting->domestic_free_shipping_min_amount){
                            foreach($cartItems as $cartItem){
                                $cart_id            = $cartItem->id;
                                $amount_after_disc  = $cartItem->amount_after_disc;
                                $product_qty        = $cartItem->qty;
                                $tax_amt            = $cartItem->tax_amt;
                                $shipping_amt       = 0;
                                $net_amt            = ($amount_after_disc + $shipping_amt + $tax_amt);

                                $updateCartData = [
                                    'shipping_amt'  => $shipping_amt,
                                    'net_amt'       => $net_amt,
                                ];
                                OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                            }
                        }
                    }
                // check cart value over 999 or not the in USA free shipping
            }
        /* update all cart due to multiple items */
        return redirect(url('cart'))->with('success_message', 'Cart Item Removed Successfully !!!');
    }
    public function updateCartItem(Request $request, $id)
    {
        $postData       = $request->all();
        $id             = Helper::decoded($id);
        $checkProductInCart = OrderDetail::where('id', '=', $id)->first();
        $deviceId                       = $this->createDeviceFingerprint();

        $generalSetting                             = GeneralSetting::find('1');
        $tax_percent                                = $generalSetting->tax_percent;
        $domestic_free_shipping_min_amount          = $generalSetting->domestic_free_shipping_min_amount;
        $domestic_shipping_single_item              = $generalSetting->domestic_shipping_single_item;
        $domestic_shipping_multiple_item            = $generalSetting->domestic_shipping_multiple_item;
        $international_shipping_single_item         = $generalSetting->international_shipping_single_item;
        $international_shipping_multiple_item       = $generalSetting->international_shipping_multiple_item;        

        $shipping_amt               = 0;
        $total                      = ($checkProductInCart->rate * $postData['qty']);
        $tax_amt                    = (($total * $tax_percent) / 100);
        $net_amt                    = ($total + $shipping_amt + $tax_amt);

        $net_amt        = ($total + $shipping_amt + $tax_amt);
        $fields = [
            'qty'               => $postData['qty'],
            'total'             => $total,
            'subtotal'          => $total,
            'amount_after_disc' => $total,
            'shipping_amt'      => $shipping_amt,
            'tax_amt'           => $tax_amt,
            'net_amt'           => $net_amt,
            'is_cart'           => 1,
        ];
        OrderDetail::where('id', '=', $id)->update($fields);

        /* update all cart due to multiple items */
            $cartItemCount          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('qty');
            $cartItems              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
            $country                = session('shipping_country');
            if($cartItems){
                foreach($cartItems as $cartItem){
                    $cart_id = $cartItem->id;
                    $amount_after_disc = $cartItem->amount_after_disc;
                    $product_qty = $cartItem->qty;

                    if ($country != '') {
                        if ($country != 'United States') {
                            if ($cartItemCount > 1) {
                                $shipping_rate = $international_shipping_multiple_item;
                            } else {
                                $shipping_rate = $international_shipping_single_item;
                            }
                            $shipping_amt = ($product_qty * $shipping_rate);
                        } else {
                            if ($cartItemCount > 1) {
                                $shipping_rate = $domestic_shipping_multiple_item;
                            } else {
                                $shipping_rate = $domestic_shipping_single_item;
                            }
                            $shipping_amt = ($product_qty * $shipping_rate);
                        }
                    } else {
                        $shipping_amt = 0;
                    }

                    $tax_amt    = $cartItem->tax_amt;
                    $net_amt    = ($amount_after_disc + $shipping_amt + $tax_amt);

                    $updateCartData = [
                        'shipping_amt'  => $shipping_amt,
                        'net_amt'       => $net_amt,
                    ];
                    OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                }
                // check cart value over 999 or not the in USA free shipping
                    if($country == 'United States'){
                        $cartValue          = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->sum('net_amt');
                        // echo $cartValue;die;
                        if($cartValue > $generalSetting->domestic_free_shipping_min_amount){
                            foreach($cartItems as $cartItem){
                                $cart_id            = $cartItem->id;
                                $amount_after_disc  = $cartItem->amount_after_disc;
                                $product_qty        = $cartItem->qty;
                                $tax_amt            = $cartItem->tax_amt;
                                $shipping_amt       = 0;
                                $net_amt            = ($amount_after_disc + $shipping_amt + $tax_amt);

                                $updateCartData = [
                                    'shipping_amt'  => $shipping_amt,
                                    'net_amt'       => $net_amt,
                                ];
                                OrderDetail::where('id', '=', $cart_id)->update($updateCartData);
                            }
                        }
                    }
                // check cart value over 999 or not the in USA free shipping
            }
            
        /* update all cart due to multiple items */
        return redirect(url('cart'))->with('success_message', 'Cart Item Updated Successfully !!!');
    }
    public function checkout(Request $request)
    {
        $deviceId                       = $this->createDeviceFingerprint();
        $data['deviceId']               = $deviceId;
        $data['cartItems']              = OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->where('status', '=', 0)->get();
        $data['countries']              = Country::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $uId                            = session('user_id');
        $data['getBillingAddrs']        = UserLocation::where('user_id', '=', $uId)->where('type', '=', 'BILLING')->where('status', '=', 1)->get();
        $data['getShippingAddrs']       = UserLocation::where('user_id', '=', $uId)->where('type', '=', 'SHIPPING')->where('status', '=', 1)->get();

        if ($request->isMethod('post')) {
            $postData = $request->all();
            // Helper::pr($postData);die;
            /* add new billing/shipping address */
            if ($postData['mode'] == 'address') {
                $rules = [
                    'title'             => 'required',
                    'address'           => 'required',
                    'country'           => 'required',
                    'state'             => 'required',
                    'city'              => 'required',
                ];
                if ($this->validate($request, $rules)) {
                    $fields = [
                        'user_id'       => $uId,
                        'type'          => $request->type,
                        'title'         => $request->title,
                        'address'       => $request->address,
                        'country'       => $request->country,
                        'state'         => $request->state,
                        'city'          => $request->city,
                        'locality'      => $request->locality,
                        'street_no'     => $request->street_no,
                        'zipcode'       => $request->zipcode,
                        'lat'           => $request->lat,
                        'lng'           => $request->lng,
                    ];
                    // Helper::pr($fields);
                    UserLocation::insert($fields);
                    $type = (($request->type == 'BILLING') ? 'Billing' : 'Shipping');
                    return redirect()->back()->with('success_message', $type . ' Address Added Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            /* add new billing/shipping address */
        }
        $title                          = 'Checkout';
        $page_name                      = 'checkout';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function placeOrder(Request $request, AuthorizeNetService $authorizeNet)
    {
        $deviceId       = $this->createDeviceFingerprint();
        $postData       = $request->all();
        $expiry         = explode('/', $postData['expiry']);
        // Helper::pr($postData);
        /* order place */
        if ($postData['mode'] == 'order') {
            $uId                            = session('user_id');
            $getLastEnquiry                 = Order::orderBy('id', 'DESC')->first();
            if ($getLastEnquiry) {
                $sl_no              = $getLastEnquiry->sl_no;
                $next_sl_no         = $sl_no + 1;
                $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                $order_no           = 'TCG-' . $next_sl_no_string;
            } else {
                $next_sl_no         = 1;
                $next_sl_no_string  = str_pad($next_sl_no, 7, 0, STR_PAD_LEFT);
                $order_no           = 'TCG-' . $next_sl_no_string;
            }
            $payment_method = 'CARD';

            $getShippingAddr = [];
            $getCustomer    = User::where('id', '=', $uId)->first();
            // if (array_key_exists("billing", $postData)) {
            //     $getBillingAddr = UserLocation::where('id', '=', $postData['billing'])->first();
            // } else {
            //     return redirect(url('checkout/'))->with('error_message', 'You Must Select Or Add Billing Address For Checkout !!!');
            // }

            // if (array_key_exists("shipping", $postData)) {
            //     $getShippingAddr = UserLocation::where('id', '=', $postData['shipping'])->first();
            // }

            if($postData['checkout_type'] == 'EXISTING'){
                $getShippingAddr = [];
                $getCustomer    = User::where('id', '=', $uId)->first();
                if (array_key_exists("billing",$postData) && array_key_exists("shipping",$postData)){
                    $getBillingAddr = UserLocation::where('id', '=', $postData['billing'])->first();
                    $getShippingAddr = UserLocation::where('id', '=', $postData['shipping'])->first();
                } else {
                    return redirect(url('checkout/'))->with('error_message', 'You Must Select Or Add Billing or Shipping Address For Checkout');
                }

                $b_fname        = (($getCustomer) ? $getCustomer->first_name : '');
                $b_lname        = (($getCustomer) ? $getCustomer->last_name : '');
                $b_phone        = (($getCustomer) ? $getCustomer->phone : '');
                $b_email        = (($getCustomer) ? $getCustomer->email : '');
                $b_company      = (($getBillingAddr) ? $getBillingAddr->title : '');
                $b_country      = (($getBillingAddr) ? $getBillingAddr->country : '');
                $b_street       = (($getBillingAddr) ? $getBillingAddr->address : '');
                $b_suburb       = (($getBillingAddr) ? $getBillingAddr->city : '');
                $b_state        = (($getBillingAddr) ? $getBillingAddr->state : '');
                $b_postcode     = (($getBillingAddr) ? $getBillingAddr->zipcode : '');

                $s_fname        = (($getCustomer) ? $getCustomer->first_name : (($getCustomer) ? $getCustomer->first_name : ''));
                $s_lname        = (($getCustomer) ? $getCustomer->last_name : (($getCustomer) ? $getCustomer->last_name : ''));
                $s_phone        = (($getCustomer) ? $getCustomer->phone : (($getCustomer) ? $getCustomer->phone : ''));
                $s_email        = (($getCustomer) ? $getCustomer->email : (($getCustomer) ? $getCustomer->email : ''));
                $s_company      = (($getShippingAddr) ? $getShippingAddr->title : (($getBillingAddr) ? $getBillingAddr->title : ''));
                $s_country      = (($getShippingAddr) ? $getShippingAddr->country : (($getBillingAddr) ? $getBillingAddr->country : ''));
                $s_street       = (($getShippingAddr) ? $getShippingAddr->address : (($getBillingAddr) ? $getBillingAddr->address : ''));
                $s_suburb       = (($getShippingAddr) ? $getShippingAddr->city : (($getBillingAddr) ? $getBillingAddr->city : ''));
                $s_state        = (($getShippingAddr) ? $getShippingAddr->state : (($getBillingAddr) ? $getBillingAddr->state : ''));
                $s_postcode     = (($getShippingAddr) ? $getShippingAddr->zipcode : (($getBillingAddr) ? $getBillingAddr->zipcode : ''));
            } else {
                $b_fname        = $postData['b_fname'];
                $b_lname        = $postData['b_lname'];
                $b_phone        = $postData['b_phone'];
                $b_email        = $postData['b_email'];
                $b_company      = $postData['b_company'];
                $b_country      = $postData['b_country'];
                $b_street       = $postData['b_street'];
                $b_suburb       = $postData['b_suburb'];
                $b_state        = $postData['b_state'];
                $b_postcode     = $postData['b_postcode'];
                $s_fname        = $postData['s_fname'];
                $s_lname        = $postData['s_lname'];
                $s_phone        = $postData['s_phone'];
                $s_email        = $postData['s_email'];
                $s_company      = $postData['s_company'];
                $s_country      = $postData['s_country'];
                $s_street       = $postData['s_street'];
                $s_suburb       = $postData['s_suburb'];
                $s_state        = $postData['s_state'];
                $s_postcode     = $postData['s_postcode'];
            }            

            $fields1 = [
                'sl_no'             => $next_sl_no,
                'order_no'          => $order_no,
                'cust_device_id'    => $deviceId,
                'cust_id'           => (($uId != '') ? $uId : 0),
                'cust_fname'        => $b_fname,
                'cust_lname'        => $b_lname,
                'cust_phone'        => $b_phone,
                'cust_email'        => $b_email,
                'order_date'        => date('Y-m-d'),
                'order_time'        => date('H:i:s'),
                'b_fname'           => $b_fname,
                'b_lname'           => $b_lname,
                'b_phone'           => $b_phone,
                'b_email'           => $b_email,
                'b_company'         => $b_company,
                'b_country'         => $b_country,
                'b_street'          => $b_street,
                'b_suburb'          => $b_suburb,
                'b_state'           => $b_state,
                'b_postcode'        => $b_postcode,
                's_fname'           => $s_fname,
                's_lname'           => $s_lname,
                's_phone'           => $s_phone,
                's_email'           => $s_email,
                's_company'         => $s_company,
                's_country'         => $s_country,
                's_street'          => $s_street,
                's_suburb'          => $s_suburb,
                's_state'           => $s_state,
                's_postcode'        => $s_postcode,
                'subtotal'          => $postData['subtotal'],
                'coupon_code'       => session('sess_coupon_code'),
                'disc_type'         => session('sess_disc_type'),
                'disc_amount'       => $postData['disc_amount'],
                'amount_after_disc' => $postData['amount_after_disc'],
                'shipping_amt'      => $postData['shipping_amt'],
                'tax_amt'           => $postData['tax_amt'],
                'net_amt'           => $postData['net_amt'],
                'payment_mode'      => $postData['payment_method'],
                'checkout_type'     => $postData['checkout_type'],
            ];
            // Helper::pr($fields1);die;
            $order_id = Order::insertGetId($fields1);
            if ($order_id) {
                $fields2 = [
                    'order_id'  => $order_id,
                    'cust_id'   => (($uId != '') ? $uId : 0),
                    // 'is_cart'   => 0,
                    // 'status'    => 1,
                ];
                // OrderDetail::where('cust_device_id', '=', $deviceId)->where('order_id', '=', 0)->where('is_cart', '=', 1)->update($fields2);
                OrderDetail::where('cust_device_id', '=', $deviceId)->where('is_cart', '=', 1)->update($fields2);
                if ($postData['payment_method'] == 'STRIPE') {
                    $request->session()->forget(['is_coupon', 'sess_coupon_code', 'sess_disc_type']);
                    return redirect(url('pay-by-card/' . Helper::encoded($order_id)))->with('success_message', 'Kindly Pay To Complete The Order !!!');
                } elseif ($postData['payment_method'] == 'PAYPAL') {
                    return redirect(url('pay-by-paypal/' . Helper::encoded($order_id)))->with('success_message', 'Kindly Pay To Complete The Order !!!');
                }
            }
        }
        /* order place */
        /* authorise.net payment process */
        $getOrder = Order::where('id', '=', $order_id)->first();
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(config('services.authorize.login_id'));
        $merchantAuthentication->setTransactionKey(config('services.authorize.transaction_key'));

        // === Payment Information (from Accept.js opaqueData or raw card for test) ===
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($request->card_number);
        $creditCard->setExpirationDate($expiry[0] . "-" . $expiry[1]);
        $creditCard->setCardCode($request->cvc);

        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // === CUSTOMER BILLING INFORMATION ===
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($this->sanitizeField((($getOrder) ? $getOrder->b_fname : ''), 50));
        $billTo->setLastName($this->sanitizeField((($getOrder) ? $getOrder->b_lname : ''), 50));
        $billTo->setCompany($this->sanitizeField((($getOrder) ? $getOrder->b_company : ''), 50));
        $billTo->setAddress($this->sanitizeField((($getOrder) ? $getOrder->b_street : ''), 60));
        $billTo->setCity($this->sanitizeField((($getOrder) ? $getOrder->b_suburb : ''), 40));
        $billTo->setState($this->sanitizeField((($getOrder) ? $getOrder->b_state : ''), 40));
        $billTo->setZip($this->sanitizeField((($getOrder) ? $getOrder->b_postcode : ''), 20));
        $billTo->setCountry($this->sanitizeField((($getOrder) ? $getOrder->b_country : ''), 60));
        $billTo->setPhoneNumber($this->sanitizeField((($getOrder) ? $getOrder->b_phone : ''), 25));
        $billTo->setFaxNumber($this->sanitizeField((($getOrder) ? $getOrder->b_phone : ''), 25));

        // === CUSTOMER SHIPPING INFORMATION ===
        $shipTo = new AnetAPI\CustomerAddressType();
        $shipTo->setFirstName($this->sanitizeField((($getOrder) ? $getOrder->s_fname : ''), 50));
        $shipTo->setLastName($this->sanitizeField((($getOrder) ? $getOrder->s_lname : ''), 50));
        $shipTo->setCompany($this->sanitizeField((($getOrder) ? $getOrder->s_company : ''), 50));
        $shipTo->setAddress($this->sanitizeField((($getOrder) ? $getOrder->s_street : ''), 60));
        $shipTo->setCity($this->sanitizeField((($getOrder) ? $getOrder->s_suburb : ''), 40));
        $shipTo->setState($this->sanitizeField((($getOrder) ? $getOrder->s_state : ''), 40));
        $shipTo->setZip($this->sanitizeField((($getOrder) ? $getOrder->s_postcode : ''), 20));
        $shipTo->setCountry($this->sanitizeField((($getOrder) ? $getOrder->s_country : ''), 60));
        // $shipTo->setPhoneNumber($this->sanitizeField((($getOrder)?$getOrder->s_phone:''), 25));
        // $shipTo->setFaxNumber($this->sanitizeField((($getOrder)?$getOrder->s_phone:''), 25));

        // === ADDITIONAL INFORMATION ===
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber((($getOrder) ? $getOrder->order_no : ''));
        $order->setDescription((($getOrder) ? $getOrder->id : ''));

        // Taxes, Duty, Freight, PO Number
        $transactionRequest = new AnetAPI\TransactionRequestType();
        $transactionRequest->setTransactionType("authCaptureTransaction"); // charge
        $transactionRequest->setAmount($request->net_amt);
        $transactionRequest->setPayment($paymentOne);
        $transactionRequest->setBillTo($billTo);
        $transactionRequest->setShipTo($shipTo);
        $transactionRequest->setOrder($order);
        $transactionRequest->setTax(new AnetAPI\ExtendedAmountType(['amount' => $request->tax_amt, 'name' => 'Sales Tax']));
        $transactionRequest->setDuty(new AnetAPI\ExtendedAmountType(['amount' => 0.00, 'name' => 'Duty Fee']));
        // $transactionRequest->setFreight(new AnetAPI\ExtendedAmountType(['amount' => 0.00, 'name' => 'Shipping']));
        // $transactionRequest->setTaxExempt(false);
        // $transactionRequest->setPoNumber("");

        // Wrap it inside a CreateTransactionRequest
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setTransactionRequest($transactionRequest);

        // Execute
        $controller = new AnetController\CreateTransactionController($request);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

        // Helper::pr($response);

        // Get Transaction ID
        if ($response != null && $response->getMessages()->getResultCode() == "Ok") {
            $tresponse = $response->getTransactionResponse();
            if ($tresponse != null && $tresponse->getMessages() != null) {
                // echo "Transaction ID: " . $tresponse->getTransId() . "\n";
                $paymentFields = [
                    'payment_status'    => 1,
                    'payment_txn_no'    => $tresponse->getTransId(),
                    'payment_date_time' => date('Y-m-d H:i:s'),
                ];
                Order::where('id', '=', $order_id)->update($paymentFields);

                $getOrder   = DB::table('orders')
                    ->join('users', 'orders.cust_id', '=', 'users.id')
                    ->select('orders.*', 'users.first_name', 'users.last_name', 'users.email')
                    ->where('orders.cust_id', '=', $uId)
                    ->where('orders.id', '=', $order_id)
                    ->first();
                /* generate inspection pdf & save it to directory */
                $enquiry_no                     = (($getOrder) ? $getOrder->order_no : '');
                $data['generalSetting']         = GeneralSetting::find('1');
                $data['getOrderDetail']         = $getOrder;
                $subject                        = $data['generalSetting']->site_name . ' Invoice' . $enquiry_no;
                $message                        = view('email-templates.print-invoice', $data);
                $options    = new Options();
                $options->set('defaultFont', 'Courier');
                $dompdf     = new Dompdf($options);
                $html       = $message;
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                // $dompdf->stream("document.pdf", array("Attachment" => false));die;
                $filename   = $enquiry_no . '.pdf';
                $pdfFilePath = 'public/uploads/orders/' . $filename;
                file_put_contents($pdfFilePath, $output);
                Order::where('id', '=', $order_id)->update(['invoice_pdf' => $filename]);
                /* generate inspection pdf & save it to directory */
                /* email functionality */
                $mailData['getOrder']       = Order::where('id', '=', $order_id)->first();
                $message                    = view('email-templates.order-place', $mailData);
                $generalSetting             = GeneralSetting::find('1');
                $subject                    = 'Order Confirmation - Your Order with ' . $generalSetting->site_name . ' [' . $mailData['getOrder']->order_no . '] has been successfully placed!';
                $this->sendMail($generalSetting->system_email, $subject, $message);
                $this->sendMail((($getOrder) ? $getOrder->cust_email : ''), $subject, $message);
                /* email functionality */
                /* email log save */
                $postData2 = [
                    'name'                  => $mailData['getOrder']->b_fname . ' ' . $mailData['getOrder']->b_lname,
                    'email'                 => $mailData['getOrder']->cust_email,
                    'subject'               => $subject,
                    'message'               => $message
                ];
                EmailLog::insertGetId($postData2);
                /* email log save */
                return redirect(url('order-success/' . Helper::encoded($order_id)))->with('success_message', 'Order placed & payment has been successfully completed !!!');
            } else {
                // echo "Transaction Failed\n";
            }
        }
        /* authorise.net payment process */
    }
    public function sanitizeField($value, $maxLength)
    {
        return substr($value ?? '', 0, $maxLength);
    }
    public function payByCard(Request $request, $id)
    {
        $id                             = Helper::decoded($id);
        $data['getOrder']               = Order::where('id', '=', $id)->first();
        $data['cartItems']              = OrderDetail::where('order_id', '=', $id)->get();
        $generalSetting                 = GeneralSetting::find('1');
        if ($request->isMethod('post')) {
            $postData = $request->all();

            $order_id                       = $request->order_id;
            $net_amt                        = $request->net_amt;
            if ($request->order_id == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Package Required !!!';
            } elseif ($request->cardNo == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Card No Required !!!';
            } elseif ($request->cardHolderName == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Card Holder Name Required !!!';
            } elseif ($request->cardExpiryMM == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Card Expiry Month Required !!!';
            } elseif ($request->cardExpiryYY == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Card Expiry Year Required !!!';
            } elseif ($request->cardCvv == '') {
                $apiStatus = FALSE;
                $apiMessage = 'Card Cvv Required !!!';
            } else {
                $getOrder                       = Order::where('id', '=', $order_id)->first();
                $price                          = $net_amt;
                $postData['cardNo']             = $request->cardNo;
                $postData['cardHolderName']     = $request->cardHolderName;
                $postData['cardExpiryMM']       = $request->cardExpiryMM;
                $postData['cardExpiryYY']       = $request->cardExpiryYY;
                $postData['cardCvv']            = $request->cardCvv;
                $user                           = [
                    'cust_fname' => $getOrder->cust_fname,
                    'cust_lname' => $getOrder->cust_lname,
                    'cust_phone' => $getOrder->cust_phone,
                    'cust_email' => $getOrder->cust_email,
                ];
                $user               = (object)$user;
                $stripeData         = $this->commonStripePayment($user, $postData, (int)$price, 'Payment ' . $price . ' for order place on ' . date('Y-m-d H:i:s') . ' by stripe');
                // Helper::pr($stripeData);
                if ($stripeData['status']) {
                    $userSubscriptionData = [
                        'payment_status'                => $stripeData['status'],
                        'payment_txn_no'                => $stripeData['transaction_id'],
                        'payment_date_time'             => date('Y-m-d H:i:s'),
                        'payment_gateway_id'            => $stripeData['payment_gateway_id'],
                        'customer_id'                   => $stripeData['customer_id'],
                        'customer_card_id'              => $stripeData['customer_card_id'],
                        'currency'                      => $stripeData['currency'],
                        'particulars'                   => $stripeData['particulars'],
                        'card_last_4_digits'            => $stripeData['card_last_4_digits'],
                        'expiry_month'                  => $stripeData['expiry_month'],
                        'expiry_year'                   => $stripeData['expiry_year'],
                    ];
                    Order::where('id', '=', $order_id)->update($userSubscriptionData);
                    OrderDetail::where('order_id', '=', $order_id)->update(['is_cart' => 0]);
                    /* email functionality */
                    $mailData['getOrder']       = Order::where('id', '=', $order_id)->first();
                    $message                    = view('email-templates.order-place', $mailData);
                    $generalSetting             = GeneralSetting::find('1');
                    $subject                    = 'Order Confirmation - Your Order with ' . $generalSetting->site_name . ' [' . $mailData['getOrder']->order_no . '] has been successfully placed!';
                    $this->sendMail($generalSetting->system_email, $subject, $message);
                    /* email functionality */
                    /* email log save */
                    $postData2 = [
                        'name'                  => $mailData['getOrder']->b_fname . ' ' . $mailData['getOrder']->b_lname,
                        'email'                 => $mailData['getOrder']->b_email,
                        'subject'               => $subject,
                        'message'               => $message
                    ];
                    EmailLog::insertGetId($postData2);
                    /* email log save */
                    return redirect(url('order-success/' . Helper::encoded($order_id)))->with('success_message', 'Order Placed & Payment Completed Successfully !!!');
                } else {
                    return redirect(url('order-success/' . Helper::encoded($order_id)))->with('error_message', 'Something Went Wrong In Order Placed !!!');
                }
            }
        }
        $title                          = 'Pay By Card';
        $page_name                      = 'pay-by-card';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function stripeCheckout(Request $request)
    {
        // $id             = Helper::decoded($id);
        $generalSetting = GeneralSetting::where('id', '=', 1)->first();
        $getOrder       = Order::where('id', '=', $request->orderid)->first();
        $stripeSecret   = ($generalSetting->stripe_payment_type == 1) ? $generalSetting->stripe_sandbox_sk : $generalSetting->stripe_live_sk;
        // echo $stripeSecret;die;
        $stripe         = new \Stripe\StripeClient($stripeSecret);
        // $redirectUrl = route('stripe.checkout.success').'?session_id={CHECKOUT_SESSION_ID}';
        // $CHECKOUT_SESSION_ID = CHECKOUT_SESSION_ID;
        $redirectUrl = url('/') . '/stripe/checkout/success/{CHECKOUT_SESSION_ID}';
        $response = $stripe->checkout->sessions->create([
            'success_url' => $redirectUrl,
            'customer_email' => (($getOrder) ? $getOrder->cust_email : ''),
            'payment_method_types' => ['link', 'card'],
            'line_items' => [
                [
                    'price_data' => [
                        'product_data' => [
                            'name' => $request->product,
                        ],
                        'unit_amount' => 100 * $request->price,
                        'currency' => 'USD',
                    ],
                    'quantity' => 1
                ],
            ],
            'custom_fields' => [
                [
                    'key' => 'order_id',
                    'label' => [
                        'type' => 'custom',
                        'custom' => $request->orderid,
                    ],
                    'type' => 'numeric',
                    'optional' => true,
                ],
            ],
            'mode' => 'payment',
            'allow_promotion_codes' => true,
        ]);
        // echo '<pre>';print_r($response);die;
        return redirect($response['url']);
    }
    public function stripeCheckoutSuccess(Request $request, $sessionId)
    {
        $generalSetting = GeneralSetting::where('id', '=', 1)->first();
        $stripeSecret   = ($generalSetting->stripe_payment_type) ? $generalSetting->stripe_sandbox_sk : $generalSetting->stripe_live_sk;
        $session_id     = $sessionId;
        $stripe         = new \Stripe\StripeClient($stripeSecret);
        $response       = $stripe->checkout->sessions->retrieve($session_id);
        $payment_intent = $response->payment_intent;
        $order_id       = $response->custom_fields[0]->label->custom;
        \Stripe\Stripe::setApiKey($stripeSecret);
        // $retrievedPaymentIntent = \Stripe\PaymentIntent::all($payment_intent);
        $retrievedPaymentIntent = $stripe->charges->all(['payment_intent' => $payment_intent])->data[0];
        // Helper::pr($response);
        $stripeData = [
            'status'                => TRUE,
            'payment_gateway_id'    => $retrievedPaymentIntent->payment_intent,
            'transaction_id'        => $retrievedPaymentIntent->balance_transaction,
            'customer_id'           => $retrievedPaymentIntent->customer,
            'customer_card_id'      => $retrievedPaymentIntent->payment_method,
            'currency'              => $retrievedPaymentIntent->currency,
            'particulars'           => $retrievedPaymentIntent->description,
            'amount'                => ($retrievedPaymentIntent->amount / 100),
            // 'card_last_4_digits'    => $retrievedPaymentIntent->payment_method_details->card->last4,
            // 'expiry_month'          => $retrievedPaymentIntent->payment_method_details->card->exp_month,
            // 'expiry_year'           => $retrievedPaymentIntent->payment_method_details->card->exp_year,
        ];
        if ($stripeData['status']) {
            $userSubscriptionData = [
                'payment_status'                => $stripeData['status'],
                'payment_txn_no'                => $stripeData['transaction_id'],
                'payment_date_time'             => date('Y-m-d H:i:s'),
                'payment_gateway_id'            => $stripeData['payment_gateway_id'],
                'customer_id'                   => $stripeData['customer_id'],
                'customer_card_id'              => $stripeData['customer_card_id'],
                'currency'                      => $stripeData['currency'],
                'particulars'                   => $stripeData['particulars'],
                // 'card_last_4_digits'            => $stripeData['card_last_4_digits'],
                // 'expiry_month'                  => $stripeData['expiry_month'],
                // 'expiry_year'                   => $stripeData['expiry_year'],
            ];
            // Helper::pr($stripeData);
            Order::where('id', '=', $order_id)->update($userSubscriptionData);
            OrderDetail::where('order_id', '=', $order_id)->update(['is_cart' => 0]);

            $getOrder   = DB::table('orders')
                ->join('users', 'orders.cust_id', '=', 'users.id')
                ->select('orders.*', 'users.first_name', 'users.last_name', 'users.email')
                ->where('orders.id', '=', $order_id)
                ->first();
            /* generate inspection pdf & save it to directory */
            $enquiry_no                     = (($getOrder) ? $getOrder->order_no : '');
            $data['generalSetting']         = GeneralSetting::find('1');
            $data['getOrderDetail']         = $getOrder;
            $subject                        = $data['generalSetting']->site_name . ' Invoice' . $enquiry_no;
            $message                        = view('email-templates.print-invoice', $data);
            $options    = new Options();
            $options->set('defaultFont', 'Courier');
            $dompdf     = new Dompdf($options);
            $html       = $message;
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $output = $dompdf->output();
            // $dompdf->stream("document.pdf", array("Attachment" => false));die;
            $filename   = $enquiry_no . '.pdf';
            $pdfFilePath = 'public/uploads/orders/' . $filename;
            file_put_contents($pdfFilePath, $output);
            Order::where('id', '=', $order_id)->update(['invoice_pdf' => $filename]);
            /* generate inspection pdf & save it to directory */

            /* email functionality */
            $mailData['getOrder']       = Order::where('id', '=', $order_id)->first();
            $message                    = view('email-templates.order-place', $mailData);
            $generalSetting             = GeneralSetting::find('1');
            $subject                    = 'Order Confirmation - Your Order with ' . $generalSetting->site_name . ' [' . $mailData['getOrder']->order_no . '] has been successfully placed!';
            $this->sendMail($generalSetting->system_email, $subject, $message);
            $this->sendMail($mailData['getOrder']->b_email, $subject, $message);
            /* email functionality */
            /* email log save */
            $postData2 = [
                'name'                  => $mailData['getOrder']->b_fname . ' ' . $mailData['getOrder']->b_lname,
                'email'                 => $mailData['getOrder']->b_email,
                'subject'               => $subject,
                'message'               => $message
            ];
            EmailLog::insertGetId($postData2);
            /* email log save */
            return redirect(url('order-success/' . Helper::encoded($order_id)))->with('success_message', 'Order Placed & Payment Completed Successfully !!!');
        } else {
            return redirect(url('order-success/' . Helper::encoded($order_id)))->with('error_message', 'Something Went Wrong In Order Placed !!!');
        }
        // Helper::pr($filteredresponse);
        // $data['response'] = $filteredresponse;
        // return view('stripe-success', $data);
        // return redirect()->route('stripe.index')
        //                     ->with('success','Payment successful.');
    }
    public function payByPaypal(Request $request, $id)
    {
        $id                             = Helper::decoded($id);
        $data['id']                     = $id;
        $data['getOrder']               = Order::where('id', '=', $id)->first();
        $data['cartItems']              = OrderDetail::where('order_id', '=', $id)->get();
        $generalSetting                 = GeneralSetting::find('1');
        $title                          = 'Pay By Paypal';
        $page_name                      = 'pay-by-paypal';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function orderSuccess($id)
    {
        $id                             = Helper::decoded($id);
        $data['getOrder']               = Order::where('id', '=', $id)->first();
        $data['cartItems']              = OrderDetail::where('order_id', '=', $id)->get();
        $generalSetting                 = GeneralSetting::find('1');
        $title                          = 'Order Success';
        $page_name                      = 'order-success';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function orderFailure($id)
    {
        $id                             = Helper::decoded($id);
        $data['getOrder']               = Order::where('id', '=', $id)->first();
        $data['cartItems']              = OrderDetail::where('order_id', '=', $id)->get();
        $generalSetting                 = GeneralSetting::find('1');
        $title                          = 'Order Failured';
        $page_name                      = 'order-failure';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* add to cart & order place */
    /* faq */
    public function faq()
    {
        $data['faqCats']                = FaqCategory::where('status', '=', 1)->get();
        $title                          = 'Frequently Asked Questions';
        $page_name                      = 'faq';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* faq */
    /* page */
    public function page($slug)
    {
        $data['page']                   = Page::where('slug', '=', $slug)->first();
        $data['cat']                    = $data['page'];
        $title                          = (($data['page']) ? $data['page']->page_title : "Page");
        $page_name                      = 'page-content';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* page */
    /* contact us */
    public function contactUs(Request $request)
    {
        $data                           = [];
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'fname'             => 'required',
                'lname'             => 'required',
                'email'             => 'required',
                'phone'             => 'required',
                'subject'           => 'required',
                'message'           => 'required',
            ];
            if ($this->validate($request, $rules)) {
                $fname              = $postData['fname'];
                $lname              = $postData['lname'];
                $email              = $postData['email'];
                $phone              = $postData['phone'];
                $subject            = $postData['subject'];
                $msg                = $postData['message'];
                $fields = [
                    'name'              => $fname . ' ' . $lname,
                    'email'             => $email,
                    'phone'             => $phone,
                    'subject'           => $subject,
                    'description'       => $msg,
                ];
                // Helper::pr($fields);
                Enquiry::insert($fields);
                /* email sent */
                $generalSetting             = GeneralSetting::find('1');

                $message                    = str_replace("{{name}}", $fname . ' ' . $lname, $generalSetting->email_template_contactus);
                $message1                    = str_replace("{{email}}", $email, $message);
                $message2                    = str_replace("{{phone}}", $phone, $message1);
                $message3                    = str_replace("{{subject}}", $subject, $message2);
                $message4                    = str_replace("{{description}}", $msg, $message3);
                // echo $message4;die;
                $subject                    = $generalSetting->site_name . ' :: Contact Enquiry';
                $this->sendMail($generalSetting->system_email, $subject, $message4);
                /* email sent */
                /* email log save */
                $postData2 = [
                    'name'                  => $fname . ' ' . $lname,
                    'email'                 => $email,
                    'subject'               => $subject,
                    'message'               => $message
                ];
                EmailLog::insertGetId($postData2);
                /* email log save */


                return redirect()->back()->with('success_message', 'You Enquiry Submitted Successfully. We Will Contact You Soon !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $title                          = 'Contact Us';
        $page_name                      = 'contact-us';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* contact us */
    /* authentication */
    public function signin(Request $request, $page_redirect = '')
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'signin_email'     => 'required|email|max:255',
                'signin_password'  => 'required|max:30',
            ];
            if ($this->validate($request, $rules)) {
                if (Auth::guard('web')->attempt(['email' => $postData['signin_email'], 'password' => $postData['signin_password'], 'status' => 1, 'type' => 1])) {
                    // Helper::pr(Auth::guard('web')->user());
                    $sessionData = Auth::guard('web')->user();
                    $request->session()->put('user_id', $sessionData['id']);
                    // $request->session()->put('name', $sessionData['first_name'].' '.$sessionData['last_name']);
                    $request->session()->put('name', $sessionData['display_name']);
                    $request->session()->put('email', $sessionData['email']);
                    // Helper::pr($request->session()->all());die;
                    /* user activity */
                    $activityData = [
                        'user_email'        => $sessionData['email'],
                        'user_name'         => $sessionData['name'],
                        'user_type'         => 'USER',
                        'ip_address'        => $request->ip(),
                        'activity_type'     => 1,
                        'activity_details'  => 'Signin Success !!!',
                        'platform_type'     => 'WEB',
                    ];
                    UserActivity::insert($activityData);
                    /* user activity */
                    $page_redirect             = $postData['page_redirect'];
                    if ($page_redirect == '') {
                        $redirectURL = url('/');
                    } else {
                        $redirectURL = url($page_redirect);
                    }
                    return redirect($redirectURL);
                } else {
                    /* user activity */
                    $activityData = [
                        'user_email'        => $postData['signin_email'],
                        'user_name'         => '',
                        'user_type'         => 'USER',
                        'ip_address'        => $request->ip(),
                        'activity_type'     => 0,
                        'activity_details'  => 'Invalid Email Or Password !!!',
                        'platform_type'     => 'WEB',
                    ];
                    UserActivity::insert($activityData);
                    /* user activity */
                    return redirect()->back()->with('error_message', 'Invalid Email Or Password !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $data['page_redirect']          = Helper::decoded($page_redirect);
        $title                          = 'Sign In / Sign Up';
        $page_name                      = 'signin';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function signout(Request $request)
    {
        $user_email                             = $request->session()->get('email');
        $user_name                              = $request->session()->get('name');
        /* user activity */
        $activityData = [
            'user_email'        => $user_email,
            'user_name'         => $user_name,
            'user_type'         => 'USER',
            'ip_address'        => $request->ip(),
            'activity_type'     => 2,
            'activity_details'  => 'You Are Successfully Logged Out !!!',
            'platform_type'     => 'WEB',
        ];
        // Helper::pr($activityData);
        UserActivity::insert($activityData);
        /* user activity */
        $request->session()->forget(['user_id', 'name', 'email']);
        // Helper::pr(session()->all());die;
        Auth::guard('web')->logout();
        return redirect('signin')->with('success_message', 'You Are Successfully Logged Out !!!');
    }
    public function signup(Request $request)
    {
        if ($request->isMethod('post')) {
            $requestData        = $request->all();
            $validator = Validator::make(
                $request->all(),
                [
                    'first_name'            => 'required',
                    'last_name'             => 'required',
                    'email'                 => 'required|email|unique:users',
                    'phone'                 => 'required|max:15',
                    'password'              => 'required_with:confirm_password|min:8',
                    'confirm_password'      => 'min:8',
                ],
                [],
                [
                    'first_name'            => 'First Name',
                    'last_name'             => 'Last Name',
                    'email'                 => 'Email Address',
                    'phone'                 => 'Phone Number',
                    'password'              => 'Password',
                    'password'              => 'Confirm Password',
                ]
            );
            if ($validator->fails()) {
                return redirect('signin')->withErrors($validator)->withInput();
            }
            $validated = $validator->valid();
            $first_name         = $validated['first_name'];
            $last_name          = $validated['last_name'];
            $email              = $validated['email'];
            $phone              = $validated['phone'];
            // if($this->validate($request, $rules)){
            $checkEmail = User::where('email', '=', $email)->count();
            if ($checkEmail <= 0) {
                $checkPhone = User::where('phone', '=', $request->phone)->count();
                if ($checkPhone <= 0) {
                    if ($request->password == $request->confirm_password) {
                        $remember_token = rand(1000, 9999);
                        $fields = [
                            'type'              => 1,
                            'first_name'        => $first_name,
                            'last_name'         => $last_name,
                            'email'             => $email,
                            'phone'             => $phone,
                            'password'          => Hash::make($request->password),
                            'remember_token'    => $remember_token,
                        ];
                        // Helper::pr($fields,0);
                        /* email sent */
                        $generalSetting              = GeneralSetting::find('1');
                        $message                     = str_replace("{{otp1}}", substr($remember_token, 0, 1), $generalSetting->email_template_forgot_password);
                        $message1                    = str_replace("{{otp2}}", substr($remember_token, 1, 1), $message);
                        $message2                    = str_replace("{{otp3}}", substr($remember_token, 2, 1), $message1);
                        $message3                    = str_replace("{{otp4}}", substr($remember_token, 3, 1), $message2);
                        $subject                     = $generalSetting->site_name . ' :: Verify Signup OTP';
                        $this->sendMail($email, $subject, $message3);
                        /* email sent */
                        /* email log save */
                        $postData2 = [
                            'name'                  => $first_name . ' ' . $last_name,
                            'email'                 => $email,
                            'subject'               => $subject,
                            'message'               => $message3
                        ];
                        EmailLog::insertGetId($postData2);
                        /* email log save */
                        $id = User::insertGetId($fields);
                        return redirect(url('signup-validate-otp/' . Helper::encoded($id)))->with('success_message2', 'Please Verify Your Email With OTP Send To Your Inbox !!!');
                    } else {
                        return redirect()->back()->with('error_message2', 'Password & Confirm Password Mismatched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message2', 'Phone Number Already Exists. Try With Different Phone Number !!!');
                }
            } else {
                return redirect()->back()->with('error_message2', 'Email Already Exists. Try With Different Email !!!');
            }
        }
    }
    public function signupValidateOTP(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'otp1'     => 'required|max:4',
                // 'otp2'     => 'required|max:1',
                // 'otp3'     => 'required|max:1',
                // 'otp4'     => 'required|max:1',
            ];
            if ($this->validate($request, $rules)) {
                $id     = $postData['id'];
                $otp1   = $postData['otp1'];
                // $otp2   = $postData['otp2'];
                // $otp3   = $postData['otp3'];
                // $otp4   = $postData['otp4'];
                // $otp    = ($otp1.$otp2.$otp3.$otp4);
                $otp    = ($otp1);
                $checkUser = User::where('id', '=', $id)->first();
                if ($checkUser) {
                    $remember_token = $checkUser->remember_token;
                    if ($remember_token == $otp) {
                        $postData = [
                            'remember_token'        => '',
                            'email_verified_at'     => date('Y-m-d H:i:s'),
                            'status'                => 1,
                        ];
                        User::where('id', '=', $checkUser->id)->update($postData);
                        return redirect('signin/')->with('success_message', 'OTP Validated. Signup Completed !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'OTP Mismatched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'We Don\'t Recognize You !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $id                             = Helper::decoded($id);
        $data['id']                     = $id;
        $title                          = 'Sign Up Validate OTP';
        $page_name                      = 'signup-validate-otp';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* authentication */
    /* forgot password */
    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'email'     => 'required|email|max:255'
            ];
            if ($this->validate($request, $rules)) {
                $email      = $postData['email'];
                $checkUser  = User::where('email', '=', $email)->first();
                if ($checkUser) {
                    $remember_token = rand(1000, 9999);
                    $postData = [
                        'remember_token'        => $remember_token,
                    ];
                    User::where('id', '=', $checkUser->id)->update($postData);
                    /* email sent */
                    $generalSetting              = GeneralSetting::find('1');
                    $message                     = str_replace("{{otp1}}", substr($remember_token, 0, 1), $generalSetting->email_template_forgot_password);
                    $message1                    = str_replace("{{otp2}}", substr($remember_token, 1, 1), $message);
                    $message2                    = str_replace("{{otp3}}", substr($remember_token, 2, 1), $message1);
                    $message3                    = str_replace("{{otp4}}", substr($remember_token, 3, 1), $message2);
                    $subject                     = $generalSetting->site_name . ' :: Forgot Password OTP';
                    $this->sendMail($checkUser->email, $subject, $message3);
                    /* email sent */
                    /* email log save */
                    $postData2 = [
                        'name'                  => $checkUser->first_name . ' ' . $checkUser->last_name,
                        'email'                 => $checkUser->email,
                        'subject'               => $subject,
                        'message'               => $message3
                    ];
                    EmailLog::insertGetId($postData2);
                    /* email log save */
                    return redirect('validate-otp/' . Helper::encoded($checkUser->id))->with('success_message', 'OTP Is Send To Your Registered Email !!!');
                } else {
                    return redirect()->back()->with('error_message', 'You Are Not Registered With Us !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $data                           = [];
        $title                          = 'Forgot Password';
        $page_name                      = 'forgot-password';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function validateOTP(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'otp1'     => 'required|max:4',
                // 'otp2'     => 'required|max:1',
                // 'otp3'     => 'required|max:1',
                // 'otp4'     => 'required|max:1',
            ];
            if ($this->validate($request, $rules)) {
                $id     = $postData['id'];
                $otp1   = $postData['otp1'];
                // $otp2   = $postData['otp2'];
                // $otp3   = $postData['otp3'];
                // $otp4   = $postData['otp4'];
                $otp    = ($otp1);
                $checkUser = User::where('id', '=', $id)->first();
                if ($checkUser) {
                    $remember_token = $checkUser->remember_token;
                    if ($remember_token == $otp) {
                        $postData = [
                            'remember_token'        => '',
                        ];
                        User::where('id', '=', $checkUser->id)->update($postData);
                        return redirect('reset-password/' . Helper::encoded($checkUser->id))->with('success_message', 'OTP Validated. Now Reset Your Password !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'OTP Mismatched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'We Don\'t Recognize You !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $id                             = Helper::decoded($id);
        $data['id']                     = $id;
        $title                          = 'Validate OTP';
        $page_name                      = 'validate-otp';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    public function resetPassword(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $rules = [
                'password'              => 'required',
                'confirm_password'      => 'required'
            ];
            if ($this->validate($request, $rules)) {
                $id                 = $postData['id'];
                $password           = $postData['password'];
                $confirm_password   = $postData['confirm_password'];
                $checkUser = User::where('id', '=', $id)->first();
                if ($checkUser) {
                    if ($password == $confirm_password) {
                        $postData = [
                            'password'        => Hash::make($password),
                        ];
                        User::where('id', '=', $checkUser->id)->update($postData);
                        /* email sent */
                        $generalSetting              = GeneralSetting::find('1');
                        $message                     = str_replace("{{name}}", $checkUser->first_name . ' ' . $checkUser->last_name, $generalSetting->email_template_change_password);
                        $message1                    = str_replace("{{email}}", $checkUser->email, $message);
                        $subject                     = $generalSetting->site_name . ' :: Reset Password';
                        $this->sendMail($checkUser->email, $subject, $message1);
                        /* email sent */
                        /* email log save */
                        $postData2 = [
                            'name'                  => $checkUser->first_name . ' ' . $checkUser->last_name,
                            'email'                 => $checkUser->email,
                            'subject'               => $subject,
                            'message'               => $message1
                        ];
                        EmailLog::insertGetId($postData2);
                        /* email log save */
                        return redirect('signin/')->with('success_message', 'Password Reset Successfully. Please Sign In !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'Password & Confirm Password Does Not Matched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'We Don\'t Recognize You !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        $id                             = Helper::decoded($id);
        $data['id']                     = $id;
        $title                          = 'Reset Password';
        $page_name                      = 'reset-password';
        echo $this->front_before_login_layout($title, $page_name, $data);
    }
    /* forgot password */
    /* dashboard */
    public function dashboard(Request $request)
    {
        $user_id                        = session('user_id');
        $data['wishlist_count']         = UserWishlist::where('user_id', '=', $user_id)->count();
        $data['order_count']            = Order::where('cust_id', '=', $user_id)->count();
        $data['orderList']              = Order::where('cust_id', '=', $user_id)->orderBy('id', 'DESC')->limit(5)->get();
        $title                          = 'Dashboard';
        $page_name                      = 'dashboard';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    /* dashboard */
    /* Update profile */
    public function account(Request $request)
    {
        $uId                            = session('user_id');
        $data['getUser']                = User::where('id', '=', $uId)->where('status', '=', 1)->first();
        if ($request->isMethod('post')) {
            $postData = $request->all();
            if ($postData['mode'] = 'profile') {
                $rules = [
                    'first_name'            => 'required',
                    'last_name'             => 'required',
                    'phone'                 => 'required',
                    'display_name'          => 'required'
                ];
                if ($this->validate($request, $rules)) {
                    $first_name             = $postData['first_name'];
                    $last_name              = $postData['last_name'];
                    $display_name           = $postData['display_name'];
                    $phone                  = $postData['phone'];
                    $checkPhone             = User::where('phone', '=', $phone)->where('id', '!=', $uId)->count();
                    if ($checkPhone <= 0) {
                        /* profile image */
                        $imageFile      = $request->file('profile_image');
                        if ($imageFile != '') {
                            $imageName      = $imageFile->getClientOriginalName();
                            $uploadedFile   = $this->upload_single_file('profile_image', $imageName, 'user', 'image');
                            if ($uploadedFile['status']) {
                                $image = $uploadedFile['newFilename'];
                            } else {
                                return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                            }
                        } else {
                            $image = $data['getUser']->profile_image;
                        }
                        // $dataImage      = $postData['dataImage'];
                        // $image_array_1  = explode(";", $dataImage);
                        // $image_array_2  = explode(",", $image_array_1[1]);
                        // $data           = base64_decode($image_array_2[1]);
                        // $imageName      = time() . '.png';
                        // $file           = 'public/uploads/user/' . $imageName;
                        // file_put_contents($file, $data);
                        // file_put_contents($imageName, $data);
                        /* profile image */
                        $fields = [
                            'first_name'            => $first_name,
                            'last_name'             => $last_name,
                            'display_name'          => $display_name,
                            'phone'                 => $phone,
                            'profile_image'         => $image,
                        ];
                        // Helper::pr($fields);
                        User::where('id', '=', $uId)->update($fields);
                        return redirect()->back()->with('success_message', 'Account Details Updated Successfully !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'Phone Number Already Exists !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
        }
        $title                          = 'Account Details';
        $page_name                      = 'account';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    /* Update profile */
    /* Change Password */
    public function changePassword(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            // Helper::pr($postData);
            $rules = [
                'old_password'          => 'required|min:8|max:15',
                'new_password'          => 'required|min:8|max:15',
                'confirm_password'      => 'required|min:8|max:15',
            ];
            if ($this->validate($request, $rules)) {
                $uId                = $postData['id'];
                $getUser            = User::where('id', '=', $uId)->where('status', '=', 1)->first();
                $old_password       = $postData['old_password'];
                $new_password       = $postData['new_password'];
                $confirm_password   = $postData['confirm_password'];
                if (Hash::check($old_password, $getUser->password)) {
                    if (!Hash::check($new_password, $getUser->password)) {
                        if ($new_password == $confirm_password) {
                            $fields = [
                                'password'            => Hash::make($new_password)
                            ];
                            User::where('id', '=', $uId)->update($fields);
                            return redirect()->back()->with('success_message', 'Password Changed Successfully !!!');
                        } else {
                            return redirect()->back()->with('error_message', 'New & Confirm Password Does Not Matched !!!');
                        }
                    } else {
                        return redirect()->back()->with('error_message', 'Existing & New Password Will Be Different !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'Current Password Is Incorrect !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
    }
    /* Change Password */
    /* Shipping & billing address */
    public function addresses(Request $request, $redirectLink = '')
    {
        $uId                                    = session('user_id');
        $redirectLink                           = Helper::decoded($redirectLink);
        $data['getBillingAddrs']                = UserLocation::where('user_id', '=', $uId)->where('type', '=', 'BILLING')->where('status', '=', 1)->get();
        $data['getShippingAddrs']               = UserLocation::where('user_id', '=', $uId)->where('type', '=', 'SHIPPING')->where('status', '=', 1)->get();
        if ($request->isMethod('post')) {
            $postData = $request->all();
            if ($postData['mode'] == 'address') {
                $rules = [
                    'title'             => 'required',
                    'address'           => 'required',
                    'country'           => 'required',
                    'state'             => 'required',
                    'city'              => 'required',
                ];
                if ($this->validate($request, $rules)) {
                    $fields = [
                        'user_id'       => $uId,
                        'type'          => $request->type,
                        'title'         => $request->title,
                        'address'       => $request->address,
                        'country'       => $request->country,
                        'state'         => $request->state,
                        'city'          => $request->city,
                        'locality'      => $request->locality,
                        'street_no'     => $request->street_no,
                        'zipcode'       => $request->zipcode,
                        'lat'           => $request->lat,
                        'lng'           => $request->lng,
                    ];
                    // Helper::pr($fields);
                    UserLocation::insert($fields);
                    $type = (($request->type == 'BILLING') ? 'Billing' : 'Shipping');
                    if ($redirectLink == '') {
                        return redirect()->back()->with('success_message', $type . ' Address Added Successfully !!!');
                    } else {
                        return redirect($redirectLink)->with('success_message', $type . ' Address Added Successfully !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
        }
        $title                          = 'Addresses';
        $page_name                      = 'addresses';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    /* Shipping & billing address */
    /* address delete */
    public function addressesDelete(Request $request, $id)
    {
        $id                             = Helper::decoded($id);
        UserLocation::where('id', '=', $id)->delete();
        return redirect("user/addresses")->with('success_message', 'Address Deleted Successfully !!!');
    }
    /* address delete */
    /* order list & order details */
    public function orderList(Request $request)
    {
        $uId                            = session('user_id');
        $data['getCustOrders1']         = Order::where('cust_id', '=', $uId)->where('status', '=', 1)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders2']         = Order::where('cust_id', '=', $uId)->where('status', '=', 2)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders3']         = Order::where('cust_id', '=', $uId)->where('status', '=', 3)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders4']         = Order::where('cust_id', '=', $uId)->where('status', '=', 4)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders5']         = Order::where('cust_id', '=', $uId)->where('status', '=', 5)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders6']         = Order::where('cust_id', '=', $uId)->where('status', '=', 6)->where('is_cancel_request', '=', 0)->orderBy('id', 'DESC')->get();
        $data['getCustOrders7']         = Order::where('cust_id', '=', $uId)->where('is_cancel_request', '=', 1)->orderBy('id', 'DESC')->get();
        $data['cancelOrderReasons']     = CancelOrderReason::select('id', 'name')->where('status', '=', 1)->get();

        $title                          = 'Order List';
        $page_name                      = 'order-list';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    public function orderDetails(Request $request, $id)
    {
        $uId                            = session('user_id');
        $id                             = Helper::decoded($id);
        $data['getOrderDetail']         = Order::where('id', '=', $id)->first();
        $data['cancelOrderReasons']     = CancelOrderReason::select('id', 'name')->where('status', '=', 1)->get();
        if ($request->isMethod('post')) {
            $postData   = $request->all();
            $order_id   = $postData['order_id'];
            $page_name  = Helper::decoded($postData['page_name']);
            $fields     = [
                'cancel_order_reason'       => $postData['cancel_order_reason'],
                'cancel_order_description'  => $postData['cancel_order_description'],
                'is_cancel_request'         => 1,
                'cancel_request_timestamp'  => date('Y-m-d H:i:s'),
            ];
            Order::where('id', '=', $order_id)->update($fields);
            return redirect($page_name)->with('success_message', 'Order Cancelled Request Submitted Successfully !!!');
        }

        $title                          = 'Order Details';
        $page_name                      = 'order-details';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    public function cancelOrder(Request $request, $id, $pageName)
    {
        $id                             = Helper::decoded($id);
        $pageName                       = Helper::decoded($pageName);
        Order::where('id', '=', $id)->update(['status' => 7]);
        return redirect($pageName)->with('success_message', 'Order Cancelled Request Submitted Successfully !!!');
    }
    public function printInvoice(Request $request, $id)
    {
        $uId                            = session('user_id');
        $id                             = Helper::decoded($id);
        $data['getOrderDetail']         = Order::where('id', '=', $id)->first();

        $page_name                      = 'print-invoice';
        return view('front.pages.user.' . $page_name, $data);
    }
    /* order list & order details */
    /* user wishlist */
    public function wishlist(Request $request)
    {
        $uId                            = session('user_id');
        $data['wishlistItems']          = UserWishlist::where('user_id', '=', $uId)->orderBy('id', 'DESC')->get();

        $title                          = 'Wishlist';
        $page_name                      = 'wishlist';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    public function wishlistProductDelete(Request $request, $id)
    {
        $id                             = Helper::decoded($id);
        UserWishlist::where('id', '=', $id)->delete();
        return redirect("user/wishlist")->with('success_message', 'Wishlist Product Deleted Successfully !!!');
    }
    /* user wishlist */
    /* user reviews */
    public function reviews(Request $request)
    {
        $uId                            = session('user_id');
        $data['reviewItems']          = UserReview::where('user_id', '=', $uId)->orderBy('id', 'DESC')->get();

        $title                          = 'Reviews';
        $page_name                      = 'reviews';
        echo $this->front_after_login_layout($title, $page_name, $data);
    }
    /* user reviews */
    /* Common Stripe Payment */
    private function commonStripePayment($user, $postData, $price, $msg = '')
    {
        $generalSetting = GeneralSetting::where('id', '=', 1)->first();
        Helper::pr($generalSetting);
        // $stripeSecret   = ($generalSetting->stripe_payment_type == 1) ? $generalSetting->stripe_sandbox_sk : $generalSetting->stripe_live_sk;
        $stripeSecret   = $generalSetting->stripe_live_sk;
        echo $stripeSecret;die;
        $stripe         = new \Stripe\StripeClient($stripeSecret);
        try {
            $stripeToken    = $stripe->tokens->create([
                'card' => [
                    'number'    => $postData['cardNo'],
                    'exp_month' => $postData['cardExpiryMM'],
                    'exp_year'  => $postData['cardExpiryYY'],
                    'cvc'       => $postData['cardCvv'],
                    'name'      => $postData['cardHolderName'],
                ],
            ]);
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            //exit('Error: ' . $e->getMessage());
            $return = array(
                'status'    => FALSE,
                'message'   => 'Something Went Wrong !!! Please Try Again !!!',
            );
        }
        try {
            $customer       = $stripe->customers->create([
                'email'     => $user->cust_email,
                'name'      => (!empty($user->cust_fname) && !empty($user->cust_lname)) ? $user->cust_fname . ' ' . $user->cust_lname : 'New User',
                'address'   => [
                    'line1'         => 'Demo Address',
                    'postal_code'   => '2000',
                    'city'          => 'Sydney',
                    'state'         => 'NSW',
                    'country'       => 'AU',
                ],
                'source'    => $stripeToken
            ]);
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            $return = array(
                'status'    => FALSE,
                'message'   => 'Something Went Wrong !!! Please Try Again !!!',
            );
        }
        try {
            $charge = $stripe->charges->create([
                'amount'        => $price,
                'currency'      => 'usd',
                'description'   => $msg,
                'customer'      => $customer->id,
                //'metadata'      => $msg
            ]);
        } catch (\Stripe\Exception\OAuth\OAuthErrorException $e) {
            $return = array(
                'status'    => FALSE,
                'message'   => 'Something Went Wrong !!! Please Try Again !!!',
            );
        }
        if (isset($charge->status)) :
            if ($charge->status == 'succeeded') :
                $return = array(
                    'status'                => TRUE,
                    'payment_gateway_id'    => $charge->id,
                    'transaction_id'        => $charge->balance_transaction,
                    'customer_id'           => $charge->customer,
                    'customer_card_id'      => $charge->payment_method,
                    'currency'              => $charge->currency,
                    'particulars'           => $charge->description,
                    'card_last_4_digits'    => $charge->payment_method_details->card->last4,
                    'expiry_month'          => $charge->payment_method_details->card->exp_month,
                    'expiry_year'           => $charge->payment_method_details->card->exp_year,
                );
            // Helper::pr($return);
            else :
                $return = array(
                    'status'    => FALSE,
                    'message'   => 'Payment Failed !!! Please Try Again !!!',
                );
            endif;
        else :
            $return = array(
                'status'    => FALSE,
                'message'   => 'Something Went Wrong !!! Please Try Again !!!',
            );
        endif;
        return $return;
    }
    /* Common Stripe Payment */
    public function show($slug, ProductSchemaService $schemaService)
    {
        $product = Product::with(['brand', 'images'])->where('slug', $slug)->firstOrFail();

        $schema = $schemaService->generate($product);

        return view('frontend.product.show', compact('product', 'schema'));
    }
}
