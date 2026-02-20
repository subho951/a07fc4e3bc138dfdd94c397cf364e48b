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

class CommitteeMemberController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Committee Member',
            'controller'        => 'CommitteeMemberController',
            'controller_route'  => 'committee-member',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'committee-member.list';
            $data['rows']                   = User::select(
                                                    'users.*',
                                                    'committee_categories.name as committee_category_name'
                                                )
                                                ->join('committee_categories', 'committee_categories.id', '=', 'users.committee_category_id')
                                                ->where('users.status', '!=', 3)
                                                ->where('users.type', '=', 1)
                                                ->where('users.committee_category_id', '>', 0)
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
                    'committee_category_id'             => 'required|integer',
                    'member_id'                         => 'required|integer',
                    'committee_member_type'             => 'required|integer',
                ]);

                $member = User::findOrFail($request->member_id);
                $fields = [
                    'committee_category_id' => $request->committee_category_id,
                    'committee_member_type' => $request->committee_member_type,
                ];
                $member->update($fields);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'committee-member.add-edit';
            $data['row']                    = [];
            $data['cats']                   = CommitteeCategory::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['members']                = User::select('id', 'name')->where('status', '=', 1)->whereNull('committee_category_id')->orderBy('name', 'ASC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'committee-member.add-edit';
            $data['row']                    = User::where($this->data['primary_key'], '=', $id)->first();
            $data['cats']                   = CommitteeCategory::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $data['members']                = User::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $request->validate([
                    'committee_category_id'             => 'required|integer',
                    'member_id'                         => 'required|integer',
                    'committee_member_type'             => 'required|integer',
                ]);

                $member = User::findOrFail($request->member_id);
                $fields = [
                    'committee_category_id' => $request->committee_category_id,
                    'committee_member_type' => $request->committee_member_type,
                ];
                $member->update($fields);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' updated successfully !!!');
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* edit */
    /* delete */
        public function delete(Request $request, $id){
            $id                             = Helper::decoded($id);
            $fields = [
                'committee_category_id'             => null,
                'committee_member_type'             => null,
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
