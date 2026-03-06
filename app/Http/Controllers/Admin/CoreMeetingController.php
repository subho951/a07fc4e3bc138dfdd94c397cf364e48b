<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\Core;
use App\Models\CoreMeeting;
use App\Models\CorePoint;

use Auth;
use Session;
use Helper;
use Hash;

class CoreMeetingController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Core Meeting',
            'controller'        => 'CoreMeetingController',
            'controller_route'  => 'core-meeting',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'core-meeting.list';
            $data['rows']                   = CoreMeeting::select(
                                                    'core_meetings.*', 'cores.name as core_name'
                                                )
                                                ->join('cores', 'cores.id', '=', 'core_meetings.core_id')
                                                ->where('core_meetings.status', '!=', 3)
                                                ->orderBy('core_meetings.id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $generalSetting             = GeneralSetting::find('1');
            $data['module']             = $this->data;
            if($request->isMethod('post')){
                $request->validate([
                    'core_id'                   => 'required|integer',
                    'meeting_type'              => 'required|string',
                    'from_date'                 => 'required|string',
                    'to_date'                   => 'required|string',
                    'venue'                     => 'required|string',
                    'short_description'         => 'required|string',
                ]);

                CoreMeeting::create([
                    'core_id'                   => $request->core_id,
                    'meeting_type'              => $request->meeting_type,
                    'from_date'                 => $request->from_date,
                    'to_date'                   => $request->to_date,
                    'venue'                     => $request->venue,
                    'short_description'         => $request->short_description,
                    'attendance'                => $request->attendance,
                    'quorum_percent'            => $request->quorum_percent,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'core-meeting.add-edit';
            $data['row']                    = [];
            $data['cores']                  = Core::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'core-meeting.add-edit';
            $data['row']                    = CoreMeeting::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');
            $data['cores']                  = Core::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();

            if($request->isMethod('post')){
                $member = CoreMeeting::findOrFail($id);

                $request->validate([
                    'core_id'                   => 'required|integer',
                    'meeting_type'              => 'required|string',
                    'from_date'                 => 'required|string',
                    'to_date'                   => 'required|string',
                    'venue'                     => 'required|string',
                    'short_description'         => 'required|string',
                ]);

                $member->update([
                    'core_id'                   => $request->core_id,
                    'meeting_type'              => $request->meeting_type,
                    'from_date'                 => $request->from_date,
                    'to_date'                   => $request->to_date,
                    'venue'                     => $request->venue,
                    'short_description'         => $request->short_description,
                    'attendance'                => $request->attendance,
                    'quorum_percent'            => $request->quorum_percent,
                ]);

                if($request->attendance > 0 && $request->quorum_percent > 0){
                    $meeting_id = $id;
                    $core_id = $request->core_id;

                    $generalSetting = GeneralSetting::find(1);
                    $core_meeting_inbound_point = $generalSetting->core_meeting_inbound_point;
                    $core_meeting_min_attn_percent = $generalSetting->core_meeting_min_attn_percent;
                    $core_meeting_local_outbound_point = $generalSetting->core_meeting_local_outbound_point;
                    $core_meeting_outbound_point = $generalSetting->core_meeting_outbound_point;

                    if($request->quorum_percent >= $core_meeting_min_attn_percent){
                        $getCore = Core::where('id', '=', $core_id)->first();
                        if($getCore){
                            $credited_points = 0;
                            if($request->meeting_type == 'INBOUND'){
                                $credited_points = $core_meeting_inbound_point;
                            } elseif($request->meeting_type == 'LOCAL INBOUND'){
                                $credited_points = $core_meeting_local_outbound_point;
                            } elseif($request->meeting_type == 'OUTBOUND'){
                                $credited_points = $core_meeting_outbound_point;
                            }
                            $fields2 = [
                                'core_id'           => $core_id,
                                'member_id'         => 0,
                                'event_id'          => 0,
                                'meeting_id'        => $meeting_id,
                                'credited_points'   => $credited_points,
                                'note'              => $credited_points . ' points credited for meeting organize',
                            ];
                            CorePoint::insert($fields2);

                            $opening_core_point = (int) $getCore->points;
                            $core_new_points = ($opening_core_point + $credited_points);
                            Core::where('id', '=', $core_id)->update(['points' => $core_new_points]);
                        } else {
                            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('error_message', $this->data['title'].' not found !!!');
                        }
                    } else {
                        return redirect('admin/'.$this->data['controller_route'] . "/list")->with('error_message', $this->data['title'].' meeting quotum percentage not greater than ' . $core_meeting_min_attn_percent);
                    }
                }

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' updated successfully !!!');
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    /* delete */
        public function delete(Request $request, $id){
            $id                             = Helper::decoded($id);
            $fields = [
                'status'             => 3
            ];
            CoreMeeting::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = CoreMeeting::find($id);
            if ($model->status == 1)
            {
                $model->status  = 0;
                $msg            = 'deactivated';
            } else {
                $model->status  = 1;
                $msg            = 'activated';
            }            
            $model->save();
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' '.$msg.' successfully !!!');
        }
    /* change status */
}
