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
use App\Models\News;
use Auth;
use Session;
use Helper;
use Hash;

class NewsController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'News',
            'controller'        => 'NewsController',
            'controller_route'  => 'news',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'news.list';
            $data['rows']                   = News::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
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
                    'news_date'     => 'required|date',
                    'photo'         => 'required|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'description'   => 'required|string|max:500',
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/news'), $photoName);

                News::create([
                    'name'              => $request->name,
                    'news_date'         => $request->news_date,
                    'photo'             => $photoName,
                    'description'       => $request->description,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'news.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'news.add-edit';
            $data['row']                    = News::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = News::findOrFail($id);

                $request->validate([
                    'name'          => 'required|string|max:255|unique:users,name',
                    'news_date'     => 'required|date',
                    'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'description'   => 'required|string|max:500',
                ]);

                /** Photo Update */
                if ($request->hasFile('photo')) {
                    $oldPath = public_path('uploads/news/'.$member->photo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->photo->getClientOriginalName();
                    $request->photo->move(public_path('uploads/news'), $photoName);
                    $member->photo = $photoName;
                }

                $member->update([
                    'name'              => $request->name,
                    'news_date'         => $request->news_date,
                    'description'       => $request->description,
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
            News::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = News::find($id);
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
