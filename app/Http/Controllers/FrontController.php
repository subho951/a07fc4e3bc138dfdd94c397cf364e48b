<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;
use Illuminate\Support\Facades\File;

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
            echo 'landing page';
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

            try {

                $id = Crypt::decryptString($token);

                $row = UserRegEvent::findOrFail($id);

                if(!empty($row)){
                    $member_id = $row->userid;
                    $event_id = $row->eventid;
                    $getMember = User::select('id', 'name', 'phone', 'photo', 'points')->where('id', '=', $member_id)->first();
                    if($getMember){
                        // Prevent duplicate entry
                        if($row->status == 1){
                            return "Already checked in!";
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
                                    'member_id' => $member_id,
                                    'credited_points' => $credited_points,
                                ];
                                Helper::pr($fields1);
                                UserPoint::insert($fields1);

                                User::where('id', '', $member_id)->update(['points' => $user_new_points]);
                            /* member point calculation */

                            /* core point calculation */

                            /* core point calculation */

                            return "Entry successful";
                        }
                    } else {
                        return "Member not found";
                    }
                } else {
                    return "Event registration not found";
                }
            } catch (\Exception $e) {
                return "Invalid QR Code";
            }
        }
    /* event checkin */
}
