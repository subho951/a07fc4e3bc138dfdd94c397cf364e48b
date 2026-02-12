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
use App\Models\CoreMember;

use Auth;
use Session;
use Helper;
use Hash;

class CoreController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Core',
            'controller'        => 'CoreController',
            'controller_route'  => 'core',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'core.list';
            $data['rows']                   = Core::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            $generalSetting             = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $request->validate([
                    'name'          => 'required|string|max:255|unique:users,name',
                    'photo'         => 'required|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'description'   => 'required|string|max:500',
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/core'), $photoName);

                $core = Core::create([
                    'name'        => $request->name,
                    'photo'       => $photoName,
                    'description' => $request->description,
                ]);
                $core_id = $core->id;

                $member_id = $request->member_id;
                CoreMember::where('core_id', '=', $core_id)->delete();
                if(!empty($member_id)){
                    for($m=0;$m<count($member_id);$m++){
                        $fields = [
                            'core_id' => $core_id,
                            'member_id' => $member_id[$m],
                        ];
                        CoreMember::insert($fields);
                    }
                }

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'core.add-edit';
            $data['row']                    = [];
            $data['memberIDs']              = [];
            $data['members']                = User::select('id', 'name', 'type')->where('status', '=', 1)->orderBy('name', 'ASc')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'core.add-edit';
            $data['row']                    = Core::where($this->data['primary_key'], '=', $id)->first();
            $data['members']                = User::select('id', 'name', 'type')->where('status', '=', 1)->orderBy('name', 'ASc')->get();
            $core_members                   = CoreMember::select('member_id')->where('status', '=', 1)->where('core_id', '=', $id)->get();

            $coreMembers                    = [];
            if($core_members){
                foreach($core_members as $core_member){
                    $coreMembers[] = $core_member->member_id;
                }
            }
            $data['memberIDs']              = $coreMembers;
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = Core::findOrFail($id);

                $request->validate([
                    'name'          => 'required|string|max:255|unique:users,name',
                    'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'description'   => 'required|string|max:500',
                    'member_id'     => 'required|array',
                ]);

                /** Photo Update */
                if ($request->hasFile('photo')) {
                    $oldPath = public_path('uploads/news/'.$member->photo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->photo->getClientOriginalName();
                    $request->photo->move(public_path('uploads/core'), $photoName);
                    $member->photo = $photoName;
                }

                $member->update([
                    'name'              => $request->name,
                    'description'       => $request->description,
                ]);

                $member_id = $request->member_id;
                CoreMember::where('core_id', '=', $id)->delete();
                if(!empty($member_id)){
                    for($m=0;$m<count($member_id);$m++){
                        $fields = [
                            'core_id' => $id,
                            'member_id' => $member_id[$m],
                        ];
                        CoreMember::insert($fields);
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
            Core::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Core::find($id);
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
    public function core_members($core_id){
        $core_id                        = Helper::decoded($core_id);
        $data['module']                 = $this->data;
        $data['core']                   = Core::where($this->data['primary_key'], '=', $core_id)->first();
        $title                          = $this->data['title'].' Member List : ' . (($data['core'])?$data['core']->name:'');
        $page_name                      = 'core.core-members';
        $data['rows']                   = CoreMember::select('users.*')
                                                    ->join('users', 'users.id', '=', 'core_members.member_id')
                                                    ->where('core_members.status', '=', 1)
                                                    ->where('core_members.core_id', '=', $core_id)
                                                    ->orderBy('core_members.id', 'DESC')
                                                    ->get();
        // Helper::pr($data['rows']);
        echo $this->admin_after_login_layout($title,$page_name,$data);
    }
}
