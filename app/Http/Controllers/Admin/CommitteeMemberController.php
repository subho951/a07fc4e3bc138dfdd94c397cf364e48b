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
            $data['rows']                   = User::select('users.*')
                                                ->where('users.status', '!=', 3)
                                                ->where('users.type', '=', 1)
                                                ->whereNotNull('users.committee_category_id')
                                                ->whereRaw("TRIM(users.committee_category_id) != ''")
                                                ->where('users.committee_category_id', '!=', '0')
                                                ->orderBy('users.id', 'DESC')
                                                ->get();

            $committeeCategoryMap           = CommitteeCategory::select('id', 'name')->get()->pluck('name', 'id')->toArray();
            if($data['rows']){
                foreach($data['rows'] as $row){
                    $row->committee_category_names = $this->getCommitteeCategoryNames($row->committee_category_id, $committeeCategoryMap);
                }
            }
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $generalSetting             = GeneralSetting::find('1');
            $data['module']             = $this->data;
            if($request->isMethod('post')){
                $committeeCategoryIds = $this->normalizeCommitteeCategoryIds($request->input('committee_category_id'));
                $request->merge([
                    'committee_category_id' => $committeeCategoryIds,
                ]);

                $request->validate([
                    'committee_category_id'             => 'required|array|min:1',
                    'committee_category_id.*'           => 'required|integer|exists:committee_categories,id',
                    'member_id'                         => 'required|integer',
                    'committee_member_type'             => 'required|integer',
                ]);

                $member = User::findOrFail($request->member_id);
                $fields = [
                    'committee_category_id' => implode(',', $committeeCategoryIds),
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
            // $data['members']                = User::select('id', 'name')->where('status', '=', 1)->whereNull('committee_category_id')->orderBy('name', 'ASC')->get();
            $data['members']                = User::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get();
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
                $committeeCategoryIds = $this->normalizeCommitteeCategoryIds($request->input('committee_category_id'));
                $request->merge([
                    'committee_category_id' => $committeeCategoryIds,
                ]);

                $request->validate([
                    'committee_category_id'             => 'required|array|min:1',
                    'committee_category_id.*'           => 'required|integer|exists:committee_categories,id',
                    'member_id'                         => 'required|integer',
                    'committee_member_type'             => 'required|integer',
                ]);

                $member = User::findOrFail($request->member_id);
                $fields = [
                    'committee_category_id' => implode(',', $committeeCategoryIds),
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
    /* helper */
        private function normalizeCommitteeCategoryIds($committeeCategoryIds){
            if(!is_array($committeeCategoryIds)){
                $committeeCategoryIds = explode(',', (string)$committeeCategoryIds);
            }

            $normalizedIds = [];
            foreach($committeeCategoryIds as $committeeCategoryId){
                $committeeCategoryId = trim((string)$committeeCategoryId);
                if($committeeCategoryId === '' || !ctype_digit($committeeCategoryId)){
                    continue;
                }

                $committeeCategoryId = (int)$committeeCategoryId;
                if($committeeCategoryId <= 0){
                    continue;
                }

                $normalizedIds[] = (string)$committeeCategoryId;
            }

            return array_values(array_unique($normalizedIds));
        }

        private function getCommitteeCategoryNames($committeeCategoryIds, $committeeCategoryMap){
            $categoryIds = $this->normalizeCommitteeCategoryIds($committeeCategoryIds);
            $categoryNames = [];

            foreach($categoryIds as $categoryId){
                if(isset($committeeCategoryMap[$categoryId])){
                    $categoryNames[] = $committeeCategoryMap[$categoryId];
                }
            }

            return implode(', ', $categoryNames);
        }
    /* helper */
}
