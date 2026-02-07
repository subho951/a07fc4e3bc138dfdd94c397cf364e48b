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
use Auth;
use Session;
use Helper;
use Hash;

class SocietyMemberController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Society Member',
            'controller'        => 'SocietyMemberController',
            'controller_route'  => 'society-member',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'society-member.list';
            $data['rows']                   = User::where('status', '!=', 3)->where('type', '=', 1)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            $generalSetting             = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $request->validate([
                    'name'         => 'required|string|max:255|unique:users,name',
                    'email'        => 'required|email|max:255|unique:users,email',
                    'phone'        => 'required|digits:10|unique:users,phone',
                    'password'     => 'required|min:6',
                    'designation'  => 'required|string|max:255',
                    'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'dob'          => 'required|date',
                    'short_profile'=> 'required|string|max:500',
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/user'), $photoName);

                User::create([
                    'type'          => 1,
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'password'      => Hash::make($request->password),
                    'designation'   => $request->designation,
                    'photo'         => $photoName,
                    'dob'           => $request->dob,
                    'short_profile' => $request->short_profile,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'society-member.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'society-member.add-edit';
            $data['row']                    = User::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = User::findOrFail($id);

                $request->validate([
                    'name'         => 'required|string|max:255|unique:users,name,'.$member->id,
                    'email'        => 'required|email|max:255|unique:users,email,'.$member->id,
                    'phone'        => 'required|digits:10|unique:users,phone,'.$member->id,
                    'password'     => 'nullable|min:6',
                    'designation'  => 'required|string|max:255',
                    'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'dob'          => 'required|date',
                    'short_profile'=> 'required|string|max:500',
                ]);

                /** Photo Update */
                if ($request->hasFile('photo')) {
                    $oldPath = public_path('uploads/user/'.$member->photo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->photo->getClientOriginalName();
                    $request->photo->move(public_path('uploads/user'), $photoName);
                    $member->photo = $photoName;
                }

                /** Password Update */
                if ($request->filled('password')) {
                    $member->password = Hash::make($request->password);
                }

                $member->update([
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'designation'   => $request->designation,
                    'dob'           => $request->dob,
                    'short_profile' => $request->short_profile,
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
            User::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = User::find($id);
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
