<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Services\PaymentService;
use Illuminate\Support\Facades\File;
use App\Services\FirebaseService;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\Newsletter;
use App\Models\Notification;
use App\Models\UserDevice;

use Hash;
use Session;
use Helper;
use Auth;

class NewsletterController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Newsletter',
            'controller'        => 'NewsletterController',
            'controller_route'  => 'newsletter',
            'primary_key'       => 'id',
        );
    }
    /* list */
        public function list(){
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' List';
            $page_name                      = 'newsletter.list';
            $data['rows']                   = Newsletter::where('status', '!=', 3)->orderBy('id', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* list */
    /* add */
        public function add(Request $request){
            $data['module']           = $this->data;
            if($request->isMethod('post')){
                $rules = [
                    'title'                 => 'required',
                    'description'           => 'required',
                    'to_users'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    /* image */
                        // $imageFile      = $request->file('attachment');
                        // if($imageFile != ''){
                        //     $imageName      = $imageFile->getClientOriginalName();
                        //     $uploadedFile   = $this->upload_single_file('attachment', $imageName, 'newsletter', 'image');
                        //     if($uploadedFile['status']){
                        //         $attachment = $uploadedFile['newFilename'];
                        //     } else {
                        //         $attachment = '';
                        //     }
                        // } else {
                        //     $attachment = '';
                        // }
                        $attachment = '';
                    /* image */
                    $postData = [
                        'title'                     => $request->title,
                        'description'               => $request->description,
                        'attachment'                => $attachment,
                        'to_users'                  => $request->to_users,
                        'users'                     => json_encode($request->users),
                    ];
                    Newsletter::insert($postData);
                    return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Inserted Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
            }
            $data['module']                 = $this->data;
            $title                          = $this->data['title'].' Add';
            $page_name                      = 'newsletter.add-edit';
            $data['row']                    = [];
            $data['allUsers']               = User::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'Asc')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* add */
    /* edit */
        public function edit(Request $request, $id){
            $data['module']                 = $this->data;
            $id                             = Helper::decoded($id);
            $title                          = $this->data['title'].' Update';
            $page_name                      = 'newsletter.add-edit';
            $data['row']                    = Newsletter::where($this->data['primary_key'], '=', $id)->first();
            $data['allUsers']               = User::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'Asc')->get();

            if($request->isMethod('post')){
                $postData = $request->all();
                $rules = [
                    'title'                 => 'required',
                    'description'           => 'required',
                    'to_users'              => 'required',
                ];
                if($this->validate($request, $rules)){
                    /* image */
                        // $imageFile      = $request->file('attachment');
                        // if($imageFile != ''){
                        //     $imageName      = $imageFile->getClientOriginalName();
                        //     $uploadedFile   = $this->upload_single_file('attachment', $imageName, 'newsletter', 'image');
                        //     if($uploadedFile['status']){
                        //         $attachment = $uploadedFile['newFilename'];
                        //     } else {
                        //         $attachment = '';
                        //     }
                        // } else {
                        //     $attachment = $data['row']->attachment;
                        // }
                        $attachment = '';
                    /* image */
                    $postData = [
                        'title'                     => $request->title,
                        'description'               => $request->description,
                        'attachment'                => $attachment,
                        'to_users'                  => $request->to_users,
                        'users'                     => json_encode($request->users),
                    ];
                    $update = Newsletter::where($this->data['primary_key'],'=',$id)->update($postData);
                    return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Updated Successfully !!!');
                } else {
                    return redirect()->back()->with('error_message', 'All Fields Required !!!');
                }
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
            Newsletter::where($this->data['primary_key'], '=', $id)->update($fields);
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Deleted Successfully !!!');
        }
    /* delete */
    /* change status */
        public function change_status(Request $request, $id){
            $id                             = Helper::decoded($id);
            $model                          = Newsletter::find($id);
            if ($model->status == 1)
            {
                $model->status  = 0;
                $msg            = 'Deactivated';
            } else {
                $model->status  = 1;
                $msg            = 'Activated';
            }            
            $model->save();
            return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' '.$msg.' Successfully !!!');
        }
    /* change status */
    public function send(Request $request, $id)
    {
        $id             = Helper::decoded($id);
        $model          = Newsletter::find($id);
        /* mail function */
            // $generalSetting             = GeneralSetting::find('1');
            // $subject                    = $generalSetting->site_name.' :: '.$model->title;
            // $requestData                = [
            //     'title'         => $model->title,
            //     'description'   => $model->description,
            //     'attachment'    => $model->attachment,
            // ];
            // $message                    = view('email-templates.newsletter',$requestData);
            // $attachment  ='';
            // if($model->attachment != ''){
            //     // $attachment = env('UPLOADS_URL').'newsletter/'.$model->attachment;
            //     $attachment = public_path("uploads/newsletter/".$model->attachment);
            // }
            // // echo $attachment;die;
            // $users = json_decode($model->users);
            // if(!empty($users)){ for($u=0;$u<count($users);$u++){
            //     $user = User::select('email')->where('id', '=', $users[$u])->first();
            //     $to_email = (($user)?$user->email:'');
            //     if($to_email != ''){
            //         $this->sendMail(strtolower($to_email), $subject, $message, $attachment);
            //     }
            // } }
        /* mail function */

        // push notification send
            $users = [];
            $getTokens = UserDevice::select('fcm_token')->where('user_id', '=', $uId)->where('published', '=', 1)->where('fcm_token', '!=', '')->get();
            
            if($getTokens){
                foreach($getTokens as $getToken){
                    $token = $getToken->fcm_token;

                    $title = 'Profile updated';
                    $message = 'Profile info has been updated at ' . date('d.m.Y h:i A');

                    $image = (($getUser->photo != '')?env('UPLOADS_URL').'user/'.$getUser->photo:env('NO_IMAGE'));

                    $data = [
                        "profile_id" => $uId,
                        "type" => 'profile'
                    ];

                    $firebase_response = FirebaseService::sendNotification($token,$title,$message,$data);
                    // Helper::pr($firebase_response);

                    $users[]            = $uId;
                    $notificationFields = [
                        'title'             => $title,
                        'description'       => $message,
                        'to_users'          => $uId,
                        'users'             => json_encode($users),
                        'is_send'           => 1,
                        'send_timestamp'    => date('Y-m-d H:i:s'),
                    ];
                    Notification::insert($notificationFields);
                }
            }
        // push notification send

        $model->is_send = 1;
        $model->save();
        return redirect('admin/'.$this->data['controller_route'] . "/list")->with('success_message', $this->data['title'].' Send Successfully !!!');
    }
    public function getUser(Request $request)
    {
        $apiStatus          = TRUE;
        $apiMessage         = 'Data Available !!!';
        $apiResponse        = [];
        $apiExtraField      = '';
        $apiExtraData       = '';
        $postData           = $request->all();
        $user_type          = $postData['user_type'];
        if($user_type == 0){
            $users                  = User::select('id', 'name')->where('status', '=', 1)->get();
        } elseif($user_type == 1){
            $users                  = User::select('id', 'name')->where('status', '=', 1)->where('type', '=', 1)->get();
        }
        /* industry segment dropdown */
            $user_selects                    = [];
            if($users){
                foreach($users as $user){
                    if($user_type != 2){
                        $user_selects[]          = [
                            'id'    => $user->id,
                            'label' => $user->name,
                        ];
                    } else {
                        $user_selects[]          = [
                            'id'    => $user->id,
                            'label' => $user->email,
                        ];
                    }
                }
            }
        /* industry segment dropdown */
        $apiResponse = [
            'user_selects'           => $user_selects
        ];
        http_response_code(200);
        $apiExtraField      = 'response_code';
        $apiExtraData       = http_response_code();
        $this->response_to_json($apiStatus, $apiMessage, $apiResponse, $apiExtraField, $apiExtraData);
    }
}
