<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Core;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use App\Models\GeneralSetting;
use App\Models\Industry;
use App\Models\Interest;
use App\Models\User;
use App\Models\UserPoint;

use Auth;
use Session;
use Helper;
use Hash;

class MemberController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Member',
            'controller'        => 'MemberController',
            'controller_route'  => 'member',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'member.list';
            $data['rows']                   = User::select(
                                                    'users.*'
                                                )
                                                ->where('users.status', '!=', 3)
                                                ->where('users.type', 1)
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
                    'company_name' => 'required|string|max:255',
                    'designation'  => 'required|string|max:255',
                    'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'dob'          => 'required|date',
                    'doj'          => 'required',
                    'profession'   => 'required',
                    'address'      => 'required',
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/user'), $photoName);                

                User::create([
                    'type'                          => 1,
                    'name'                          => $request->name,
                    'email'                         => $request->email,
                    'phone'                         => $request->phone,
                    'company_name'                  => $request->company_name,
                    'designation'                   => $request->designation,
                    'photo'                         => $photoName,
                    'dob'                           => $request->dob,
                    'doj'                           => $request->doj,
                    'doa'                           => $request->doa,
                    'core_id'                       => $request->core_id,
                    'spouse_name'                   => $request->spouse_name,
                    'profession'                    => $request->profession,
                    'alumni'                        => $request->alumni,
                    'industry_id'                   => (($request->industry_id != '')?json_encode($request->industry_id):[]),
                    'interest_id'                   => (($request->interest_id != '')?json_encode($request->interest_id):[]),
                    'address'                       => $request->address,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'member.add-edit';
            $data['row']                    = [];
            $data['cores']                  = Core::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['industries']             = Industry::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['interests']              = Interest::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'member.add-edit';
            $data['row']                    = User::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');
            $data['cores']                  = Core::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['industries']             = Industry::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['interests']              = Interest::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();

            if($request->isMethod('post')){
                $member = User::findOrFail($id);

                $request->validate([
                    'name'         => 'required|string|max:255|unique:users,name,'.$member->id,
                    'email'        => 'required|email|max:255|unique:users,email,'.$member->id,
                    'phone'        => 'required|digits:10|unique:users,phone,'.$member->id,
                    'company_name' => 'required|string|max:255',
                    'designation'  => 'required|string|max:255',
                    'photo'        => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'dob'          => 'required|date',
                    'doj'          => 'required',
                    'profession'   => 'required',
                    'address'      => 'required',
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

                $member->update([
                    'name'                          => $request->name,
                    'email'                         => $request->email,
                    'phone'                         => $request->phone,
                    'company_name'                  => $request->company_name,
                    'designation'                   => $request->designation,
                    'dob'                           => $request->dob,
                    'doj'                           => $request->doj,
                    'doa'                           => $request->doa,
                    'core_id'                       => $request->core_id,
                    'spouse_name'                   => $request->spouse_name,
                    'profession'                    => $request->profession,
                    'alumni'                        => $request->alumni,
                    'industry_id'                   => (($request->industry_id != '')?json_encode($request->industry_id):[]),
                    'interest_id'                   => (($request->interest_id != '')?json_encode($request->interest_id):[]),
                    'address'                       => $request->address,
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
    /* member points */
        public function points_history(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $page_name                      = 'member.points-history';
            $data['member']                 = User::where($this->data['primary_key'], '=', $id)->first();
            $title                          = $this->data['title'].' Points History : ' . (($data['member'])?$data['member']->name . '('. $data['member']->phone .')':'');
            $data['rows']                   = UserPoint::where('member_id', '=', $id)->orderBy('id', 'DESC')->get();

            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* member points */
}
