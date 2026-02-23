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
