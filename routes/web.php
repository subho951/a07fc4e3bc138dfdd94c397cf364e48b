<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\PayPalController;
use App\Models\Product;
use App\Services\Schema\ProductSchemaService;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

/* Admin Panel */
    Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function(){
        Route::match(['get', 'post'], '/', 'UserController@login');
        Route::match(['get','post'],'/forgot-password', 'UserController@forgotPassword');
        Route::match(['get','post'],'/validateOtp/{id}', 'UserController@validateOtp');
        Route::match(['get','post'],'/changePassword/{id}', 'UserController@changePassword');
        Route::group(['middleware' => ['admin']], function(){
            Route::get('dashboard', 'UserController@dashboard');
            Route::get('dashboard-filter', 'UserController@dashboardFilter');
            Route::get('logout', 'UserController@logout');
            Route::get('email-logs', 'UserController@emailLogs');
            Route::match(['get','post'],'/email-logs/details/{id}', 'UserController@emailLogsDetails');
            Route::get('login-logs', 'UserController@loginLogs');
            // Route::match(['get','post'], 'update-product-view', 'UserController@update_product_view')->name('updateProductView');;
            Route::match(['get', 'post'], 'image-gallery', 'UserController@imageGallery');
            Route::get('dashboard-new', 'UserController@dashboardNew');
            Route::get('stats', 'UserController@stats');
            Route::get('message', 'UserController@message');
            Route::get('user-all-activity', 'UserController@userAllActivity');
            /* setting */
                Route::get('settings', 'UserController@settings');
                Route::post('profile-settings', 'UserController@profile_settings');
                Route::post('general-settings', 'UserController@general_settings');
                Route::post('change-password', 'UserController@change_password');
                Route::post('email-settings', 'UserController@email_settings');
                Route::post('email-template', 'UserController@email_template');
                Route::post('sms-settings', 'UserController@sms_settings');
                Route::post('footer-settings', 'UserController@footer_settings');
                Route::post('seo-settings', 'UserController@seo_settings');
                Route::post('payment-settings', 'UserController@payment_settings');
                Route::post('shipping-settings', 'UserController@shipping_settings');
          		Route::get('test-email', 'UserController@testEmail');
            /* setting */
            /* privileges category */
                Route::get('categories/list', 'CategoryController@list');
                Route::match(['get', 'post'], 'categories/add', 'CategoryController@add');
                Route::match(['get', 'post'], 'categories/edit/{id}', 'CategoryController@edit');
                Route::get('categories/delete/{id}', 'CategoryController@delete');
                Route::get('categories/change-status/{id}', 'CategoryController@change_status');
            /* privileges category */
            /* privileges */
                Route::get('privileges/list', 'PrivilegeController@list');
                Route::match(['get', 'post'], 'privileges/add', 'PrivilegeController@add');
                Route::match(['get', 'post'], 'privileges/edit/{id}', 'PrivilegeController@edit');
                Route::get('privileges/delete/{id}', 'PrivilegeController@delete');
                Route::get('privileges/change-status/{id}', 'PrivilegeController@change_status');
            /* privileges */
            /* committee member */
                Route::get('committee-member/list', 'CommitteeMemberController@list');
                Route::match(['get', 'post'], 'committee-member/add', 'CommitteeMemberController@add');
                Route::match(['get', 'post'], 'committee-member/edit/{id}', 'CommitteeMemberController@edit');
                Route::get('committee-member/delete/{id}', 'CommitteeMemberController@delete');
                Route::get('committee-member/change-status/{id}', 'CommitteeMemberController@change_status');
            /* committee member */
            /* member */
                Route::get('member/list', 'MemberController@list');
                Route::match(['get', 'post'], 'member/add', 'MemberController@add');
                Route::match(['get', 'post'], 'member/edit/{id}', 'MemberController@edit');
                Route::get('member/delete/{id}', 'MemberController@delete');
                Route::get('member/change-status/{id}', 'MemberController@change_status');
                Route::get('member/points-history/{id}', 'MemberController@points_history');
            /* member */
            /* page */
                Route::get('page/list', 'PageController@list');
                Route::match(['get', 'post'], 'page/add', 'PageController@add');
                Route::match(['get', 'post'], 'page/edit/{id}', 'PageController@edit');
                Route::get('page/delete/{id}', 'PageController@delete');
                Route::get('page/change-status/{id}', 'PageController@change_status');
            /* page */

            /* core */
                Route::get('core/list', 'CoreController@list');
                Route::match(['get', 'post'], 'core/add', 'CoreController@add');
                Route::match(['get', 'post'], 'core/edit/{id}', 'CoreController@edit');
                Route::get('core/delete/{id}', 'CoreController@delete');
                Route::get('core/change-status/{id}', 'CoreController@change_status');
            /* core */
            /* magazine */
                Route::get('magazine/list', 'MagazineController@list');
                Route::match(['get', 'post'], 'magazine/add', 'MagazineController@add');
                Route::match(['get', 'post'], 'magazine/edit/{id}', 'MagazineController@edit');
                Route::get('magazine/delete/{id}', 'MagazineController@delete');
                Route::get('magazine/change-status/{id}', 'MagazineController@change_status');
            /* magazine */
            /* achievement */
                Route::get('achievement/list', 'AchievementController@list');
                Route::match(['get', 'post'], 'achievement/add', 'AchievementController@add');
                Route::match(['get', 'post'], 'achievement/edit/{id}', 'AchievementController@edit');
                Route::get('achievement/delete/{id}', 'AchievementController@delete');
                Route::get('achievement/change-status/{id}', 'AchievementController@change_status');
            /* achievement */
            /* event */
                Route::get('event/list', 'EventController@list');
                Route::match(['get', 'post'], 'event/add', 'EventController@add');
                Route::match(['get', 'post'], 'event/edit/{id}', 'EventController@edit');
                Route::get('event/delete/{id}', 'EventController@delete');
                Route::get('event/change-status/{id}', 'EventController@change_status');
            /* event */
            /* media */
                Route::get('media/institute-list', 'MediaController@list');
                Route::get('media/category-list/{id}', 'MediaController@categoryList');
                Route::get('media/media-list/{id}/{id2}', 'MediaController@mediaList');
                Route::post('media/media-list/{id}/{id2}', 'MediaController@mediaList');
                Route::get('media/delete/{id}', 'MediaController@delete');

                Route::match(['get', 'post'], 'media/add', 'MediaController@add');
                Route::match(['get', 'post'], 'media/edit/{id}', 'MediaController@edit');
                Route::get('media/delete/{id}', 'MediaController@delete');
                Route::get('media/change-status/{id}', 'MediaController@change_status');
            /* media */
        });
    });
/* Admin Panel */
/* Api */
    Route::prefix('api')->namespace('App\Http\Controllers')->group(function(){
        // Other Version 2 routes
        /* before login */
            Route::match(['get'], '/get-app-setting', 'ApiController@getAppSetting');
            Route::match(['post'], '/get-static-pages', 'ApiController@getStaticPages');
            Route::match(['post'], '/signup', 'ApiController@signup');
            Route::match(['post'], '/signup-validate', 'ApiController@signupValidate');
            Route::match(['post'], '/signin', 'ApiController@signin');
            Route::match(['post'], '/forgot-password', 'ApiController@forgotPassword');
            Route::match(['post'], '/validate-otp', 'ApiController@validateOtp');
            Route::match(['post'], '/resend-otp', 'ApiController@resendOtp');
            Route::match(['post'], '/reset-password', 'ApiController@resetPassword');
            Route::match(['get'], '/get-home', 'ApiController@getHome');
            Route::match(['get'], '/faq', 'ApiController@faq');
            Route::match(['post'], '/contact-us', 'ApiController@contactUs');
            Route::match(['post'], '/submit-subscriber', 'ApiController@submitSubscriber');
            Route::match(['get'], '/get-parent-category', 'ApiController@getParentCategory');
            Route::match(['post'], '/get-child-category', 'ApiController@getChildCategory');
            Route::match(['post'], '/get-product-list-by-parent-category', 'ApiController@getProductListByParentCategory');
            Route::match(['get'], '/get-all-product-list', 'ApiController@getAllProductList');
            Route::match(['post'], '/product-filter', 'ApiController@productFilter');
            Route::match(['post'], '/product-details', 'ApiController@productDetails');
            Route::match(['post'], '/select-variation', 'ApiController@selectVariation');
            Route::match(['post'], '/add-cart', 'ApiController@addCart');
            Route::match(['get'], '/get-cart', 'ApiController@getCart');
            Route::match(['post'], '/cart-item-remove', 'ApiController@cartItemRemove');
            Route::match(['post'], '/update-cart-item', 'ApiController@updateCartItem');
            Route::match(['post'], '/search-product', 'ApiController@searchProduct');
            Route::match(['post'], '/search-suggestion', 'ApiController@searchSuggestion');
            Route::match(['post'], '/apply-coupon', 'ApiController@applyCoupon');
            Route::match(['get'], '/remove-coupon', 'ApiController@removeCoupon');
            Route::match(['post'], '/payment-process', 'ApiController@paymentProcess');
        /* before login */
        /* after login */
            Route::match(['get'], '/signout', 'ApiController@signout');
            Route::match(['get'], '/dashboard', 'ApiController@dashboard');
            Route::match(['post'], '/change-password', 'ApiController@changePassword');
            Route::match(['get'], '/get-profile', 'ApiController@getProfile');
            Route::match(['get'], '/edit-profile', 'ApiController@editProfile');
            Route::match(['post'], '/update-profile', 'ApiController@updateProfile');
            Route::match(['post'], '/upload-profile-image', 'ApiController@uploadProfileImage');
            Route::match(['get'], '/get-address', 'ApiController@getAddress');
            Route::match(['post'], '/add-address', 'ApiController@addAddress');
            Route::match(['post'], '/delete-address', 'ApiController@deleteAddress');
            Route::match(['get'], '/get-reviews', 'ApiController@getReview');
            Route::match(['get'], '/get-wishlist', 'ApiController@getWishlist');
            Route::match(['post'], '/delete-wishlist', 'ApiController@deleteWishlist');
            Route::match(['post'], '/add-wishlist', 'ApiController@addWishlist');
            Route::match(['post'], '/add-review', 'ApiController@addReview');
            Route::match(['get'], '/checkout', 'ApiController@checkout');
            Route::match(['post'], '/place-order', 'ApiController@placeOrder');
            Route::match(['get'], '/order-list', 'ApiController@orderList');
            Route::match(['post'], '/order-details', 'ApiController@orderDetails');
            Route::match(['post'], '/print-invoice', 'ApiController@printInvoice');
            Route::match(['get'], '/cancel-order-reason', 'ApiController@cancelOrderReason');
            Route::match(['post'], '/cancel-order', 'ApiController@cancelOrder');
        /* after login */
    });
/* Api */