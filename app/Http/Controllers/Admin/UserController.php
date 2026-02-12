<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\GeneralSetting;
use App\Models\Admin;
use App\Models\EmailLog;
use App\Models\Enquiry;
use App\Models\UserActivity;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Product;
use App\Models\Order;
use App\Models\ImageGallery;
use App\Models\UserView;
use App\Models\UserVisit;
use App\Models\UserWebsiteActivity;
use App\Models\UserWishlist;
use App\Models\UserReview;
use App\Models\OrderDetail;

use Auth;
use Mail;
use App\Mail\ForgotPwdMail;
use App\Models\Core;
use App\Models\CoreMember;
use App\Models\Event;
use App\Models\Privilege;
use Session;
use Helper;
use Hash;
use DB;
use DateTime;
use DateInterval;
use DatePeriod;
class UserController extends Controller
{
    /* authentication */
        public function login(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'email'     => 'required|email|max:255',
                            'password'  => 'required|max:30',
                        ];
                if($this->validate($request, $rules)){
                    if(Auth::guard('admin')->attempt(['email' => $postData['email'], 'password' => $postData['password'], 'status' => 1])){
                        // Helper::pr(Auth::guard('admin')->user());put
                        $sessionData = Auth::guard('admin')->user();
                        $request->session()->put('user_id', $sessionData->id);
                        $request->session()->put('name', $sessionData->name);
                        $request->session()->put('type', $sessionData->type);
                        $request->session()->put('email', $sessionData->email);
                        $request->session()->put('is_admin_login', 1);
                        /* user activity */
                            $activityData = [
                                'user_email'        => $sessionData->email,
                                'user_name'         => $sessionData->name,
                                'user_type'         => 'ADMIN',
                                'ip_address'        => $request->ip(),
                                'activity_type'     => 1,
                                'activity_details'  => 'Login Success !!!',
                                'platform_type'     => 'WEB',
                            ];
                            UserActivity::insert($activityData);
                        /* user activity */
                        return redirect('admin/dashboard');
                    } else {
                        /* user activity */
                            $activityData = [
                                'user_email'        => $postData['email'],
                                'user_name'         => 'Super Admin',
                                'user_type'         => 'ADMIN',
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
            $data                           = [];
            $title                          = 'Sign In';
            $page_name                      = 'signin';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function forgotPassword(Request $request){
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'email' => 'required|email|max:255',
                        ];
                if($this->validate($request, $rules)){
                    $checkEmail                   = Admin::where('email','=',$postData['email'])->get();
                    if(count($checkEmail) > 0){
                        $row     =  Admin::where('email', '=', $postData['email'])->first();
                        $otp     =  rand(999,10000);
                        $fields  =  [
                                        'otp' => $otp
                                    ];
                        Admin::where('id', '=', $row->id)->update($fields);
                        $to = $row->email;
                        $subject = "Reset Password";
                        $message = "Your Reset Password is :" . $otp;
                        // $this->sendMail('avijit@keylines.net',$subject,$message);
                        return redirect('/admin/validateOtp/'.Helper::encoded($row->id))->with('success_message', 'OTP Sent To Your Registered Email !!!');
                    }else{
                        return redirect()->back()->with('error_message', 'Please Enter a Registered Email !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data                           = [];
            $title                          = 'Forgot Password';
            $page_name                      = 'forgot-password';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function validateOtp(Request $request, $id){
            $id                             = Helper::decoded($id);
            $data['id']                     = $id;
            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                            'otp1'     => 'required|max:1',
                            'otp2'     => 'required|max:1',
                            'otp3'     => 'required|max:1',
                            'otp4'     => 'required|max:1',
                        ];
                if($this->validate($request, $rules)){
                    // $id     = $postData['id'];
                    $otp1   = $postData['otp1'];
                    $otp2   = $postData['otp2'];
                    $otp3   = $postData['otp3'];
                    $otp4   = $postData['otp4'];
                    $newotp    = ($otp1.$otp2.$otp3.$otp4);
                    $checkUser = Admin::where('id', '=', $id)->first();
                    if($checkUser){
                        $otp = $checkUser->otp;
                        if($otp == $newotp){
                            $postData = [
                                            'otp'        => '',
                                        ];
                            Admin::where('id', '=', $checkUser->id)->update($postData);
                            return redirect('/admin/changePassword/'.Helper::encoded($checkUser->id))->with('success_message', 'OTP Validated. Just Reset Your Password !!!');
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
            $title                          = 'OTP';
            $page_name                      = 'validotp';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function changePassword(Request $request ,$id){
            $ID = Helper::decoded($id);
            if($request->isMethod('post')){
                $postData = $request->all();
                if($postData['new_password'] != $postData['old_password'] ){
                    return redirect()->back()->with('error_message', 'Password Doesn\'t match !!!');
                } else {
                    $postData = [
                                    'password'        => Hash::make($postData['new_password']),
                                ];
                    Admin::where('id', '=', $ID)->update($postData);
                    return redirect('/admin')->with('success_message', 'Password Reset Successfully. Please Sign In !!!');
                }
            }
            $data                           = [];
            $title                          = 'Change Password';
            $page_name                      = 'change-password';
            echo $this->admin_before_login_layout($title,$page_name,$data);
        }
        public function logout(Request $request){
            $user_email                             = $request->session()->get('email');
            $user_name                              = $request->session()->get('name');
            /* user activity */
                $activityData = [
                    'user_email'        => $user_email,
                    'user_name'         => $user_name,
                    'user_type'         => 'ADMIN',
                    'ip_address'        => $request->ip(),
                    'activity_type'     => 2,
                    'activity_details'  => 'You Are Successfully Logged Out !!!',
                    'platform_type'     => 'WEB',
                ];
                UserActivity::insert($activityData);
            /* user activity */
            $request->session()->forget(['user_id', 'name', 'email']);
            // Helper::pr(session()->all());die;
            Auth::guard('admin')->logout();
            return redirect('/admin')->with('success_message', 'You Are Successfully Logged Out !!!');
        }
    /* authentication */
    /* dashboard */
        public function dashboardOld(Request $request){
            $data['filter_keyword']                                     = '';
            $data['filter_keyword_text']                                = 'All Time';
            $title                                                      = 'Dashboard';
            $page_name                                                  = 'dashboard';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function dashboard(Request $request){
            $data['committee_member_count']                                 = User::where('type', '=', 1)->where('status', '=', 1)->count();
            $data['normal_member_count']                                    = User::where('type', '=', 2)->where('status', '=', 1)->count();

            $data['core_count']                                             = Core::where('status', '=', 1)->count();
            $data['core_member_count']                                      = CoreMember::where('status', '=', 1)->count();

            $data['event_count']                                            = Event::where('status', '=', 1)->count();
            $data['privilege_count']                                        = Privilege::where('status', '=', 1)->count();

            $data['cores']                                                  = Core::select('name', 'points')->where('status', '=', 1)->orderBy('points', 'DESC')->get();

            $title                                                      = 'Dashboard';
            $page_name                                                  = 'dashboard-new';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function getMonthYearList($startDate) {
            $start = new DateTime($startDate);
            $end = new DateTime(); // Current date
            $end->modify('last day of this month'); // End of the current month
            $interval = new DateInterval('P1M'); // Interval of 1 month
            $period = new DatePeriod($start, $interval, $end->add($interval)); // Period from start to end

            $monthYearList = [];
            foreach ($period as $date) {
                // $monthYearList[] = $date->format('Y-m'); // Format as "YYYY-MM"
                $monthYearList[] = [
                    'month' => $date->format('m'), // Full month name
                    'month_name' => $date->format('M'), // Full month name
                    'year' => $date->format('Y'),  // Year
                ];
            }

            return $monthYearList;
        }
        public function message(Request $request){
            $data = [];
            $title                                                      = 'Message';
            $page_name                                                  = 'message';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function userAllActivity(Request $request){
            $data['rows']                                               = DB::table('user_website_activities')
                                                                                ->join('users', 'user_website_activities.user_id', '=', 'users.id')
                                                                                ->select('user_website_activities.*', 'users.profile_image')
                                                                                ->orderBy('user_website_activities.id', 'DESC')
                                                                                ->get();
            $title                                                      = 'User All Activity';
            $page_name                                                  = 'user-all-activity';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function dashboardFilter(Request $request){
            $postData = $request->all();
            $fDate = '';
            $tDate = '';
            if($postData['filter_keyword'] == 'today'){
                $fDate          = date('Y-m-d');
                $tDate          = date('Y-m-d');
                $filter_keyword_text = 'Today';
            }
            if($postData['filter_keyword'] == 'yesterday'){
                $fDate          = date('Y-m-d',strtotime("-1 days"));
                $tDate          = date('Y-m-d',strtotime("-1 days"));
                $filter_keyword_text = 'Yesterday';
            }
            if($postData['filter_keyword'] == 'this_month'){
                $fDate          = date('Y-m')."-01";
                $tDate          = date('Y-m-d');
                $filter_keyword_text = 'This Month';
            }
            if($postData['filter_keyword'] == 'last_month'){
                $fDate          = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1));
                $tDate          = date("Y-m-d", mktime(0, 0, 0, date("m"), 0));
                $filter_keyword_text = 'Last Month';
            }
            if($postData['filter_keyword'] == 'last_7_days'){
                $fDate          = date('Y-m-d', strtotime('-7 days'));
                $tDate          = date('Y-m-d',strtotime("-1 days"));
                $filter_keyword_text = 'Last 7 Days';
            }
            if($postData['filter_keyword'] == 'last_30_days'){
                $fDate          = date('Y-m-d', strtotime('-30 days'));
                $tDate          = date('Y-m-d',strtotime("-1 days"));
                $filter_keyword_text = 'Last 30 Days';
            }
            if($postData['filter_keyword'] == 'this_year'){
                $fDate          = date('Y')."-01-01";
                $tDate          = date('Y')."-12-31";
                $filter_keyword_text = 'This Year';
            }
            if($postData['filter_keyword'] == 'last_year'){
                $fDate          = (date('Y') - 1)."-01-01";
                $tDate          = (date('Y') - 1)."-12-31";
                $filter_keyword_text = 'Last Year';
            }
            if($postData['filter_keyword'] == ''){
                return redirect()->to('/admin/dashboard');
            }
            $data['filter_keyword']                                     = $postData['filter_keyword'];
            $data['filter_keyword_text']                                = $filter_keyword_text;

            // $data['total_products']                                     = Product::where('status', '!=', 3)->where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->count();
            // $data['total_new_products']                                 = Product::where('status', '!=', 3)->where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->where('is_new', '=', 1)->count();
            // $data['total_active_products']                              = Product::where('status', '=', 1)->where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->count();

            // $data['total_orders']                                       = Order::count();
            // $data['total_new_orders']                                   = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->where('status', '=', 1)->count();
            // $data['total_rejected_orders']                              = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->where('status', '=', 6)->count();
            // $data['total_cancelled_orders']                             = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->where('status', '=', 7)->count();

            // $data['total_customers']                                    = User::where('status', '!=', 3)->where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->count();
            // $data['total_sales']                                        = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->sum('net_amt');
            // $data['total_refunds']                                      = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->where('is_refund', '=', 1)->sum('refund_amount');

            $data['total_view']                                         = UserView::where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->count();
            $data['total_visit']                                        = UserVisit::where('created_at', '>=', $fDate)->where('created_at', '<=', $tDate)->count();
            $data['total_sales']                                        = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->sum('net_amt');
            $data['total_orders']                                       = Order::where('order_date', '>=', $fDate)->where('order_date', '<=', $tDate)->count();

            $data['total_active_products']                              = Product::where('status', '=', 1)->count();
            $data['total_deactive_products']                            = Product::where('status', '=', 0)->count();
            $data['total_draft_products']                               = Product::where('status', '=', 2)->count();

            $data['total_new_orders']                                   = Order::where('status', '=', 1)->count();
            $data['total_processing_orders']                            = Order::where('status', '=', 2)->count();
            $data['total_incomplete_orders']                            = Order::where('status', '=', 3)->count();
            $data['total_shipped_orders']                               = Order::where('status', '=', 4)->count();
            $data['total_complete_orders']                              = Order::where('status', '=', 5)->count();
            $data['total_rejected_orders']                              = Order::where('status', '=', 6)->count();
            $data['total_cancelled_orders']                             = Order::where('status', '=', 7)->count();

            $data['recent_activities']                                  = DB::table('user_website_activities')
                                                                                ->join('users', 'user_website_activities.user_id', '=', 'users.id')
                                                                                ->select('user_website_activities.*', 'users.profile_image')
                                                                                ->orderBy('user_website_activities.id', 'DESC')
                                                                                ->limit(10)
                                                                                ->get();

            $title                                                      = 'Dashboard';
            $page_name                                                  = 'dashboard-new';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* dashboard */
    /* settings */
        public function settings(Request $request){
            $uId                            = $request->session()->get('user_id');
            $data['setting']                = GeneralSetting::where('id', '=', 1)->first();
            $data['admin']                  = Admin::where('id', '=', $uId)->first();
            $title                          = 'Settings';
            $page_name                      = 'settings';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
  		public function testEmail(){
            $to = 'subhomoysamanta1989@gmail.com';
            $subject = "Test Email Subject On " . date('Y-m-d H:i:s');
            $message = "Test Email Body On " . date('Y-m-d H:i:s');
            $this->sendMail($to,$subject,$message);
            return redirect('/admin/settings/')->with('success_message', 'Test Email Sent Successfully !!!');
        }
        public function profile_settings(Request $request){
            $uId        = $request->session()->get('user_id');
            $row        = Admin::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'name'            => 'required',
                'mobile'          => 'required',
                'email'           => 'required',
            ];
            if($this->validate($request, $rules)){
                /* profile image */
                $imageFile      = $request->file('image');
                if($imageFile != ''){
                    $imageName      = $imageFile->getClientOriginalName();
                    $uploadedFile   = $this->upload_single_file('image', $imageName, '', 'image');
                    if($uploadedFile['status']){
                        $image = $uploadedFile['newFilename'];
                    } else {
                        return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                    }
                } else {
                    $image = $row->image;
                }
                /* profile image */
                $fields = [
                    'name'                  => $postData['name'],
                    'mobile'                => $postData['mobile'],
                    'email'                 => $postData['email'],
                    'image'                 => $image
                ];
                // Helper::pr($fields);
                Admin::where('id', '=', $uId)->update($fields);
                return redirect()->back()->with('success_message', 'Profile Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function general_settings(Request $request){
            $row        = GeneralSetting::where('id', '=', 1)->first();
            $postData   = $request->all();
            $rules      = [
                'site_name'            => 'required',
                'site_phone'           => 'required',
                'site_mail'            => 'required',
                'system_email'         => 'required',
                'site_url'             => 'required',
            ];
            if($this->validate($request, $rules)){
                /* site logo */
                    $imageFile      = $request->file('site_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_logo = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_logo = $row->site_logo;
                    }
                /* site logo */
                /* site footer logo */
                    $imageFile      = $request->file('site_footer_logo');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_footer_logo', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_footer_logo = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_footer_logo = $row->site_footer_logo;
                    }
                /* site footer logo */
                /* site favicon */
                    $imageFile      = $request->file('site_favicon');
                    if($imageFile != ''){
                        $imageName      = $imageFile->getClientOriginalName();
                        $uploadedFile   = $this->upload_single_file('site_favicon', $imageName, '', 'image');
                        if($uploadedFile['status']){
                            $site_favicon = $uploadedFile['newFilename'];
                        } else {
                            return redirect()->back()->with(['error_message' => $uploadedFile['message']]);
                        }
                    } else {
                        $site_favicon = $row->site_favicon;
                    }
                /* site favicon */
                $fields = [
                    'site_name'                         => $postData['site_name'],
                    'site_phone'                        => $postData['site_phone'],
                    'site_mail'                         => $postData['site_mail'],
                    'system_email'                      => $postData['system_email'],
                    'site_url'                          => $postData['site_url'],
                    'description'                       => $postData['description'],
                    'theme_color'                       => $postData['theme_color'],
                    'font_color'                        => $postData['font_color'],
                    'twitter_profile'                   => $postData['twitter_profile'],
                    'facebook_profile'                  => $postData['facebook_profile'],
                    'instagram_profile'                 => $postData['instagram_profile'],
                    // 'linkedin_profile'                  => $postData['linkedin_profile'],
                    'youtube_profile'                   => $postData['youtube_profile'],
                    'document_size'                     => $postData['document_size'],
                    'photo_size'                        => $postData['photo_size'],
                    'video_size'                        => $postData['video_size'],
                    'site_logo'                         => $site_logo,
                    'site_footer_logo'                  => $site_footer_logo,
                    'site_favicon'                      => $site_favicon,
                ];
                // Helper::pr($fields);
                GeneralSetting::where('id', '=', 1)->update($fields);
                // Product::where('shipping_type', '=', 'FIXED')->update(['shipping_rate' => $postData['shipping_charge']]);
                return redirect()->back()->with('success_message', 'General Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function change_password(Request $request){
            $uId        = $request->session()->get('user_id');
            $adminData  = Admin::where('id', '=', $uId)->first();
            $postData   = $request->all();
            $rules      = [
                'old_password'            => 'required',
                'new_password'            => 'required',
                'confirm_password'        => 'required',
            ];
            if($this->validate($request, $rules)){
                $old_password       = $postData['old_password'];
                $new_password       = $postData['new_password'];
                $confirm_password   = $postData['confirm_password'];
                if(Hash::check($old_password, $adminData->password)){
                    if($new_password == $confirm_password){
                        $fields = [
                            'password'            => Hash::make($new_password)
                        ];
                        Admin::where('id', '=', $uId)->update($fields);
                        return redirect()->back()->with('success_message', 'Password Changed Successfully !!!');
                    } else {
                        return redirect()->back()->with('error_message', 'New & Confirm Password Does Not Matched !!!');
                    }
                } else {
                    return redirect()->back()->with('error_message', 'Current Password Is Incorrect !!!');
                }
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function email_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'from_email'            => 'required',
                'from_name'             => 'required',
                'smtp_host'             => 'required',
                'smtp_username'         => 'required',
                'smtp_password'         => 'required',
                'smtp_port'             => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'from_email'            => $postData['from_email'],
                    'from_name'             => $postData['from_name'],
                    'smtp_host'             => $postData['smtp_host'],
                    'smtp_username'         => $postData['smtp_username'],
                    'smtp_password'         => $postData['smtp_password'],
                    'smtp_port'             => $postData['smtp_port'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Email Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function email_template(Request $request){
            $postData = $request->all();
            $rules = [
                'email_template_user_signup'            => 'required',
                'email_template_forgot_password'        => 'required',
                'email_template_change_password'        => 'required',
                'email_template_failed_login'           => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'email_template_user_signup'            => $postData['email_template_user_signup'],
                    'email_template_forgot_password'        => $postData['email_template_forgot_password'],
                    'email_template_change_password'        => $postData['email_template_change_password'],
                    'email_template_failed_login'           => $postData['email_template_failed_login'],
                    'email_template_contactus'              => $postData['email_template_contactus'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Email Templates Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function sms_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'sms_authentication_key'            => 'required',
                'sms_sender_id'                     => 'required',
                'sms_base_url'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'sms_authentication_key'            => $postData['sms_authentication_key'],
                    'sms_sender_id'                     => $postData['sms_sender_id'],
                    'sms_base_url'                      => $postData['sms_base_url'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'SMS Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function footer_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'footer_text'            => 'required',
            ];
            if($this->validate($request, $rules)){
                $footer_link_name_array = $postData['footer_link_name'];
                $footer_link_name       = [];
                if(!empty($footer_link_name_array)){
                    for($f=0;$f<count($footer_link_name_array);$f++){
                        if($footer_link_name_array[$f]){
                            $footer_link_name[]       = $footer_link_name_array[$f];
                        }
                    }
                }
                $footer_link_array = $postData['footer_link'];
                $footer_link       = [];
                if(!empty($footer_link_array)){
                    for($f=0;$f<count($footer_link_array);$f++){
                        if($footer_link_array[$f]){
                            $footer_link[]       = $footer_link_array[$f];
                        }
                    }
                }
                $footer_link_name_array2 = $postData['footer_link_name2'];
                $footer_link_name2       = [];
                if(!empty($footer_link_name_array2)){
                    for($f=0;$f<count($footer_link_name_array2);$f++){
                        if($footer_link_name_array2[$f]){
                            $footer_link_name2[]       = $footer_link_name_array2[$f];
                        }
                    }
                }
                $footer_link_array2 = $postData['footer_link2'];
                $footer_link2       = [];
                if(!empty($footer_link_array2)){
                    for($f=0;$f<count($footer_link_array2);$f++){
                        if($footer_link_array2[$f]){
                            $footer_link2[]       = $footer_link_array2[$f];
                        }
                    }
                }
                $footer_link_name_array3 = $postData['footer_link_name3'];
                $footer_link_name3       = [];
                if(!empty($footer_link_name_array3)){
                    for($f=0;$f<count($footer_link_name_array3);$f++){
                        if($footer_link_name_array3[$f]){
                            $footer_link_name3[]       = $footer_link_name_array3[$f];
                        }
                    }
                }
                $footer_link_array3 = $postData['footer_link3'];
                $footer_link3       = [];
                if(!empty($footer_link_array3)){
                    for($f=0;$f<count($footer_link_array3);$f++){
                        if($footer_link_array3[$f]){
                            $footer_link3[]       = $footer_link_array3[$f];
                        }
                    }
                }
                $fields = [
                    'footer_text'                   => $postData['footer_text'],
                    'footer_link_name'              => json_encode($footer_link_name),
                    'footer_link'                   => json_encode($footer_link),
                    'footer_link_name2'             => json_encode($footer_link_name2),
                    'footer_link2'                  => json_encode($footer_link2),
                    'footer_link_name3'             => json_encode($footer_link_name3),
                    'footer_link3'                  => json_encode($footer_link3),
                ];
                // Helper::pr($fields);
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Footer Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function seo_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'meta_title'            => 'required',
                'meta_description'      => 'required'
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'meta_title'            => $postData['meta_title'],
                    'meta_description'      => $postData['meta_description'],
                    'meta_keywords'         => $postData['meta_keywords'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'SEO Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function payment_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'authorizenet_payment_type'         => 'required',
                'authorizenet_login_id'             => 'required',
                'authorizenet_transaction_key'      => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'authorizenet_payment_type'         => $postData['authorizenet_payment_type'],
                    'authorizenet_login_id'             => $postData['authorizenet_login_id'],
                    'authorizenet_transaction_key'      => $postData['authorizenet_transaction_key'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Payment Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function color_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'color_theme'               => 'required',
                'color_button'              => 'required',
                'color_title'               => 'required',
                'color_panel_bg'            => 'required',
                'color_panel_text'          => 'required',
                'color_accept_button'       => 'required',
                'color_reject_button'       => 'required',
                'color_transfer_button'     => 'required',
                'color_complete_button'     => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'color_theme'                       => $postData['color_theme'],
                    'color_button'                      => $postData['color_button'],
                    'color_title'                       => $postData['color_title'],
                    'color_panel_bg'                    => $postData['color_panel_bg'],
                    'color_panel_text'                  => $postData['color_panel_text'],
                    'color_accept_button'               => $postData['color_accept_button'],
                    'color_reject_button'               => $postData['color_reject_button'],
                    'color_transfer_button'             => $postData['color_transfer_button'],
                    'color_complete_button'             => $postData['color_complete_button'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Color Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
        public function shipping_settings(Request $request){
            $postData = $request->all();
            $rules = [
                'tax_percent'                                               => 'required',
                'domestic_free_shipping_min_amount'                         => 'required',
                'domestic_shipping_single_item'                             => 'required',
                'domestic_shipping_multiple_item'                           => 'required',
                'international_shipping_single_item'                        => 'required',
                'international_shipping_multiple_item'                      => 'required',
            ];
            if($this->validate($request, $rules)){
                $fields = [
                    'tax_percent'                                               => $postData['tax_percent'],
                    'domestic_free_shipping_min_amount'                         => $postData['domestic_free_shipping_min_amount'],
                    'domestic_shipping_single_item'                             => $postData['domestic_shipping_single_item'],
                    'domestic_shipping_multiple_item'                           => $postData['domestic_shipping_multiple_item'],
                    'international_shipping_single_item'                        => $postData['international_shipping_single_item'],
                    'international_shipping_multiple_item'                      => $postData['international_shipping_multiple_item'],
                ];
                GeneralSetting::where('id', '=', 1)->update($fields);
                return redirect()->back()->with('success_message', 'Tax & Shipping Settings Updated Successfully !!!');
            } else {
                return redirect()->back()->with('error_message', 'All Fields Required !!!');
            }
        }
    /* settings */
    /* email logs */
        public function emailLogs(){
            $data['rows']                   = EmailLog::where('status', '=', 1)->orderBy('id', 'DESC')->get();
            $title                          = 'Email Logs';
            $page_name                      = 'email-logs';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function emailLogsDetails(Request $request,$id ){
            $id = Helper::decoded($id);
            $data['logData']                   = EmailLog::where('id', '=', $id)->orderBy('id', 'DESC')->first();
            $title                          = 'Email Logs Details';
            $page_name                      = 'email-logs-info';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* email logs */
    /* login logs */
        public function loginLogs(){
            $data['rows1']                   = UserActivity::where('activity_type', '=', 0)->orderBy('activity_id', 'DESC')->get();
            $data['rows2']                   = UserActivity::where('activity_type', '=', 1)->orderBy('activity_id', 'DESC')->get();
            $data['rows3']                   = UserActivity::where('activity_type', '=', 2)->orderBy('activity_id', 'DESC')->get();
            $title                          = 'Login Logs';
            $page_name                      = 'login-logs';
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* login logs */
    /* image gallery */
        public function imageGallery(Request $request){
            $title                          = 'Image Gallery';
            $page_name                      = 'image-gallery';
            $data['rows']                   = ImageGallery::where('status', '!=', 3)->orderBy('id', 'DESC')->paginate(12);
            if($request->isMethod('post')){
                $image_array            = $request->file('image_file');
                if(!empty($image_array)){
                    $uploadedFile       = $this->commonFileArrayUpload('public/uploads/gallery/', $image_array, 'image');
                    if(!empty($uploadedFile)){
                        $images    = $uploadedFile;
                    } else {
                        $images    = [];
                    }
                }
                // Helper::pr($images);
                if(!empty($images)){
                    for($i=0;$i<count($images);$i++){
                        $image_link = env('UPLOADS_URL').'gallery/'.$images[$i];
                        $fields2 = [
                            'image_file'            => $images[$i],
                            'image_link'            => $image_link
                        ];
                        ImageGallery::insert($fields2);
                    }
                }
                return redirect("admin/image-gallery")->with('success_message', 'Images Uploaded Successfully !!!');
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* image gallery */
}
