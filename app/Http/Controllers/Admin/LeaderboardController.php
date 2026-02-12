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
use App\Models\Core;
use App\Models\CoreMember;

use Auth;
use Session;
use Helper;
use Hash;

class LeaderboardController extends Controller
{
    public function __construct()
    {        
        $this->data = array(
            'title'             => 'Leader Board',
            'controller'        => 'LeaderboardController',
            'controller_route'  => 'leader-board',
            'primary_key'       => 'id',
        );
    }
    /* core */
        public function core(){
            $data['module']                 = $this->data;
            $title                          = 'Core ' . $this->data['title'].' List';
            $page_name                      = 'leader-board.core';
            $data['core_points']            = Core::select('name', 'points')->where('status', '=', 1)->orderBy('points', 'DESC')->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* core */
    /* core */
        public function member(){
            $data['module']                 = $this->data;
            $title                          = 'Member ' . $this->data['title'].' List';
            $page_name                      = 'leader-board.member';
            $data['member_points']          = User::select('name', 'points')->where('status', '=', 1)->orderBy('points', 'DESC')->limit(20)->get();
            echo $this->admin_after_login_layout($title,$page_name,$data);
        }
    /* core */
}
