<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;

use App\Models\Achievement;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CommitteeCategory;
use App\Models\Core;
use App\Models\CoreMeeting;
use App\Models\CoreMember;
use App\Models\EmailLog;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\GeneralSetting;
use App\Models\Industry;
use App\Models\Interest;
use App\Models\Magazine;
use App\Models\Media;
use App\Models\Page;
use App\Models\Privilege;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\UserActivity;
use App\Models\UserPoint;
use App\Models\UserRegEvent;
use App\Models\UserRegEventAnswer;
use App\Models\UserDevice;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
use App\Libraries\CreatorJwt;
use App\Libraries\JWT;
use Dompdf\Dompdf;
use Dompdf\Options;
date_default_timezone_set("Asia/Calcutta");
class ApiController extends Controller
{
    protected $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    /* before login screen */
        public function getAppSetting(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $generalSetting = GeneralSetting::find(1);
                if($generalSetting){
                    $apiResponse        = [
                        'site_name'             => $generalSetting->site_name,
                        'site_phone'            => $generalSetting->site_phone,
                        'site_phone2'           => $generalSetting->site_phone2,
                        'site_mail'             => $generalSetting->site_mail,
                        'site_url'              => $generalSetting->site_url,
                        'site_logo'             => env('UPLOADS_URL').$generalSetting->site_logo,
                        'site_address'          => $generalSetting->description,
                        'theme_color'           => $generalSetting->theme_color,
                        'font_color'            => $generalSetting->font_color,
                        'twitter_profile'       => $generalSetting->twitter_profile,
                        'facebook_profile'      => $generalSetting->facebook_profile,
                        'instagram_profile'     => $generalSetting->instagram_profile,
                        'linkedin_profile'      => $generalSetting->linkedin_profile,
                        'youtube_profile'       => $generalSetting->youtube_profile
                    ];
                }
                
                http_response_code(200);
                $apiStatus          = TRUE;
                $apiMessage         = 'Data Available !!!';
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function getStaticPages(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'page_slug'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $page_slug      = $requestData['page_slug'];
                $pageContent    = Page::select('page_title', 'long_description', 'short_description')->where('status', '=', 1)->where('slug', '=', $page_slug)->first();
                $generalSetting = GeneralSetting::find(1);
                if($pageContent){
                    $apiResponse[] = [
                        'page_title'                => $pageContent->page_title,
                        'short_description'         => $pageContent->short_description,
                        'long_description'          => $pageContent->long_description,
                    ];
                }
                
                http_response_code(200);
                $apiStatus          = TRUE;
                $apiMessage         = 'Data Available !!!';
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
    /* before login screen */
    /* authentication */
        public function signin(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'email', 'password', 'device_token', 'fcm_token'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $email                      = $requestData['email'];
                $password                   = $requestData['password'];
                $device_type                = $headerData['source'][0];
                $device_token               = $requestData['device_token'];
                $fcm_token                  = $requestData['fcm_token'];
                // $checkUser                  = User::where('email', '=', $email)->where('status', '=', 1)->first();
                $checkUser                  = User::where(function($query) {
                                                $query->where('status', 1);
                                             })
                                             ->where(function($query) use ($email) {
                                                $query->where('email', $email)
                                                      ->orWhere('phone', $email);
                                             })
                                             ->first();
                if($checkUser){
                    if(Hash::check($password, $checkUser->password)){
                        $objOfJwt           = new CreatorJwt();
                        $app_access_token   = $objOfJwt->GenerateToken($checkUser->id, $checkUser->email, $checkUser->phone);
                        $user_id                        = $checkUser->id;
                        $fields     = [
                            'user_id'               => $user_id,
                            'device_type'           => $device_type,
                            'device_token'          => $device_token,
                            'fcm_token'             => $fcm_token,
                            'app_access_token'      => $app_access_token,
                        ];
                        $checkUserTokenExist            = UserDevice::where('user_id', '=', $user_id)->where('published', '=', 1)->where('device_type', '=', $device_type)->where('device_token', '=', $device_token)->first();
                        if(!$checkUserTokenExist){
                            UserDevice::insert($fields);
                        } else {
                            UserDevice::where('id','=',$checkUserTokenExist->id)->update($fields);
                        }
                        $apiResponse = [
                            'user_id'               => $user_id,
                            'name'                  => $checkUser->first_name.' '.$checkUser->last_name,
                            'email'                 => $checkUser->email,
                            'phone'                 => $checkUser->phone,
                            'role'                  => 'USER',
                            'device_type'           => $device_type,
                            'device_token'          => $device_token,
                            'fcm_token'             => $fcm_token,
                            'app_access_token'      => $app_access_token,
                        ];
                        /* view analytics track */
                            $userAgent                      = $request->header('User-Agent', 'unknown');
                            $acceptLanguage                 = $request->header('Accept-Language', 'en');
                            $clientIp                       = $request->ip();
                            $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                            $viewData = [
                                'device_id'     => $deviceId,
                                'page'          => 'signin',
                                'product_id'    => 0,
                            ];
                            UserView::insert($viewData);
                        /* view analytics track */
                        $apiStatus                          = TRUE;
                        $apiMessage                         = 'SignIn Successfully !!!';
                    } else {
                        $apiStatus                          = FALSE;
                        $apiMessage                         = 'Invalid Password !!!';
                    }                   
                } else {
                    $apiStatus                              = FALSE;
                    $apiMessage                             = 'We Don\'t Recognize You !!!';
                }
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function signinWithEmail(Request $request)
        {
            $apiStatus = true;
            $apiMessage = '';
            $apiResponse = [];
            $apiExtraField = '';
            $apiExtraData = '';
            $requestData = $request->all();
            $requiredFields = ['key', 'source', 'email'];
            $headerData = $request->header();
            if (! $this->validateArray($requiredFields, $requestData)) {
                $apiStatus = false;
                $apiMessage = 'All Data Are Not Present !!!';
            }
            if ($headerData['key'][0] == env('PROJECT_KEY')) {
                $checkEmail = User::where('email', $requestData['email'])
                                    ->orWhere('phone', $requestData['email'])
                                    ->first();

                if ($checkEmail) {
                    if($checkEmail->status == 1){
                        
                        // $remember_token  = rand(1000,9999);
                        $remember_token = 1234;
                        User::where('id', '=', $checkEmail->id)->update(['remember_token' => $remember_token]);
                        $mailData = [
                            'id'    => $checkEmail->id,
                            'email' => $checkEmail->email,
                            'otp'   => $remember_token,
                        ];
                        $generalSetting = GeneralSetting::find('1');
                        $subject = GeneralSetting::where('slug', 'site_name')->value('value').' :: SignIn OTP';
                        $message = view('email-templates.otp', $mailData);
                        echo $message;die;
                        // $this->sendMail($requestData['email'], $subject, $message);

                    /* email log capture */
                        $fields = [
                            'name' => $checkEmail->name,
                            'email' => $checkEmail->email,
                            'subject' => $subject,
                            'message' => $message,
                            'status' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        EmailLog::insert($fields);
                    /* email log capture */

                    //Authentication log capture //
                        $ipAddress = $request->ip();
                        $userAgent = $request->header('User-Agent');
                        $fields101 = [
                            'user_email' => $checkEmail->email,
                            'user_name' => $checkEmail->name,
                            'user_type' => 'USER',
                            'ip_address' => $ipAddress,
                            'activity_type' => 0,
                            'activity_details' => 'OTP Sent To Email Validation !!!',
                            'platform_type' => 'WEB',
                            'browser_used' => $userAgent,
                            'status' => 1,
                            'created_by' => (($checkEmail) ? $checkEmail->id : 0),
                            'updated_by' => (($checkEmail) ? $checkEmail->id : 0),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        UserActivity::insert($fields101);
                    //Authentication log capture //
                    $apiResponse = $mailData;
                    $apiStatus = true;

                    http_response_code(200);
                    $apiMessage = 'OTP Sent To Email Validation !!!';
                    $apiExtraField = 'response_code';
                    $apiExtraData = http_response_code();
                    } else{
                        $ipAddress = $request->ip();
                        $userAgent = $request->header('User-Agent');
                        //Authentication log capture //
                            $fields101 = [
                                'user_email' => $requestData['email'],
                                'user_name' => '',
                                'user_type' => '',
                                'ip_address' => $ipAddress,
                                'activity_type' => 0,
                                'activity_details' => 'Your account is deactiveted please contact admin !!!',
                                'platform_type' => 'WEB',
                                'browser_used' => $userAgent,
                                'status' => 1,
                                'created_by' => (($checkEmail) ? $checkEmail->id : 0),
                                'updated_by' => (($checkEmail) ? $checkEmail->id : 0),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            UserActivity::insert($fields101);
                        //Authentication log capture //

                        $apiStatus = false;
                        http_response_code(400);
                        $apiMessage = 'Email Not Activated contact admin !!!';
                        $apiExtraField = 'response_code';
                        $apiExtraData = http_response_code();
                    }                
                } else {
                    $ipAddress = $request->ip();
                    $userAgent = $request->header('User-Agent');
                    //Authentication log capture //
                        $fields101 = [
                            'user_email' => $requestData['email'],
                            'user_name' => '',
                            'user_type' => '',
                            'ip_address' => $ipAddress,
                            'activity_type' => 0,
                            'activity_details' => 'We Don\'t Recognize You !!!',
                            'platform_type' => 'WEB',
                            'browser_used' => $userAgent,
                            'status' => 1,
                            'created_by' => (($checkEmail) ? $checkEmail->id : 0),
                            'updated_by' => (($checkEmail) ? $checkEmail->id : 0),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        UserActivity::insert($fields101);
                    //Authentication log capture //

                    $apiStatus = false;
                    http_response_code(400);
                    $apiMessage = 'Email Not Registered With Us !!!';
                    $apiExtraField = 'response_code';
                    $apiExtraData = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus = false;
                $apiMessage = $this->getResponseCode(http_response_code());
                $apiExtraField = 'response_code';
                $apiExtraData = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }

        public function signinValidateOTP(Request $request)
        {
            $apiStatus = true;
            $apiMessage = '';
            $apiResponse = [];
            $apiExtraField = '';
            $apiExtraData = '';
            $requestData = $request->all();
            $requiredFields = ['key', 'source', 'id', 'otp', 'device_token', 'fcm_token'];
            $headerData = $request->header();
            if (! $this->validateArray($requiredFields, $requestData)) {
                $apiStatus = false;
                $apiMessage = 'All Data Are Not Present !!!';
            }
            if ($headerData['key'][0] == env('PROJECT_KEY')) {
                $getUser = User::where('id', '=', $requestData['id'])->first();
                $roleName = PermissionRole::where('id', '=', $getUser->role_id)->first();
                if ($getUser) {
                    $remember_token = $getUser->remember_token;
                    $device_type = $headerData['source'][0];
                    $device_token = $requestData['device_token'];
                    $fcm_token = $requestData['fcm_token'];

                    if ($remember_token == $requestData['otp']) {
                        User::where('id', '=', $requestData['id'])->update(['remember_token' => 0]);

                        $objOfJwt = new CreatorJwt;
                        $app_access_token = $objOfJwt->GenerateToken($getUser->id, $getUser->email, $getUser->phone);
                        $user_id = $getUser->id;
                        $fields = [
                            'user_id' => $user_id,
                            'device_type' => $device_type,
                            'device_token' => $device_token,
                            'fcm_token' => $fcm_token,
                            'app_access_token' => $app_access_token,
                        ];
                        $checkUserTokenExist = UserDevice::where('user_id', '=', $user_id)->where('published', '=', 1)->where('device_type', '=', $device_type)->where('device_token', '=', $device_token)->first();
                        if (! $checkUserTokenExist) {
                            UserDevice::insert($fields);
                        } else {
                            UserDevice::where('id', '=', $checkUserTokenExist->id)->update($fields);
                        }
                        $apiResponse = [
                            'user_id' => $user_id,
                            'name' => $getUser->name,
                            'email' => $getUser->email,
                            'phone' => $getUser->phone,
                            'role' => 'USER',
                            'device_type' => $device_type,
                            'device_token' => $device_token,
                            'fcm_token' => $fcm_token,
                            'app_access_token' => $app_access_token,
                        ];

                        /* authentication log capture */
                        $ipAddress = $request->ip();
                        $userAgent = $request->header('User-Agent');
                        $fields101 = [
                            'user_email' => $getUser->email,
                            'user_name' => $getUser->name,
                            'user_type' => $roleName->role_name,
                            'ip_address' => $ipAddress,
                            'activity_type' => 1,
                            'activity_details' => 'Signin Success !!!',
                            'platform_type' => 'WEB',
                            'browser_used' => $userAgent,
                            'status' => 1,
                            'created_by' => (($getUser) ? $getUser->id : 0),
                            'updated_by' => (($getUser) ? $getUser->id : 0),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        UserActivity::insert($fields101);
                        /* authentication log capture */

                        $apiStatus = true;
                        $apiMessage = 'SignIn Successfully !!!';

                        http_response_code(200);
                        $apiExtraField = 'response_code';
                        $apiExtraData = http_response_code();
                    } else {
                        $ipAddress = $request->ip();
                        $userAgent = $request->header('User-Agent');
                        $fields101 = [
                            'user_email' => $getUser->email,
                            'user_name' => $getUser->name,
                            'user_type' => $roleName->role_name,
                            'ip_address' => $ipAddress,
                            'activity_type' => 0,
                            'activity_details' => 'Otp Mismatched !!!',
                            'platform_type' => 'WEB',
                            'browser_used' => $userAgent,
                            'status' => 1,
                            'created_by' => (($getUser) ? $getUser->id : 0),
                            'updated_by' => (($getUser) ? $getUser->id : 0),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        UserActivity::insert($fields101);

                        $apiStatus = false;
                        http_response_code(400);
                        $apiMessage = 'OTP Mismatched !!!';
                        $apiExtraField = 'response_code';
                    }
                } else {
                    $ipAddress = $request->ip();
                    $userAgent = $request->header('User-Agent');
                    $fields101 = [
                        'user_email' => '',
                        'user_name' => '',
                        'user_type' => (($roleName) ? $roleName->role_name : ''),
                        'ip_address' => $ipAddress,
                        'activity_type' => 0,
                        'activity_details' => 'User Not Found !!!',
                        'platform_type' => 'WEB',
                        'browser_used' => $userAgent,
                        'status' => 1,
                        'created_by' => (($getUser) ? $getUser->id : 0),
                        'updated_by' => (($getUser) ? $getUser->id : 0),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    UserActivity::insert($fields101);

                    $apiStatus = false;
                    http_response_code(400);
                    $apiMessage = 'User Not Found !!!';
                    $apiExtraField = 'response_code';
                    $apiExtraData = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus = false;
                $apiMessage = $this->getResponseCode(http_response_code());
                $apiExtraField = 'response_code';
                $apiExtraData = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }

        public function forgotPassword(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'email'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $checkEmail = User::where('email', '=', $requestData['email'])->first();
                if($checkEmail){
                    $remember_token  = rand(1000,9999);
                    User::where('id', '=', $checkEmail->id)->update(['remember_token' => $remember_token]);
                    $mailData                   = [
                        'id'                => $checkEmail->id,
                        'email'             => $checkEmail->email,
                        'otp'               => $remember_token,
                    ];
                    $generalSetting             = GeneralSetting::find('1');
                    $subject                    = $generalSetting->site_name.' :: Forgot Password OTP';
                    $message                    = view('email-templates.otp',$mailData);
                    // echo $message;die;
                    $this->sendMail($requestData['email'], $subject, $message);
                    $apiResponse                        = $mailData;
                    $apiStatus                          = TRUE;
                    /* view analytics track */
                        $userAgent                      = $request->header('User-Agent', 'unknown');
                        $acceptLanguage                 = $request->header('Accept-Language', 'en');
                        $clientIp                       = $request->ip();
                        $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                        $viewData = [
                            'device_id'     => $deviceId,
                            'page'          => 'forgot password',
                            'product_id'    => 0,
                        ];
                        UserView::insert($viewData);
                    /* view analytics track */
                    http_response_code(200);
                    $apiMessage                         = 'OTP Sent To Email Validation !!!';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(400);
                    $apiMessage         = 'Email Not Registered With Us !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function validateOtp(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'id', 'otp'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $getUser = User::where('id', '=', $requestData['id'])->first();
                if($getUser){
                    $remember_token  = $getUser->remember_token;
                    if($remember_token == $requestData['otp']){
                        User::where('id', '=', $requestData['id'])->update(['remember_token' => 0]);
                        // $this->sendMail('subhomoysamanta1989@gmail.com', $requestData['subject'], $requestData['message']);
                        $apiResponse        = [
                            'id'    => $getUser->id,
                            'email' => $getUser->email
                        ];
                        /* view analytics track */
                            $userAgent                      = $request->header('User-Agent', 'unknown');
                            $acceptLanguage                 = $request->header('Accept-Language', 'en');
                            $clientIp                       = $request->ip();
                            $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                            $viewData = [
                                'device_id'     => $deviceId,
                                'page'          => 'validate OTP',
                                'product_id'    => 0,
                            ];
                            UserView::insert($viewData);
                        /* view analytics track */
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'OTP Validated Successfully !!!';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(400);
                        $apiMessage         = 'OTP Mismatched !!!';
                        $apiExtraField      = 'response_code';
                    }
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(400);
                    $apiMessage         = 'User Not Found !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function resendOtp(Request $request){
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'id'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $id         = $requestData['id'];
                $getUser    = User::where('id', '=', $id)->first();
                if($getUser){
                    $remember_token = rand(1000,9999);
                    $postData = [
                        'remember_token'        => $remember_token
                    ];
                    User::where('id', '=', $id)->update($postData);
                    
                    $mailData                   = [
                        'id'                => $getUser->id,
                        'email'             => $getUser->email,
                        'otp'               => $remember_token,
                    ];
                    $generalSetting             = GeneralSetting::find('1');
                    $subject                    = $generalSetting->site_name.' :: Resend OTP';
                    $message                    = view('email-templates.otp',$mailData);
                    // echo $message;die;
                    $this->sendMail($getUser->email, $subject, $message);
                    $apiResponse                        = $mailData;

                    /* view analytics track */
                        $userAgent                      = $request->header('User-Agent', 'unknown');
                        $acceptLanguage                 = $request->header('Accept-Language', 'en');
                        $clientIp                       = $request->ip();
                        $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                        $viewData = [
                            'device_id'     => $deviceId,
                            'page'          => 'resend OTP',
                            'product_id'    => 0,
                        ];
                        UserView::insert($viewData);
                    /* view analytics track */
                    $apiStatus                          = TRUE;
                    http_response_code(200);
                    $apiMessage                         = 'OTP Resend !!!';
                    $apiExtraField                      = 'response_code';
                    $apiExtraData                       = http_response_code();
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(400);
                    $apiMessage         = 'User Not Found !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function resetPassword(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source', 'id', 'password', 'confirm_password'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $getUser = User::where('id', '=', $requestData['id'])->first();
                if($getUser){
                    if($requestData['password'] == $requestData['confirm_password']){
                        User::where('id', '=', $requestData['id'])->update(['password' => Hash::make($requestData['password'])]);
                        $mailData        = [
                            'id'        => $getUser->id,
                            'name'      => $getUser->first_name.' '.$getUser->last_name,
                            'email'     => $getUser->email
                        ];
                        $generalSetting             = GeneralSetting::find('1');
                        $subject                    = $generalSetting->site_name.' :: Reset Password';
                        $message                    = view('email-templates.change-password',$mailData);
                        // echo $message;die;
                        $this->sendMail($getUser->email, $subject, $message);
                        /* view analytics track */
                            $userAgent                      = $request->header('User-Agent', 'unknown');
                            $acceptLanguage                 = $request->header('Accept-Language', 'en');
                            $clientIp                       = $request->ip();
                            $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                            $viewData = [
                                'device_id'     => $deviceId,
                                'page'          => 'reset password',
                                'product_id'    => 0,
                            ];
                            UserView::insert($viewData);
                        /* view analytics track */
                        $apiStatus                          = TRUE;
                        http_response_code(200);
                        $apiMessage                         = 'Password Reset Successfully !!!';
                        $apiExtraField                      = 'response_code';
                        $apiExtraData                       = http_response_code();
                    } else {
                        $apiStatus          = FALSE;
                        http_response_code(400);
                        $apiMessage         = 'Password & Confirm Password Not Matched !!!';
                        $apiExtraField      = 'response_code';
                    }
                } else {
                    $apiStatus          = FALSE;
                    http_response_code(400);
                    $apiMessage         = 'User Not Found !!!';
                    $apiExtraField      = 'response_code';
                    $apiExtraData       = http_response_code();
                }
            } else {
                http_response_code(400);
                $apiStatus          = FALSE;
                $apiMessage         = $this->getResponseCode(http_response_code());
                $apiExtraField      = 'response_code';
                $apiExtraData       = http_response_code();
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
    /* authentication */
    /* after login */
        public function signout(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = [];
            $headerData         = $request->header();
            // Helper::pr($headerData);
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $headerData['authorization'][0];
                $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('published', '=', 1)->first();
                if($checkUserTokenExist){
                    UserDevice::where('app_access_token', '=', $app_access_token)->delete();
                    /* view analytics track */
                        $userAgent                      = $request->header('User-Agent', 'unknown');
                        $acceptLanguage                 = $request->header('Accept-Language', 'en');
                        $clientIp                       = $request->ip();
                        $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                        $viewData = [
                            'device_id'     => $deviceId,
                            'page'          => 'signout',
                            'product_id'    => 0,
                        ];
                        UserView::insert($viewData);
                    /* view analytics track */
                    $apiStatus                      = TRUE;
                    $apiMessage                     = 'Signout Successfully !!!';
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = 'Something Went Wrong !!!';
                }               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function dashboard(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $headerData['authorization'][0];
                $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('published', '=', 1)->first();
                if($checkUserTokenExist){
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId        = $getTokenValue['data'][1];
                        $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $order_count            = Order::where('cust_id', '=', $uId)->count();
                            $wishlist_count         = UserWishlist::where('user_id', '=', $uId)->count();
                            $approved_review_count  = UserReview::where('user_id', '=', $uId)->where('status', '=', 1)->count();
                            $pending_review_count   = UserReview::where('user_id', '=', $uId)->where('status', '=', 0)->count();
                            $billing_address        = UserLocation::where('user_id', '=', $uId)->where('status', '!=', 3)->where('type', '=', 'BILLING')->count();
                            $shipping_address       = UserLocation::where('user_id', '=', $uId)->where('status', '!=', 3)->where('type', '=', 'SHIPPING')->count();
                            $apiResponse            = [
                                'order_count'                   => $order_count,
                                'wishlist_count'                => $wishlist_count,
                                'approved_review_count'         => $approved_review_count,
                                'pending_review_count'          => $pending_review_count,
                                'billing_address'               => $billing_address,
                                'shipping_address'              => $shipping_address,
                            ];
                            /* view analytics track */
                                $userAgent                      = $request->header('User-Agent', 'unknown');
                                $acceptLanguage                 = $request->header('Accept-Language', 'en');
                                $clientIp                       = $request->ip();
                                $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                                $viewData = [
                                    'device_id'     => $deviceId,
                                    'page'          => 'dashboard',
                                    'product_id'    => 0,
                                ];
                                UserView::insert($viewData);
                            /* view analytics track */
                            $apiStatus          = TRUE;
                            $apiMessage         = 'Data Available !!!';
                        } else {
                            $apiStatus          = FALSE;
                            $apiMessage         = 'User Not Found !!!';
                        }
                    } else {
                        $apiStatus                      = FALSE;
                        $apiMessage                     = $getTokenValue['data'];
                        http_response_code(401);
                        $apiExtraData                   = http_response_code();
                    }
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = 'Something Went Wrong !!!';
                }               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function getProfile(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $headerData['authorization'][0];
                $getTokenValue              = $this->tokenAuth($app_access_token);
                if($getTokenValue['status']){
                    $uId        = $getTokenValue['data'][1];
                    $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                    $getUser    = User::where('id', '=', $uId)->first();
                    if($getUser){
                        $profileData    = [
                            'first_name'            => $getUser->first_name,
                            'last_name'             => $getUser->last_name,
                            'email'                 => $getUser->email,
                            'phone'                 => $getUser->phone,
                            'display_name'          => $getUser->display_name,
                            'profile_image'         => (($getUser->profile_image != '')?env('UPLOADS_URL').'user/'.$getUser->profile_image:env('NO_IMAGE')),
                        ];
                        /* view analytics track */
                            $userAgent                      = $request->header('User-Agent', 'unknown');
                            $acceptLanguage                 = $request->header('Accept-Language', 'en');
                            $clientIp                       = $request->ip();
                            $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                            $viewData = [
                                'device_id'     => $deviceId,
                                'page'          => 'get profile',
                                'product_id'    => 0,
                            ];
                            UserView::insert($viewData);
                        /* view analytics track */
                        $apiStatus          = TRUE;
                        $apiMessage         = 'Data Available !!!';
                        $apiResponse        = $profileData;
                    } else {
                        $apiStatus          = FALSE;
                        $apiMessage         = 'User Not Found !!!';
                    }
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = $getTokenValue['data'];
                    http_response_code(401);
                    $apiExtraData                   = http_response_code();
                }                                               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function updateProfile(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();       
            $requiredFields     = ['key', 'source', 'first_name', 'last_name', 'display_name', 'phone'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $request->header('Authorization');
                $getTokenValue              = $this->tokenAuth($app_access_token);
                if($getTokenValue['status']){
                    $uId        = $getTokenValue['data'][1];
                    $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                    $getUser    = User::where('id', '=', $uId)->first();
                    if($getUser){
                        // $profile_image  = $requestData['profile_image'];
                        // if(!empty($profile_image)){
                        //     $profile_image      = $profile_image;
                        //     $upload_type        = $profile_image['type'];
                        //     if($upload_type == 'image/jpeg' || $upload_type == 'image/jpg' || $upload_type == 'image/png' || $upload_type == 'image/gif'){
                        //         $upload_base64      = $profile_image['base64'];
                        //         $img                = $upload_base64;
                        //         $proof_type         = $profile_image['type'];
                        //         if($proof_type == 'image/png'){
                        //             $extn = 'png';
                        //         } elseif($proof_type == 'image/jpg'){
                        //             $extn = 'jpg';
                        //         } elseif($proof_type == 'image/jpeg'){
                        //             $extn = 'jpeg';
                        //         } elseif($proof_type == 'image/gif'){
                        //             $extn = 'gif';
                        //         } else {
                        //             $extn = 'png';
                        //         }
                        //         $data               = base64_decode($img);
                        //         $fileName           = uniqid() . '.' . $extn;
                        //         $file               = 'public/uploads/user/' . $fileName;
                        //         $success            = file_put_contents($file, $data);
                        //         $profile_image      = $fileName;
                        //     } else {
                        //         $apiStatus          = FALSE;
                        //         http_response_code(404);
                        //         $apiMessage         = 'Please Upload Image !!!';
                        //         $apiExtraField      = 'response_code';
                        //         $apiExtraData       = http_response_code();
                        //     }
                        // } else {
                        //     $profile_image = $getUser->profile_image;
                        // }
                        /* banner image */
                            $imageFile      = $request->file('profile_image');
                            if($imageFile != ''){
                                $imageName      = $imageFile->getClientOriginalName();
                                $uploadedFile   = $this->upload_single_file('profile_image', $imageName, 'user', 'image');
                                if($uploadedFile['status']){
                                    $profile_image = $uploadedFile['newFilename'];
                                } else {
                                    $apiStatus          = FALSE;
                                    http_response_code(404);
                                    $apiMessage         = 'Please Upload Image !!!';
                                    $apiExtraField      = 'response_code';
                                    $apiExtraData       = http_response_code();
                                }
                            } else {
                                $profile_image = $getUser->profile_image;
                            }
                        /* banner image */
                        $postData = [
                                    'first_name'                    => $requestData['first_name'],
                                    'last_name'                     => $requestData['last_name'],
                                    // 'email'                         => $requestData['country'],
                                    'phone'                         => $requestData['phone'],
                                    'display_name'                  => $requestData['first_name'].' '.$requestData['last_name'],
                                    'profile_image'                 => $profile_image
                                ];
                        // Helper::pr($postData);
                        User::where('id', '=', $uId)->update($postData);
                        /* view analytics track */
                            $userAgent                      = $request->header('User-Agent', 'unknown');
                            $acceptLanguage                 = $request->header('Accept-Language', 'en');
                            $clientIp                       = $request->ip();
                            $deviceId                       = $this->createDeviceFingerprint($userAgent, $acceptLanguage, $clientIp);
                            $viewData = [
                                'device_id'     => $deviceId,
                                'page'          => 'update profile',
                                'product_id'    => 0,
                            ];
                            UserView::insert($viewData);
                        /* view analytics track */
                        $apiStatus                  = TRUE;
                        $apiMessage                 = 'Profile Updated Successfully !!!';
                    } else {
                        $apiStatus          = FALSE;
                        $apiMessage         = 'User Not Found !!!';
                    }
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = $getTokenValue['data'];
                    http_response_code(401);
                    $apiExtraData                   = http_response_code();
                }                                               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function uploadProfileImage(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['profile_image'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $request->header('Authorization');
                $getTokenValue              = $this->tokenAuth($app_access_token);
                if($getTokenValue['status']){
                    $uId        = $getTokenValue['data'][1];
                    $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                    $getUser    = User::where('id', '=', $uId)->first();
                    if($getUser){
                        $profile_image  = $requestData['profile_image'];
                        // if($profile_image != ''){
                        //     /* upload profile image */        
                        //         $upload_file    = $profile_image;
                        //         Helper::pr($upload_file);
                        //         // $img            = $upload_file['base64'];
                        //         $img            = str_replace('data:image/jpeg;base64,', '', $upload_file);
                        //         $img            = str_replace(' ', '+', $img);
                        //         $data           = base64_decode($img);
                        //         $fileName       = uniqid() . '.jpg';
                        //         $file           = 'public/uploads/user/' . $fileName;
                        //         $success        = file_put_contents($file, $data);
                        //         $profile_image  = $fileName;
                        //     /* upload profile image */
                        // } else {
                        //     $profile_image = $getUser->profile_image;
                        // }
                        if(!empty($profile_image)){
                            $profile_image      = $profile_image;
                            $upload_type        = $profile_image['type'];
                            if($upload_type == 'image/jpeg' || $upload_type == 'image/jpg' || $upload_type == 'image/png' || $upload_type == 'image/gif'){
                                $upload_base64      = $profile_image['base64'];
                                $img                = $upload_base64;
                                $proof_type         = $profile_image['type'];
                                if($proof_type == 'image/png'){
                                    $extn = 'png';
                                } elseif($proof_type == 'image/jpg'){
                                    $extn = 'jpg';
                                } elseif($proof_type == 'image/jpeg'){
                                    $extn = 'jpeg';
                                } elseif($proof_type == 'image/gif'){
                                    $extn = 'gif';
                                } else {
                                    $extn = 'png';
                                }
                                $data               = base64_decode($img);
                                $fileName           = uniqid() . '.' . $extn;
                                $file               = 'public/uploads/user/' . $fileName;
                                $success            = file_put_contents($file, $data);
                                $profile_image      = $fileName;
                            } else {
                                $apiStatus          = FALSE;
                                http_response_code(404);
                                $apiMessage         = 'Please Upload Image !!!';
                                $apiExtraField      = 'response_code';
                                $apiExtraData       = http_response_code();
                            }
                        } else {
                            $profile_image = $getUser->photo;
                        }
                        $postData = [
                                    'photo'         => $profile_image
                                ];
                        // Helper::pr($postData);
                        User::where('id', '=', $uId)->update($postData);
                        $apiStatus                  = TRUE;
                        $apiMessage                 = 'Profile Image Uploaded Successfully !!!';
                    } else {
                        $apiStatus          = FALSE;
                        $apiMessage         = 'User Not Found !!!';
                    }
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = $getTokenValue['data'];
                    http_response_code(401);
                    $apiExtraData                   = http_response_code();
                }                                               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
        public function deleteAccount(Request $request)
        {
            $apiStatus          = TRUE;
            $apiMessage         = '';
            $apiResponse        = [];
            $apiExtraField      = '';
            $apiExtraData       = '';
            $requestData        = $request->all();
            $requiredFields     = ['key', 'source'];
            $headerData         = $request->header();
            if (!$this->validateArray($requiredFields, $requestData)){
                $apiStatus          = FALSE;
                $apiMessage         = 'All Data Are Not Present !!!';
            }
            if($headerData['key'][0] == env('PROJECT_KEY')){
                $app_access_token           = $headerData['authorization'][0];
                $checkUserTokenExist        = UserDevice::where('app_access_token', '=', $app_access_token)->where('published', '=', 1)->first();
                if($checkUserTokenExist){
                    $getTokenValue              = $this->tokenAuth($app_access_token);
                    if($getTokenValue['status']){
                        $uId        = $getTokenValue['data'][1];
                        $expiry     = date('d/m/Y H:i:s', $getTokenValue['data'][4]);
                        $getUser    = User::where('id', '=', $uId)->first();
                        if($getUser){
                            $fields = [
                                'user_type'                 => 'user',
                                'entity_name'               => $getUser->name,
                                'email'                     => $getUser->email,
                                'is_email_verify'           => 1,
                                'phone'                     => $getUser->phone,
                                'is_phone_verify'           => 1,
                            ];
                            DeleteAccountRequest::insert($fields);
                            $apiStatus          = TRUE;
                            $apiMessage         = 'Account Delete Requests Submitted Successfully !!!';
                        } else {
                            $apiStatus          = FALSE;
                            $apiMessage         = 'User Not Found !!!';
                        }
                    } else {
                        $apiStatus                      = FALSE;
                        $apiMessage                     = $getTokenValue['data'];
                    }
                } else {
                    $apiStatus                      = FALSE;
                    $apiMessage                     = 'Something Went Wrong !!!';
                }               
            } else {
                $apiStatus          = FALSE;
                $apiMessage         = 'Unauthenticate Request !!!';
            }
            $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
        }
    /* after login */
    /*
    Get http response code
    Author : Subhomoy
    */
    private function getResponseCode($code = NULL){
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Unauthenticated Request !!!'; break;
                case 401: $text = 'Token Not Found !!!'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Token Has Expired !!!'; break;
                case 404: $text = 'User Not Found !!!'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'All Data Are Not Present !!!'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
            $text = '';
        }
        return $text;
    }
    /*
    Generate JWT tokens for authentication
    Author : Subhomoy
    */
    private static function generateToken($userId, $email, $phone){
        $token      = array(
            'id'                => $userId,
            'email'             => $email,
            'phone'             => $phone,
            'exp'               => time() + (30 * 24 * 60 * 60) // 30 days
        );
        // pr($token);
        return JWT::encode($token, TOKEN_SECRET, 'HS256');
    }
    /*
    Check Authentication
    Author : Subhomoy
    */
    private function tokenAuth($appAccessToken){
        $headers = apache_request_headers();
        if (isset($appAccessToken) && !empty($appAccessToken)) :
            $userdata = $this->matchToken($appAccessToken);
            // pr($userdata);
            if ($userdata['status']) :
                $checkToken =  UserDevice::where('user_id', '=', $userdata['data']->id)->where('app_access_token', '=', $appAccessToken)->first();
                // echo $this->db->last_query();
                // pr($userdata);
                if (!empty($checkToken)) :
                    if ($userdata['data']->exp && $userdata['data']->exp > time()) :
                        $tokenStatus = array(TRUE, $userdata['data']->id, $userdata['data']->email, $userdata['data']->phone, $userdata['data']->exp);
                    else :
                        $tokenStatus = array(FALSE, 'Token Has Expired 1 !!!');
                    endif;
                else :
                    $tokenStatus = array(FALSE, 'Token Has Expired 2 !!!');
                endif;
            else :
                $tokenStatus = array(FALSE, 'Token Not Found !!!');
            endif;
        else :
            $tokenStatus = array(FALSE, 'Token Not Found In Request !!!');
        endif;
        if ($tokenStatus[0]) :
            $this->userId           = $tokenStatus[1];
            $this->userEmail        = $tokenStatus[2];
            $this->userMobile       = $tokenStatus[3];
            $this->userExpiry       = $tokenStatus[4];
            // pr($tokenStatus);
            return array('status' => TRUE, 'data' => $tokenStatus);
        else :
            return array('status' => FALSE, 'data' => $tokenStatus[1]);
            // $this->response_to_json(FALSE, $tokenStatus[1]);
        endif;
    }
    /*
    Match JWT token with user token saved in database
    Author : Subhomoy
    */
    private static function matchToken($token){
        // try{
        //     // $decoded    = JWT::decode($token, TOKEN_SECRET, 'HS256');
        //     $decoded    = JWT::decode($token, new Key(TOKEN_SECRET, 'HS256'));
        //     // pr($decoded);
        // } catch (\Exception $e) {
        //     //echo 'Caught exception: ',  $e->getMessage(), "\n";
        //     return array('status' => FALSE, 'data' => '');
        // }
        
        // return array('status' => TRUE, 'data' => $decoded);
        try{
            $key = "1234567890qwertyuiopmnbvcxzasdfghjkl";
            $decoded = JWT::decode($token, $key, array('HS256'));
            // $decodedData = (array) $decoded;
        } catch (\Exception $e) {
            //echo 'Caught exception: ',  $e->getMessage(), "\n";
            return array('status' => FALSE, 'data' => '');
        }
        return array('status' => TRUE, 'data' => $decoded);
    }
}
