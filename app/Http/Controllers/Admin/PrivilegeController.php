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
use App\Models\Category;
use App\Models\Privilege;
use Auth;
use Session;
use Helper;
use Hash;

class PrivilegeController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Privilege',
            'controller'        => 'PrivilegeController',
            'controller_route'  => 'privileges',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'privilege.list';
             $data['rows']                   = Privilege::select(
                                                    'privileges.*',
                                                    'categories.name as category_name'
                                                )
                                                ->join('categories', 'categories.id', '=', 'privileges.category_id')
                                                ->where('privileges.status', '!=', 3)
                                                ->orderBy('privileges.id', 'DESC')
                                                ->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            $generalSetting             = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $request->validate([
                    'name'                          => 'required|string',
                    'short_description'             => 'required|string',
                    'category_id'                   => 'required',
                    'logo'                          => 'required|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->logo->getClientOriginalName();
                $request->logo->move(public_path('uploads/privilege'), $photoName);

                Privilege::create([
                    'category_id'                   => $request->category_id,
                    'name'                          => $request->name,
                    'short_description'             => $request->short_description,
                    'logo'                          => $photoName,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'privilege.add-edit';
            $data['row']                    = [];
            $data['cats']                   = Category::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'privilege.add-edit';
            $data['row']                    = Privilege::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');
            $data['cats']                   = Category::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();

            if($request->isMethod('post')){
                $member = Privilege::findOrFail($id);

                $request->validate([
                    'name'                          => 'required',
                    'short_description'             => 'required|string',
                    'category_id'                   => 'required',
                    'logo'                          => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                ]);

                /** Photo Update */
                if ($request->hasFile('logo')) {
                    $oldPath = public_path('uploads/privilege/'.$member->logo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->logo->getClientOriginalName();
                    $request->logo->move(public_path('uploads/privilege'), $photoName);
                    $member->logo = $photoName;
                }

                $member->update([
                    'category_id'                   => $request->category_id,
                    'name'                          => $request->name,
                    'short_description'             => $request->short_description,
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
            Privilege::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Privilege::find($id);
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
