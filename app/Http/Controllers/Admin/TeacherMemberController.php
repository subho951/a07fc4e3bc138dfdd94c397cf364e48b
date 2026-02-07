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
use App\Models\Institute;
use Auth;
use Session;
use Helper;
use Hash;

class TeacherMemberController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Teacher Member',
            'controller'        => 'TeacherMemberController',
            'controller_route'  => 'teacher-member',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'teacher-member.list';
            $data['rows']                   = User::select(
                                                    'users.*',
                                                    'institutes.name as institute_name'
                                                )
                                                ->join('institutes', 'institutes.id', '=', 'users.institute_id')
                                                ->where('users.status', '!=', 3)
                                                ->where('users.type', 3)
                                                ->orderBy('users.id', 'DESC')
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
                    'name'         => 'required|string|max:255|unique:users,name',
                    'email'        => 'required|email|max:255|unique:users,email',
                    'phone'        => 'required|digits:10|unique:users,phone',
                    'password'     => 'required|min:6',
                    'designation'  => 'required|string|max:255',
                    'photo'        => 'required|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'dob'          => 'required|date',
                    'biodata'      => 'required|file|mimes:pdf|max:' . $generalSetting->document_size,
                    'institute_id' => 'required',
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/user'), $photoName);

                /** biodata Upload */
                $biodataName = time().'_'.$request->biodata->getClientOriginalName();
                $request->biodata->move(public_path('uploads/user'), $biodataName);

                User::create([
                    'type'          => 3,
                    'name'          => $request->name,
                    'email'         => $request->email,
                    'phone'         => $request->phone,
                    'password'      => Hash::make($request->password),
                    'designation'   => $request->designation,
                    'photo'         => $photoName,
                    'dob'           => $request->dob,
                    'institute_id'  => $request->institute_id,
                    'biodata'       => $biodataName,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'teacher-member.add-edit';
            $data['row']                    = [];
            $data['institutes']             = Institute::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'teacher-member.add-edit';
            $data['row']                    = User::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');
            $data['institutes']             = Institute::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();

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
                    'biodata'      => 'nullable|file|mimes:pdf|max:' . $generalSetting->document_size,
                    'institute_id' => 'required',
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

                /** biodata Update */
                if ($request->hasFile('biodata')) {
                    $oldPath2 = public_path('uploads/user/'.$member->biodata);
                    if (File::exists($oldPath2)) {
                        File::delete($oldPath2);
                    }

                    $biodataName = time().'_'.$request->biodata->getClientOriginalName();
                    $request->biodata->move(public_path('uploads/user'), $biodataName);
                    $member->biodata = $biodataName;
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
                    'institute_id'  => $request->institute_id,
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
