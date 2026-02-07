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
use App\Models\Category;
use App\Models\Media;

use Auth;
use Session;
use Helper;
use Hash;

class MediaController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Media',
            'controller'        => 'MediaController',
            'controller_route'  => 'media',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'media.institute-list';
            $data['institutes']             = Institute::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function categoryList($institute_id){
            $institute_id                   = Helper::decoded($institute_id);
            $data['institute_id']           = $institute_id;
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'media.category-list';
            $data['cats']                   = Category::where('status', '!=', 3)->where('institute_id', '=', $institute_id)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
        public function mediaList(Request $request, $institute_id, $category_id){
            $institute_id                   = Helper::decoded($institute_id);
            $category_id                    = Helper::decoded($category_id);

            $data['institute_id']           = $institute_id;
            $data['category_id']            = $category_id;

            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'media.media-list';
            $data['cats']                   = Category::where('status', '!=', 3)->where('institute_id', '=', $institute_id)->orderBy('id', 'DESC')->get();
                        
            $data['medias']                 = Media::where('status', '=', 1)->where('institute_id', '=', $institute_id)->where('category_id', '=', $category_id)->orderBy('id', 'DESC')->get();
            // Helper::pr($data['medias']);

            if($request->isMethod('post')){
                $request->validate([
                    'photo'   => 'required',
                    'photo.*' => 'image|mimes:jpg,jpeg,png,webp|max:200', // 200 KB
                ]);

                $uploadedFiles = [];

                if ($request->hasFile('photo')) {
                    foreach ($request->file('photo') as $image) {

                        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                        $image->move(public_path('uploads/media'), $filename);

                        $uploadedFiles[] = 'uploads/media/' . $filename;
                    }
                }

                // Helper::pr($uploadedFiles);

                // return back()->with('success', 'Images uploaded successfully!')
                //             ->with('files', $uploadedFiles);

                if(!empty($uploadedFiles)){
                    for($k=0;$k<count($uploadedFiles);$k++){
                        $fields = [
                            'institute_id' => $institute_id,
                            'category_id' => $category_id,
                            'media_file' => $uploadedFiles[$k],
                        ];
                        Media::insert($fields);
                    }
                }

                return redirect('admin/media/media-list/'.Helper::encoded($institute_id).'/' . Helper::encoded($category_id))->with('success_message', $this->data['title'].' uploaded successfully !!!');
            }

            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* delete */
        public function delete(Request $request, $id){
            $id                       = Helper::decoded($id);
            $getMedia                 = Media::where('id', '=', $id)->first();

            $fields = [
                'status'             => 3
            ];
            Media::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/media/media-list/'.Helper::encoded($getMedia->institute_id).'/' . Helper::encoded($getMedia->category_id))->with('success_message', $this->data['title'].' deleted successfully !!!');
        }
    /* delete */
}
