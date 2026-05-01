<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
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
        public function eventCheckin(Request $request, $token)
        {
            $generalSetting = GeneralSetting::find(1);

            try {
                $id = Crypt::decryptString(urldecode($token));
            } catch (\Throwable $e) {
                return "Invalid QR Code";
            }

            $row = UserRegEvent::findOrFail($id);
            $member_id = $row->userid;
            $event_id = $row->eventid;
            $getEvent = Event::select('id', 'title', 'venue', 'event_date', 'photo')->where('id', '=', $event_id)->first();
            $getMember = User::select('id', 'name', 'phone', 'photo', 'points', 'core_id')->where('id', '=', $member_id)->first();

            if(!$getMember){
                return "Member not found";
            }

            $data = [
                'user_event' => $row,
                'member' => $getMember,
                'event' => $getEvent,
                'checkin_pending' => false,
            ];

            if($request->isMethod('post')){
                $latitude = trim((string) $request->input('latitude', ''));
                $longitude = trim((string) $request->input('longitude', ''));
                $location = trim((string) $request->input('location', ''));

                if($latitude === '' || $longitude === '' || !is_numeric($latitude) || !is_numeric($longitude)){
                    $data['checkin_msg'] = 'Location access is required to complete check-in.';
                    $data['checkin_error'] = true;
                    return view('front.event-checkin', $data);
                }

                if($row->status == 1){
                    return redirect()->to(url()->current())->with('checkin_msg', 'Already checked in!');
                }

                if($location === ''){
                    $location = $this->resolveLocationFromCoordinates($latitude, $longitude);
                }

                $row->status = 1;
                $row->entry_timestamp = now();
                $row->latitude = $latitude;
                $row->longitude = $longitude;
                $row->location = $location;
                $row->save();

                /* member point calculation */
                    $attendancePointCredit = $this->creditUserEventAttendancePoints($getMember, $getEvent, $generalSetting);
                    $credited_points = $attendancePointCredit['credited_points'];
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

                    if($attendancePointCredit['is_back_to_back_bonus']){
                        $this->creditCoreBackToBackBonusPoints($member_id, $core_id, $event_id, $getMember->name);
                    }
                /* core point calculation */

                // push notification send
                    $users = [];
                    $getTokens = UserDevice::select('fcm_token')->where('user_id', '=', $member_id)->where('published', '=', 1)->where('fcm_token', '!=', '')->get();
                    if($getTokens){
                        foreach($getTokens as $getToken){
                            $token = $getToken->fcm_token;

                            $title = 'Event Checkin';
                            $message = 'You are successfully attended the event ' . (($getEvent)?$getEvent->title:'') . ' at ' . date('d.m.Y h:i A');

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
                                    $message = $getMember->name . ' successfully attended the event ' . (($getEvent)?$getEvent->title:'') . ' at ' . date('d.m.Y h:i A');

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

                return redirect()->to(url()->current())->with('checkin_msg', 'Entry successful');
            }

            if($row->status == 1){
                $data['checkin_msg'] = session('checkin_msg', 'Already checked in!');
                return view('front.event-checkin', $data);
            }

            $data['checkin_msg'] = session('checkin_msg', 'Fetching your location...');
            $data['checkin_pending'] = true;
            return view('front.event-checkin', $data);
        }

        private function resolveLocationFromCoordinates($latitude, $longitude)
        {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => ((config('app.name') != '') ? config('app.name') : 'Laravel') . ' Event Checkin',
                    'Accept-Language' => 'en',
                ])->timeout(10)->get('https://nominatim.openstreetmap.org/reverse', [
                    'format' => 'jsonv2',
                    'lat' => $latitude,
                    'lon' => $longitude,
                ]);

                if($response->successful()){
                    $location = $response->json('display_name');
                    if(!empty($location)){
                        return $location;
                    }
                }
            } catch (\Throwable $e) {
                // Fall back to coordinates below.
            }

            return 'Latitude: ' . $latitude . ', Longitude: ' . $longitude;
        }

        private function creditUserEventAttendancePoints($member, $event, $generalSetting)
        {
            if(!$member || !$event || !$generalSetting){
                return [
                    'credited_points' => 0,
                    'is_back_to_back_bonus' => false,
                ];
            }

            $member_id = $member->id;
            $event_id = $event->id;
            $attendancePoint = (int) $generalSetting->individual_attn_point;
            $backToBackCount = (int) $generalSetting->individual_backtoback_attn_count;
            $backToBackPoint = (int) $generalSetting->individual_backtoback_attn_point;
            $credited_points = $attendancePoint;
            $isBackToBackAttendance = false;

            if($backToBackCount > 1 && $backToBackPoint > 0){
                $eventIds = Event::where(function($query) use ($event){
                                    $query->where('event_date', '<', $event->event_date)
                                        ->orWhere(function($query) use ($event){
                                            $query->where('event_date', '=', $event->event_date)
                                                ->where('id', '<=', $event->id);
                                        });
                                })
                                ->orderBy('event_date', 'DESC')
                                ->orderBy('id', 'DESC')
                                ->pluck('id')
                                ->toArray();

                $attendedEventIds = UserRegEvent::where('userid', '=', $member_id)
                                                ->whereIn('eventid', $eventIds)
                                                ->where('status', '=', 1)
                                                ->pluck('eventid')
                                                ->toArray();

                $attendedEventIds = array_flip($attendedEventIds);
                $backToBackAttendanceStreak = 0;

                foreach($eventIds as $eventId){
                    if(!isset($attendedEventIds[$eventId])){
                        break;
                    }

                    $backToBackAttendanceStreak++;
                }

                $isBackToBackAttendance = ($backToBackAttendanceStreak > 0 && $backToBackAttendanceStreak % $backToBackCount == 0);
            } elseif($backToBackCount == 1 && $backToBackPoint > 0){
                $isBackToBackAttendance = true;
            }

            if($isBackToBackAttendance){
                $credited_points += $backToBackPoint;
            }

            $note = $credited_points . ' points credited for event attended';
            if($isBackToBackAttendance){
                $note = $credited_points . ' points credited for event attended with back to back attendance bonus';
            }

            UserPoint::insert([
                'member_id'         => $member_id,
                'event_id'          => $event_id,
                'credited_points'   => $credited_points,
                'note'              => $note,
            ]);

            User::where('id', '=', $member_id)->increment('points', $credited_points);

            return [
                'credited_points' => $credited_points,
                'is_back_to_back_bonus' => $isBackToBackAttendance,
            ];
        }

        private function creditCoreBackToBackBonusPoints($member_id, $core_id, $event_id, $member_name = '')
        {
            $coreBonusPoint = 5;

            if($core_id <= 0){
                return false;
            }

            $getCore = Core::where('id', '=', $core_id)->first();
            if(!$getCore){
                return false;
            }

            $memberNameText = (($member_name != '') ? ' of ' . $member_name : '');

            CorePoint::insert([
                'core_id'           => $core_id,
                'member_id'         => $member_id,
                'event_id'          => $event_id,
                'meeting_id'        => 0,
                'credited_points'   => $coreBonusPoint,
                'note'              => $coreBonusPoint . ' points credited for member back to back attendance bonus' . $memberNameText,
            ]);

            Core::where('id', '=', $core_id)->increment('points', $coreBonusPoint);

            return true;
        }
    /* event checkin */
    /* back to back attendance bonus cron */
        public function backToBackAttendanceBonusCron(Request $request)
        {
            $secureCronKey = env('CRON_KEY', '');
            if($secureCronKey != '' && $request->query('key') != $secureCronKey){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid cron key.',
                ], 401);
            }

            $generalSetting = GeneralSetting::find(1);
            $backToBackCount = (($generalSetting) ? (int) $generalSetting->individual_backtoback_attn_count : 0);
            $backToBackPoint = (($generalSetting) ? (int) $generalSetting->individual_backtoback_attn_point : 0);
            $memberIdFilter = (int) $request->query('member_id', 0);

            $report = [
                'back_to_back_count' => $backToBackCount,
                'back_to_back_bonus_point' => $backToBackPoint,
                'member_id_filter' => $memberIdFilter,
                'members_checked' => 0,
                'bonus_credited_count' => 0,
                'bonus_skipped_count' => 0,
                'core_bonus_credited_count' => 0,
                'total_core_points_credited' => 0,
                'total_points_credited' => 0,
                'credited' => [],
            ];

            if(!$generalSetting || $backToBackCount <= 0 || $backToBackPoint <= 0){
                return response()->json([
                    'status' => false,
                    'message' => 'Back to back attendance bonus settings are not configured.',
                    'report' => $report,
                ], 422);
            }

            $memberQuery = UserRegEvent::where('status', '=', 1);
            if($memberIdFilter > 0){
                $memberQuery->where('userid', '=', $memberIdFilter);
            }

            $memberIds = $memberQuery->pluck('userid')
                                    ->unique()
                                    ->values()
                                    ->toArray();

            if(empty($memberIds)){
                return response()->json([
                    'status' => true,
                    'message' => 'Back to back attendance bonus cron executed successfully.',
                    'report' => $report,
                ]);
            }

            $report['members_checked'] = count($memberIds);

            foreach($memberIds as $memberId){
                $attendedEvents = Event::select('events.id', 'events.title', 'events.event_date')
                                        ->join('user_reg_events', 'user_reg_events.eventid', '=', 'events.id')
                                        ->where('user_reg_events.userid', '=', $memberId)
                                        ->where('user_reg_events.status', '=', 1)
                                        ->groupBy('events.id', 'events.title', 'events.event_date')
                                        ->orderBy('events.event_date', 'ASC')
                                        ->orderBy('events.id', 'ASC')
                                        ->get();

                $backToBackAttendanceStreak = 0;

                foreach($attendedEvents as $event){
                    $backToBackAttendanceStreak++;

                    if($backToBackAttendanceStreak % $backToBackCount != 0){
                        continue;
                    }

                    $alreadyCredited = UserPoint::where('member_id', '=', $memberId)
                                                ->where('event_id', '=', $event->id)
                                                ->where('note', 'LIKE', '%back to back attendance bonus%')
                                                ->exists();

                    if($alreadyCredited){
                        $report['bonus_skipped_count']++;
                        continue;
                    }

                    $note = $backToBackPoint . ' points credited for back to back attendance bonus';

                    UserPoint::insert([
                        'member_id'         => $memberId,
                        'event_id'          => $event->id,
                        'credited_points'   => $backToBackPoint,
                        'note'              => $note,
                    ]);

                    User::where('id', '=', $memberId)->increment('points', $backToBackPoint);

                    $member = User::select('id', 'name', 'core_id')->where('id', '=', $memberId)->first();
                    $coreBonusCredited = false;
                    if($member){
                        $coreBonusCredited = $this->creditCoreBackToBackBonusPoints($member->id, $member->core_id, $event->id, $member->name);
                    }

                    if($coreBonusCredited){
                        $report['core_bonus_credited_count']++;
                        $report['total_core_points_credited'] += 5;
                    }

                    $report['bonus_credited_count']++;
                    $report['total_points_credited'] += $backToBackPoint;
                    $report['credited'][] = [
                        'member_id' => $memberId,
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'credited_points' => $backToBackPoint,
                        'core_bonus_points_credited' => (($coreBonusCredited) ? 5 : 0),
                    ];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Back to back attendance bonus cron executed successfully.',
                'report' => $report,
            ]);
        }
    /* back to back attendance bonus cron */
    /* event notification cron */
        public function eventNotificationCron(Request $request)
        {
            $now = Carbon::now('Asia/Calcutta');
            $today = $now->format('Y-m-d');

            $secureCronKey = env('CRON_KEY', '');
            if($secureCronKey != '' && $request->query('key') != $secureCronKey){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid cron key.',
                ], 401);
            }

            $todayEvents = Event::select('id', 'title', 'venue', 'event_date', 'event_time', 'photo')
                                ->where('status', '=', 1)
                                ->whereDate('event_date', '=', $today)
                                ->orderBy('event_time', 'ASC')
                                ->get();

            $broadcastMembers = User::select('id', 'name')
                                    ->where('status', '=', 1)
                                    ->orderBy('name', 'ASC')
                                    ->get();

            $broadcastRecipientIds = $broadcastMembers->pluck('id')->values()->all();
            $broadcastDevices = collect();
            if(!empty($broadcastRecipientIds)){
                $broadcastDevices = UserDevice::select('user_id', 'fcm_token')
                                            ->whereIn('user_id', $broadcastRecipientIds)
                                            ->where('published', '=', 1)
                                            ->where('fcm_token', '!=', '')
                                            ->get()
                                            ->groupBy('user_id');
            }

            $report = [
                'date' => $today,
                'total_events' => $todayEvents->count(),
                'broadcast_recipients' => $broadcastMembers->count(),
                'push_sent' => 0,
                'push_skipped' => 0,
                'errors' => [],
            ];

            if($todayEvents->isEmpty()){
                return response()->json([
                    'status' => true,
                    'message' => 'Event cron executed successfully.',
                    'report' => $report,
                ]);
            }

            foreach($todayEvents as $event){
                $eventTitle = (($event->title != '') ? $event->title : 'Event');
                $eventTime = '';
                if(!empty($event->event_time)){
                    try {
                        $eventTime = Carbon::parse($event->event_time)->format('h:i A');
                    } catch (\Throwable $e) {
                        $eventTime = '';
                    }
                }

                $pushTitle = 'Event Reminder - ' . $eventTitle;
                $pushMessage = 'There is an event today: ' . $eventTitle;
                if($eventTime != ''){
                    $pushMessage .= ' at ' . $eventTime;
                }
                if(!empty($event->venue)){
                    $pushMessage .= ' in ' . $event->venue;
                }
                $pushMessage .= '.';

                $eventImage = (!empty($event->photo) ? env('UPLOADS_URL').'event/'.$event->photo : null);

                foreach($broadcastMembers as $member){
                    $alreadyPushedToday = Notification::where('to_users', '=', $member->id)
                                                        ->where('title', '=', $pushTitle)
                                                        ->where('description', '=', $pushMessage)
                                                        ->whereDate('send_timestamp', '=', $today)
                                                        ->exists();

                    if($alreadyPushedToday){
                        $report['push_skipped']++;
                        continue;
                    }

                    $tokenSentCount = 0;
                    $tokens = $broadcastDevices->get($member->id, collect());

                    if($tokens->isNotEmpty()){
                        foreach($tokens as $device){
                            try {
                                FirebaseService::sendNotification(
                                    $device->fcm_token,
                                    $pushTitle,
                                    $pushMessage,
                                    [
                                        'event_id' => $event->id,
                                        'event_date' => (string) $event->event_date,
                                        'event_time' => (string) $event->event_time,
                                        'type' => 'event',
                                    ],
                                    $eventImage
                                );
                                $tokenSentCount++;
                            } catch (\Throwable $e) {
                                $report['errors'][] = 'Push failed for member ID '.$member->id.' and event ID '.$event->id.': '.$e->getMessage();
                            }
                        }
                    }

                    if($tokenSentCount > 0){
                        Notification::insert([
                            'title'             => $pushTitle,
                            'description'       => $pushMessage,
                            'to_users'          => $member->id,
                            'users'             => json_encode([$member->id]),
                            'is_send'           => 1,
                            'send_timestamp'    => $now->format('Y-m-d H:i:s'),
                            'status'            => 1,
                        ]);
                        $report['push_sent']++;
                    } else {
                        $report['push_skipped']++;
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Event cron executed successfully.',
                'report' => $report,
            ]);
        }
    /* event notification cron */
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
                                ->orderBy('name', 'ASC')
                                ->get();
            $birthdayUserIds = $birthdayUsers->pluck('id')->values()->all();
            $birthdayNames = $birthdayUsers->pluck('name')->filter()->values()->all();
            $birthdayNamesText = $this->formatBirthdayNames($birthdayNames);
            $birthdayCount = count($birthdayUserIds);
            $pushTitle = 'Birthday Celebration Today';
            $pushMessage = ($birthdayCount === 1)
                ? 'Today we are celebrating the birthday of ' . $birthdayNamesText . '.'
                : 'Today we are celebrating the birthdays of ' . $birthdayNamesText . '.';
            $emailSubject = $siteName . ' :: ' . $pushTitle;
            $emailWishMessage = 'Please join us in wishing ' . $birthdayNamesText . ' a wonderful birthday and an amazing year ahead.';

            $broadcastMembers = User::select('id', 'name', 'email')
                                    ->where('status', '=', 1)
                                    ->orderBy('name', 'ASC')
                                    ->get();
            $broadcastRecipientIds = $broadcastMembers->pluck('id')->values()->all();
            $broadcastDevices = collect();
            if(!empty($broadcastRecipientIds)){
                $broadcastDevices = UserDevice::select('user_id', 'fcm_token')
                                            ->whereIn('user_id', $broadcastRecipientIds)
                                            ->where('published', '=', 1)
                                            ->where('fcm_token', '!=', '')
                                            ->get()
                                            ->groupBy('user_id');
            }

            $report = [
                'date' => $today,
                'total_birthday_users' => $birthdayUsers->count(),
                'broadcast_recipients' => $broadcastMembers->count(),
                'birthday_names' => (($birthdayCount > 0) ? $birthdayNamesText : ''),
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

            foreach($broadcastMembers as $member){
                $memberEmail = strtolower(trim((string) $member->email));

                $alreadyPushedToday = Notification::where('to_users', '=', $member->id)
                                                    ->where('title', '=', $pushTitle)
                                                    ->where('description', '=', $pushMessage)
                                                    ->whereDate('send_timestamp', '=', $today)
                                                    ->exists();

                if(!$alreadyPushedToday){
                    $tokenSentCount = 0;
                    $tokens = $broadcastDevices->get($member->id, collect());

                    if($tokens->isNotEmpty()){
                        foreach($tokens as $device){
                            try {
                                FirebaseService::sendNotification(
                                    $device->fcm_token,
                                    $pushTitle,
                                    $pushMessage,
                                    [
                                        'type' => 'birthday',
                                        'birthday_count' => (string) $birthdayCount,
                                        'birthday_names' => $birthdayNamesText,
                                        'birthday_user_ids' => json_encode($birthdayUserIds),
                                    ]
                                );
                                $tokenSentCount++;
                            } catch (\Throwable $e) {
                                $report['errors'][] = 'Push failed for member ID '.$member->id.': '.$e->getMessage();
                            }
                        }
                    }

                    if($tokenSentCount > 0){
                        Notification::insert([
                            'title'             => $pushTitle,
                            'description'       => $pushMessage,
                            'to_users'          => $member->id,
                            'users'             => json_encode([$member->id]),
                            'is_send'           => 1,
                            'send_timestamp'    => $now->format('Y-m-d H:i:s'),
                            'status'            => 1,
                        ]);
                        $report['push_sent']++;
                    } else {
                        $report['push_skipped']++;
                    }
                } else {
                    $report['push_skipped']++;
                }

                if($memberEmail != ''){
                    $alreadyEmailedToday = EmailLog::where('email', '=', $memberEmail)
                                                    ->where('subject', '=', $emailSubject)
                                                    ->whereDate('created_at', '=', $today)
                                                    ->exists();

                    if(!$alreadyEmailedToday){
                        $mailData = [
                            'name' => $birthdayNamesText,
                            'site_name' => $siteName,
                            'generalSetting' => $generalSetting,
                            'theme_color' => $themeColor,
                            'font_color' => $fontColor,
                            'font_family' => $fontFamily,
                            'wish_message' => $emailWishMessage,
                        ];

                        $message = '';
                        try {
                            $message = view('email-templates.birthday-wish', $mailData)->render();
                            // $mailStatus = $this->sendMail($memberEmail, $emailSubject, $message);
                        } catch (\Throwable $e) {
                            $mailStatus = false;
                            $report['errors'][] = 'Email sending failed for member ID '.$member->id.': '.$e->getMessage();
                        }

                        // EmailLog::insert([
                        //     'name' => (($member->name != '') ? $member->name : 'Member'),
                        //     'email' => $memberEmail,
                        //     'subject' => $emailSubject,
                        //     'message' => $message,
                        //     'status' => (($mailStatus) ? 1 : 0),
                        //     'created_at' => $now->format('Y-m-d H:i:s'),
                        //     'updated_at' => $now->format('Y-m-d H:i:s'),
                        // ]);

                        if($mailStatus){
                            $report['email_sent']++;
                        } else {
                            $report['email_skipped']++;
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
        private function formatBirthdayNames(array $names)
        {
            $names = array_values(array_filter(array_map('trim', $names)));

            if(empty($names)){
                return 'our members';
            }

            if(count($names) === 1){
                return $names[0];
            }

            if(count($names) === 2){
                return $names[0].' and '.$names[1];
            }

            $lastName = array_pop($names);
            return implode(', ', $names).' and '.$lastName;
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
