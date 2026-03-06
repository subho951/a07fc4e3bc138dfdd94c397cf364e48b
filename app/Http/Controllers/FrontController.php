<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\PaymentService;
use Illuminate\Support\Facades\File;

use App\Models\Achievement;
use App\Models\Admin;
use App\Models\Banner;
use App\Models\Category;
use App\Models\CommitteeCategory;
use App\Models\Core;
use App\Models\CoreMeeting;
use App\Models\CoreMember;
use App\Models\EmailLog;
use App\Models\Event;
use App\Models\EventQuestion;
use App\Models\GeneralSetting;
use App\Models\Industry;
use App\Models\Interest;
use App\Models\Magazine;
use App\Models\Media;
use App\Models\Page;
use App\Models\Privilege;
use App\Models\User;
use App\Models\UserAccess;
use App\Models\UserActivity;
use App\Models\UserPoint;
use App\Models\UserRegEvent;
use App\Models\UserRegEventAnswer;
use App\Models\UserDevice;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;

use Auth;
use Session;
use Helper;
use Hash;
use DB;
use App\Libraries\CreatorJwt;
use App\Libraries\JWT;
use Dompdf\Dompdf;
use Dompdf\Options;
date_default_timezone_set("Asia/Calcutta");

class FrontController extends Controller
{
    /* home */
        public function index(Request $request)
        {
            echo 'landing page';
        }
    /* home */
    /* event checkin */
        public function eventCheckin($token)
        {
            try {

                $id = Crypt::decryptString($token);

                $row = UserRegEvent::findOrFail($id);

                // Prevent duplicate entry
                if($row->status == 1){
                    return "Already checked in!";
                }

                $row->status = 1;
                $row->entry_timestamp = now();
                $row->save();

                return "Entry successful";

            } catch (\Exception $e) {
                return "Invalid QR Code";
            }
        }
    /* event checkin */
}
