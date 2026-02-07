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
use App\Models\Event;
use Auth;
use Session;
use Helper;
use Hash;

class EventController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Event',
            'controller'        => 'EventController',
            'controller_route'  => 'event',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'event.list';
            $data['rows']                   = Event::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $generalSetting             = GeneralSetting::find('1');
            $data['module']             = $this->data;
            if($request->isMethod('post')){
                $request->validate([
                    'title'                 => 'required|string|max:255',
                    'description'           => 'required|max:500',
                    'venue'                 => 'required',
                    'event_date'            => 'required|date',
                    'photo'                 => 'required|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'video'                 => 'required|mimes:mp4,mov,avi,wmv|max:' . $generalSetting->video_size,
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/event'), $photoName);

                /** video Upload */
                $videoName = time().'_'.$request->video->getClientOriginalName();
                $request->video->move(public_path('uploads/event'), $videoName);

                Event::create([
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'venue'             => $request->venue,
                    'event_date'        => $request->event_date,
                    'photo'             => $photoName,
                    'video'             => $videoName,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'event.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'event.add-edit';
            $data['row']                    = Event::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = Event::findOrFail($id);

                $request->validate([
                    'title'                 => 'required|string|max:255',
                    'description'           => 'required|max:500',
                    'venue'                 => 'required',
                    'event_date'            => 'required|date',
                    'photo'                 => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'video'                 => 'nullable|mimes:mp4,mov,avi,wmv|max:' . $generalSetting->video_size,
                ]);

                /** Photo Update */
                if ($request->hasFile('photo')) {
                    $oldPath = public_path('uploads/event/'.$member->photo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->photo->getClientOriginalName();
                    $request->photo->move(public_path('uploads/event'), $photoName);
                    $member->photo = $photoName;
                }

                /** video Update */
                if ($request->hasFile('video')) {
                    $oldPath2 = public_path('uploads/event/'.$member->video);
                    if (File::exists($oldPath2)) {
                        File::delete($oldPath2);
                    }

                    $videoName = time().'_'.$request->video->getClientOriginalName();
                    $request->video->move(public_path('uploads/event'), $videoName);
                    $member->video = $videoName;
                }

                $member->update([
                    'title'             => $request->title,
                    'description'       => $request->description,
                    'venue'             => $request->venue,
                    'event_date'        => $request->event_date,
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
            Event::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Event::find($id);
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
