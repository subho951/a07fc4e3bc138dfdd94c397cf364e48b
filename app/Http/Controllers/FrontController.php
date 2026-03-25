<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;
use Illuminate\Support\Facades\File;
use App\Services\FirebaseService;

use App\Models\Achievement;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CommitteeCategory;
use App\Models\Core;
use App\Models\CoreMeeting;
use App\Models\CoreMember;
use App\Models\CorePoint;
use App\Models\DeleteAccountRequest;
use App\Models\EmailLog;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\GeneralSetting;
use App\Models\Industry;
use App\Models\Interest;
use App\Models\Magazine;
use App\Models\Notification;
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

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

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

class FrontController extends Controller
{
    /* home */
        public function index(Request $request)
        {
            $data['generalSetting'] = GeneralSetting::find(1);
            $data['title'] = (($data['generalSetting'] && $data['generalSetting']->site_name != '') ? $data['generalSetting']->site_name : 'ALFA Network');
            return view('front.landing', $data);
        }
    /* home */
    /* event checkin */
        public function eventCheckin($token)
        {
            $generalSetting = GeneralSetting::find(1);

            $individual_attn_point = $generalSetting->individual_attn_point;
            $individual_backtoback_attn_count = $generalSetting->individual_backtoback_attn_count;
            $individual_backtoback_attn_point = $generalSetting->individual_backtoback_attn_point;
            $individual_not_attn = $generalSetting->individual_not_attn;
            $core_meeting_inbound_point = $generalSetting->core_meeting_inbound_point;
            $core_meeting_min_attn_percent = $generalSetting->core_meeting_min_attn_percent;
            $core_meeting_local_outbound_point = $generalSetting->core_meeting_local_outbound_point;
            $core_meeting_outbound_point = $generalSetting->core_meeting_outbound_point;

            // try {

                $id = Crypt::decryptString(urldecode($token));
                $row = UserRegEvent::findOrFail($id);
                // Helper::pr($row);
                if(!empty($row)){
                    $member_id = $row->userid;
                    $event_id = $row->eventid;
                    $getEvent = Event::select('id', 'title', 'venue', 'event_date', 'photo')->where('id', '=', $event_id)->first();
                    $getMember = User::select('id', 'name', 'phone', 'photo', 'points', 'core_id')->where('id', '=', $member_id)->first();
                    if($getMember){
                        // Prevent duplicate entry
                        if($row->status == 1){
                            $data['checkin_msg']            = "Already checked in!";
                            $data['user_event']             = $row;
                            $data['member']                 = $getMember;
                            $data['event']                  = $getEvent;
                            return view('front.event-checkin', $data);
                            // return "Already checked in!";
                        } else {
                            $row->status = 1;
                            $row->entry_timestamp = now();
                            $row->save();

                            $currentEvent = Event::find(32);

                            $previousEvents = Event::where('event_date', '<', $currentEvent->event_date)
                                ->orderBy('event_date', 'desc')
                                ->limit($individual_backtoback_attn_count)
                                ->pluck('id')
                                ->toArray();

                            /* member point calculation */
                                $opening_point = (int) $getMember->points;
                                $credited_points = 0;
                                $credited_points = (int) $individual_attn_point;

                                $eventAttnCount = 0;
                                if($previousEvents){
                                    for($k=0;$k<count($previousEvents);$k++){
                                        $evid = $previousEvents[$k];
                                        $checkAttendance = UserRegEvent::where('userid', '=', $member_id)->where('eventid', '=', $evid)->where('status', '=', 1)->count();
                                        if($checkAttendance > 0){
                                            $eventAttnCount++;
                                        }
                                    }
                                }

                                if($eventAttnCount >= $individual_backtoback_attn_count){
                                    $credited_points = (int) $individual_attn_point + (int) $individual_backtoback_attn_point;
                                }

                                $user_new_points = ($opening_point + $credited_points);
                                $fields1 = [
                                    'member_id'         => $member_id,
                                    'event_id'          => $event_id,
                                    'credited_points'   => $credited_points,
                                    'note'              => $credited_points . ' points credited for event attended',
                                ];
                                UserPoint::insert($fields1);
                                User::where('id', '=', $member_id)->update(['points' => $user_new_points]);
                            /* member point calculation */

                            /* core point calculation */
                                $core_id = $getMember->core_id;
                                if($core_id > 0){
                                    $getCore = Core::where('id', '=', $core_id)->first();
                                    if($getCore){
                                        $fields2 = [
                                            'core_id'           => $core_id,
                                            'member_id'         => $member_id,
                                            'event_id'          => $event_id,
                                            'meeting_id'        => 0,
                                            'credited_points'   => $credited_points,
                                            'note'              => $credited_points . ' points credited for event attended of ' . $getMember->name,
                                        ];
                                        CorePoint::insert($fields2);

                                        

                                        $opening_core_point = (int) $getCore->points;
                                        $core_new_points = ($opening_core_point + $credited_points);
                                        Core::where('id', '=', $core_id)->update(['points' => $core_new_points]);
                                    }
                                }
                            /* core point calculation */

                            // push notification send
                                $users = [];
                                $getTokens = UserDevice::select('fcm_token')->where('user_id', '=', $member_id)->where('published', '=', 1)->where('fcm_token', '!=', '')->get();
                                if($getTokens){
                                    foreach($getTokens as $getToken){
                                        $token = $getToken->fcm_token;

                                        $title = 'Event Checkin';
                                        $message = 'You are successfully attended in event ' . (($getEvent)?$getEvent->title:'') . ' at ' . date('d.m.Y h:i A');

                                        $image = (($getEvent)?(($getEvent->photo != '')?env('UPLOADS_URL').'event/'.$getEvent->photo:env('NO_IMAGE')):env('NO_IMAGE'));

                                        $data = [
                                            "event_id" => $event_id,
                                            "type" => 'event'
                                        ];

                                        $firebase_response = FirebaseService::sendNotification($token,$title,$message,$data,$image);

                                        $users[]            = $member_id;
                                        $notificationFields = [
                                            'title'             => $title,
                                            'description'       => $message,
                                            'to_users'          => $member_id,
                                            'users'             => json_encode($users),
                                            'is_send'           => 1,
                                            'send_timestamp'    => date('Y-m-d H:i:s'),
                                        ];
                                        Notification::insert($notificationFields);
                                    }
                                }

                                if($core_id > 0){
                                    $coreMembers = CoreMember::where('member_id', '!=', $member_id)->where('core_id', '=', $core_id)->get();
                                    if($coreMembers){
                                        foreach($coreMembers as $coreMember){
                                            $users = [];
                                            $getToken = UserDevice::select('fcm_token')->where('user_id', '=', $coreMember->member_id)->where('published', '=', 1)->where('fcm_token', '!=', '')->first();
                                            if($getToken){
                                                $token = $getToken->fcm_token;

                                                $title = 'Event Checkin';
                                                $message = $getMember->name . ' successfully attended in event ' . (($getEvent)?$getEvent->title:'') . ' at ' . date('d.m.Y h:i A');

                                                $image = (($getEvent)?(($getEvent->photo != '')?env('UPLOADS_URL').'event/'.$getEvent->photo:env('NO_IMAGE')):env('NO_IMAGE'));

                                                $data = [
                                                    "event_id" => $event_id,
                                                    "type" => 'event'
                                                ];

                                                $firebase_response = FirebaseService::sendNotification($token,$title,$message,$data,$image);

                                                $users[]            = $coreMember->member_id;
                                                $notificationFields = [
                                                    'title'             => $title,
                                                    'description'       => $message,
                                                    'to_users'          => $coreMember->member_id,
                                                    'users'             => json_encode($users),
                                                    'is_send'           => 1,
                                                    'send_timestamp'    => date('Y-m-d H:i:s'),
                                                ];
                                                Notification::insert($notificationFields);
                                            }
                                        }
                                    }
                                }
                            // push notification send

                            $data['checkin_msg']            = "Entry successful";
                            $data['user_event']             = $row;
                            $data['member']                 = $getMember;
                            $data['event']                  = $getEvent;
                            return view('front.event-checkin', $data);
                            // return "Entry successful";
                        }
                    } else {
                        return "Member not found";
                    }
                } else {
                    return "Event registration not found";
                }
            // } catch (\Exception $e) {
            //     return "Invalid QR Code";
            // }
        }
    /* event checkin */
    /* birthday cron */
        public function birthdayWishCron(Request $request)
        {
            $now = Carbon::now('Asia/Calcutta');
            $today = $now->format('Y-m-d');
            $monthDay = $now->format('m-d');

            $secureCronKey = env('CRON_KEY', '');
            if($secureCronKey != '' && $request->query('key') != $secureCronKey){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid cron key.',
                ], 401);
            }

            $generalSetting = GeneralSetting::find(1);
            $siteName = (($generalSetting && $generalSetting->site_name != '') ? $generalSetting->site_name : 'ALFA Network');
            $themeColor = (($generalSetting && $generalSetting->theme_color != '') ? $generalSetting->theme_color : '#FCC312');
            $fontColor = (($generalSetting && $generalSetting->font_color != '') ? $generalSetting->font_color : '#1F2937');
            $fontFamily = "'Segoe UI', 'Helvetica Neue', Arial, sans-serif";

            $birthdayUsers = User::select('id', 'name', 'email', 'photo', 'dob')
                                ->where('status', '=', 1)
                                ->whereNotNull('dob')
                                ->where('dob', '!=', '')
                                ->whereRaw("DATE_FORMAT(dob, '%m-%d') = ?", [$monthDay])
                                ->get();
            Helper::pr($birthdayUsers);

            $report = [
                'date' => $today,
                'total_birthday_users' => $birthdayUsers->count(),
                'push_sent' => 0,
                'push_skipped' => 0,
                'email_sent' => 0,
                'email_skipped' => 0,
                'errors' => [],
            ];

            if($birthdayUsers->isEmpty()){
                return response()->json([
                    'status' => true,
                    'message' => 'Birthday cron executed successfully.',
                    'report' => $report,
                ]);
            }

            foreach($birthdayUsers as $user){
                $userName = (($user->name != '') ? $user->name : 'Member');
                $pushTitle = 'Happy Birthday, '.$userName.'!';
                $pushMessage = 'Wishing you joy, success and a fantastic year ahead from '.$siteName.'.';
                $image = (($user->photo != '') ? env('UPLOADS_URL').'user/'.$user->photo : env('NO_IMAGE'));

                $alreadyPushedToday = Notification::where('to_users', '=', $user->id)
                                                    ->where('title', '=', $pushTitle)
                                                    ->whereDate('send_timestamp', '=', $today)
                                                    ->exists();

                if(!$alreadyPushedToday){
                    $tokenSentCount = 0;
                    $tokens = UserDevice::select('fcm_token')
                                        ->where('user_id', '=', $user->id)
                                        ->where('published', '=', 1)
                                        ->where('fcm_token', '!=', '')
                                        ->get();

                    if($tokens){
                        foreach($tokens as $device){
                            try {
                                FirebaseService::sendNotification(
                                    $device->fcm_token,
                                    $pushTitle,
                                    $pushMessage,
                                    [
                                        'member_id' => $user->id,
                                        'type' => 'birthday'
                                    ],
                                    $image
                                );
                                $tokenSentCount++;
                            } catch (\Exception $e) {
                                $report['errors'][] = 'Push failed for user ID '.$user->id.': '.$e->getMessage();
                            }
                        }
                    }

                    if($tokenSentCount > 0){
                        $notificationFields = [
                            'title'             => $pushTitle,
                            'description'       => $pushMessage,
                            'to_users'          => $user->id,
                            'users'             => json_encode([$user->id]),
                            'is_send'           => 1,
                            'send_timestamp'    => $now->format('Y-m-d H:i:s'),
                        ];
                        Notification::insert($notificationFields);
                        $report['push_sent']++;
                    } else {
                        $report['push_skipped']++;
                    }
                } else {
                    $report['push_skipped']++;
                }

                if($user->email != ''){
                    $subject = $siteName.' :: Happy Birthday '.$userName;
                    $alreadyEmailedToday = EmailLog::where('email', '=', $user->email)
                                                    ->where('subject', '=', $subject)
                                                    ->whereDate('created_at', '=', $today)
                                                    ->exists();

                    if(!$alreadyEmailedToday){
                        $mailData = [
                            'name' => $userName,
                            'site_name' => $siteName,
                            'generalSetting' => $generalSetting,
                            'theme_color' => $themeColor,
                            'font_color' => $fontColor,
                            'font_family' => $fontFamily,
                            'wish_message' => 'May your special day be filled with happiness, meaningful moments and continued success.',
                        ];

                        $message = view('email-templates.birthday-wish', $mailData);
                        $mailStatus = $this->sendMail(strtolower($user->email), $subject, $message);

                        $emailLogFields = [
                            'name' => $userName,
                            'email' => strtolower($user->email),
                            'subject' => $subject,
                            'message' => $message,
                            'status' => (($mailStatus) ? 1 : 0),
                            'created_at' => $now->format('Y-m-d H:i:s'),
                            'updated_at' => $now->format('Y-m-d H:i:s'),
                        ];
                        EmailLog::insert($emailLogFields);

                        if($mailStatus){
                            $report['email_sent']++;
                        } else {
                            $report['email_skipped']++;
                            $report['errors'][] = 'Email sending failed for user ID '.$user->id.'.';
                        }
                    } else {
                        $report['email_skipped']++;
                    }
                } else {
                    $report['email_skipped']++;
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Birthday cron executed successfully.',
                'report' => $report,
            ]);
        }
    /* birthday cron */
    /* delete account */
        public function deleteaccountview(Request $request)
        {        
            $data = [];
            $title                          = 'Delete Account';
            $page_name                      = 'delete-account';     

            return view('front.delete-account', $data);
        }
        public function deleteaccount(Request $request)
        {
            if($request->isMethod('post')){
                $postData           = $request->all();
                // Helper::pr($postData);
                $user_type         = $postData['user_type'];
                $Entityname         = $postData['entity_name'];
                $email             = $postData['email'];
                $phone           = $postData['phone'];
                $comment           = !empty($request->comment) ? $request->comment : null;
                $rules = [                                 
                    'user_type'           => 'required',
                    'entity_name'         => 'required',
                    'email'               => 'required|email',
                    'phone'               => 'required|numeric',                
                ];
                
                if ($this->validate($request, $rules)) {
                    $email_validation    = DeleteAccountRequest::where('email', $email)->first();               
                    if($email_validation){
                        $user_id           = $email_validation->id;
                        $fields = [
                            'user_type'       => $user_type,
                            'entity_name'     => $Entityname,
                            'email'           => $email,
                            'is_email_verify' => 1,
                            'is_phone_verify' => 1,
                            'phone'           => $phone,
                            'comments'         => $comment,
                            'created_at'      => date('Y-m-d H:i:s'), 
                            'updated_at'    => date('Y-m-d H:i:s'),             
                            'status'          => 1,                                  
                        ];
                        DeleteAccountRequest::where('id', $user_id)->update($fields);
                    }
                    $fields2 = [
                        'user_type'       => $user_type,
                        'entity_name'     => $Entityname,
                        'email'           => $email,
                        'is_email_verify' => 1,
                        'is_phone_verify' => 1,
                        'phone'           => $phone,
                        'comments'         => $comment,
                        'created_at'      => date('Y-m-d H:i:s'),                    
                        'status'          => 1,                                  
                    ];                
                    DeleteAccountRequest::insert($fields2);                
                    return redirect('delete-account')->with('success_message', 'Delete account request send successfully');
                } else {
                    return redirect('delete-account')->with('error_message', 'Please enter valid data');
                    
                }
            }        
        }
    /* delete account */
    /* page */
        public function page($slug){
            $data['generalSetting']             = GeneralSetting::find('1');
            $data['page']                       = Page::where('slug', '=', $slug)->first();
            
            $data['title']                      = (($data['page'])?$data['page']->page_title:"Page");
            $page_name                          = 'page-content';
            return view('front.page-content', $data);
        }
    /* page */
}
