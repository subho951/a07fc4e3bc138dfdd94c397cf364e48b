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
use App\Models\CommitteeCategory;
use Auth;
use Session;
use Helper;
use Hash;

class CommitteeCategoryController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Committee Category',
            'controller'        => 'CommitteeCategoryController',
            'controller_route'  => 'committee-category',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'committee-category.list';
            $data['rows']                   = CommitteeCategory::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $generalSetting             = GeneralSetting::find('1');
            $data['module']             = $this->data;
            if($request->isMethod('post')){
                $request->validate([
                    'name'         => 'required|string|max:255|unique:committee_categories,name',
                ]);

                CommitteeCategory::create([
                    'name'          => $request->name,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'committee-category.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'committee-category.add-edit';
            $data['row']                    = CommitteeCategory::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = CommitteeCategory::findOrFail($id);

                $request->validate([
                    'name'         => 'required|string|max:255|unique:committee_categories,name,'.$member->id,
                ]);

                $member->update([
                    'name'          => $request->name,
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
            CommitteeCategory::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = CommitteeCategory::find($id);
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
