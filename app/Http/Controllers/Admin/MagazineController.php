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
use App\Models\Magazine;
use Auth;
use Session;
use Helper;
use Hash;

class MagazineController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Magazine',
            'controller'        => 'MagazineController',
            'controller_route'  => 'magazine',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'magazine.list';
            $data['rows']                   = Magazine::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
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
                    'mag_file'      => 'required|file|mimes:pdf|max:' . $generalSetting->document_size,
                ]);

                /** Photo Upload */
                $photoName = time().'_'.$request->photo->getClientOriginalName();
                $request->photo->move(public_path('uploads/magazine'), $photoName);

                /** file Upload */
                $magfileName = time().'_'.$request->mag_file->getClientOriginalName();
                $request->mag_file->move(public_path('uploads/magazine'), $magfileName);

                Magazine::create([
                    'name'              => $request->name,
                    'news_date'         => $request->news_date,
                    'photo'             => $photoName,
                    'mag_file'          => $magfileName,
                    'description'       => $request->description,
                ]);

                return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' added successfully !!!');
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'magazine.add-edit';
            $data['row']                    = [];
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'magazine.add-edit';
            $data['row']                    = Magazine::where($this->data['primary_key'], '=', $id)->first();
            $generalSetting                 = GeneralSetting::find('1');

            if($request->isMethod('post')){
                $member = Magazine::findOrFail($id);

                $request->validate([
                    'name'          => 'required|string|max:255|unique:users,name',
                    'news_date'     => 'required|date',
                    'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:' . $generalSetting->photo_size,
                    'description'   => 'required|string|max:500',
                    'mag_file'      => 'nullable|file|mimes:pdf|max:' . $generalSetting->document_size,
                ]);

                /** Photo Update */
                if ($request->hasFile('photo')) {
                    $oldPath = public_path('uploads/magazine/'.$member->photo);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    $photoName = time().'_'.$request->photo->getClientOriginalName();
                    $request->photo->move(public_path('uploads/magazine'), $photoName);
                    $member->photo = $photoName;
                }

                /** file Update */
                if ($request->hasFile('mag_file')) {
                    $oldPath2 = public_path('uploads/magazine/'.$member->mag_file);
                    if (File::exists($oldPath2)) {
                        File::delete($oldPath2);
                    }

                    $magfileName = time().'_'.$request->mag_file->getClientOriginalName();
                    $request->mag_file->move(public_path('uploads/magazine'), $magfileName);
                    $member->mag_file = $magfileName;
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
            Magazine::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Magazine::find($id);
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
