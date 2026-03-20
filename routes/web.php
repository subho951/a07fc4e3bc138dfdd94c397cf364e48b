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
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [FrontController::class, 'index']);
Route::get('event-checkin/{id}', [FrontController::class, 'eventCheckin']);
Route::get('delete-account', [FrontController::class, 'deleteaccountview']);
Route::post('delete-account-update', [FrontController::class, 'deleteaccount'])->name('delete-account.store');
Route::get('pages/{id}', [FrontController::class, 'page']);

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
                Route::post('application-settings', 'UserController@application_settings');
          		Route::get('test-email', 'UserController@testEmail');
            /* setting */

            /* industry */
                Route::get('industry/list', 'IndustryController@list');
                Route::match(['get', 'post'], 'industry/add', 'IndustryController@add');
                Route::match(['get', 'post'], 'industry/edit/{id}', 'IndustryController@edit');
                Route::get('industry/delete/{id}', 'IndustryController@delete');
                Route::get('industry/change-status/{id}', 'IndustryController@change_status');
            /* industry */
            /* interest */
                Route::get('interest/list', 'InterestController@list');
                Route::match(['get', 'post'], 'interest/add', 'InterestController@add');
                Route::match(['get', 'post'], 'interest/edit/{id}', 'InterestController@edit');
                Route::get('interest/delete/{id}', 'InterestController@delete');
                Route::get('interest/change-status/{id}', 'InterestController@change_status');
            /* interest */
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
            /* committee category */
                Route::get('committee-category/list', 'CommitteeCategoryController@list');
                Route::match(['get', 'post'], 'committee-category/add', 'CommitteeCategoryController@add');
                Route::match(['get', 'post'], 'committee-category/edit/{id}', 'CommitteeCategoryController@edit');
                Route::get('committee-category/delete/{id}', 'CommitteeCategoryController@delete');
                Route::get('committee-category/change-status/{id}', 'CommitteeCategoryController@change_status');
            /* committee category */
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
            /* theme */
                Route::get('theme/list', 'ThemeController@list');
                Route::match(['get', 'post'], 'theme/add', 'ThemeController@add');
                Route::match(['get', 'post'], 'theme/edit/{id}', 'ThemeController@edit');
                Route::get('theme/delete/{id}', 'ThemeController@delete');
                Route::get('theme/change-status/{id}', 'ThemeController@change_status');
            /* theme */
            /* core */
                Route::get('core/list', 'CoreController@list');
                Route::match(['get', 'post'], 'core/add', 'CoreController@add');
                Route::match(['get', 'post'], 'core/edit/{id}', 'CoreController@edit');
                Route::get('core/delete/{id}', 'CoreController@delete');
                Route::get('core/change-status/{id}', 'CoreController@change_status');
                Route::get('core/core-members/{id}', 'CoreController@core_members');
                Route::get('core/points-history/{id}', 'CoreController@points_history');
            /* core */
            /* core meeting */
                Route::get('core-meeting/list', 'CoreMeetingController@list');
                Route::match(['get', 'post'], 'core-meeting/add', 'CoreMeetingController@add');
                Route::match(['get', 'post'], 'core-meeting/edit/{id}', 'CoreMeetingController@edit');
                Route::get('core-meeting/delete/{id}', 'CoreMeetingController@delete');
                Route::get('core-meeting/change-status/{id}', 'CoreMeetingController@change_status');
            /* core meeting */
            /* leader board */
                Route::get('leader-board/core', 'LeaderboardController@core');
                Route::get('leader-board/member', 'LeaderboardController@member');
            /* leader board */
            /* event */
                Route::get('event/list', 'EventController@list');
                Route::match(['get', 'post'], 'event/add', 'EventController@add');
                Route::match(['get', 'post'], 'event/edit/{id}', 'EventController@edit');
                Route::get('event/delete/{id}', 'EventController@delete');
                Route::get('event/change-status/{id}', 'EventController@change_status');
                Route::get('event/registered-users/{id}', 'EventController@registered_users');
            /* event */
            /* newsletter */
                Route::get('newsletter/list', 'NewsletterController@list');
                Route::match(['get', 'post'], 'newsletter/add', 'NewsletterController@add');
                Route::match(['get', 'post'], 'newsletter/edit/{id}', 'NewsletterController@edit');
                Route::get('newsletter/delete/{id}', 'NewsletterController@delete');
                Route::get('newsletter/change-status/{id}', 'NewsletterController@change_status');
                Route::get('newsletter/send/{id}', 'NewsletterController@send');
            /* newsletter */
        });
    });
/* Admin Panel */
/* Api */
    Route::prefix('api')->namespace('App\Http\Controllers')->group(function(){
        // Other Version 2 routes
        /* before login */
            Route::match(['get'], '/get-app-setting', 'ApiController@getAppSetting');
            Route::match(['post'], '/get-static-pages', 'ApiController@getStaticPages');

            Route::match(['post'], '/signin-with-email', 'ApiController@signinWithEmail');
            Route::match(['post'], '/signin-validate-otp', 'ApiController@signinValidateOTP');

            Route::match(['post'], '/forgot-password', 'ApiController@forgotPassword');
            Route::match(['post'], '/validate-otp', 'ApiController@validateOtp');
            Route::match(['post'], '/resend-otp', 'ApiController@resendOtp');
            Route::match(['post'], '/reset-password', 'ApiController@resetPassword');
        /* before login */
        /* after login */
            Route::match(['get'], '/signout', 'ApiController@signout');
            Route::match(['get'], '/dashboard', 'ApiController@dashboard');
            Route::match(['post'], '/change-password', 'ApiController@changePassword');
            Route::match(['get'], '/get-master', 'ApiController@getMaster');
            Route::match(['get'], '/get-profile', 'ApiController@getProfile');
            Route::match(['post'], '/update-profile', 'ApiController@updateProfile');
            Route::match(['post'], '/upload-profile-image', 'ApiController@uploadProfileImage');

            Route::match(['get'], '/core', 'ApiController@core');
            Route::match(['get'], '/committee-members', 'ApiController@committeeMembers');
            Route::match(['post'], '/member-directory', 'ApiController@memberDirectory');
            Route::match(['post'], '/member-detail', 'ApiController@memberDetail');
            Route::match(['get'], '/concierge', 'ApiController@concierge');
            Route::match(['get'], '/privileges', 'ApiController@privileges');
            Route::match(['get'], '/leaderboard', 'ApiController@leaderboard');
            Route::match(['get'], '/events', 'ApiController@events');
            Route::match(['post'], '/event-detail', 'ApiController@eventDetail');
            Route::match(['post'], '/event-registration', 'ApiController@eventRegistration');
            Route::match(['get'], '/home', 'ApiController@home');
            Route::match(['post'], '/get-notification', 'ApiController@getNotification');
            Route::match(['get'], 'delete-account', 'ApiController@deleteAccount');
            Route::match(['post'], 'test-fcm', 'ApiController@testFCM');
            Route::match(['get'], 'identity-card', 'ApiController@identityCard');
        /* after login */
    });
/* Api */