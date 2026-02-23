<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoreMeeting extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'core_meetings';

    protected $fillable = [
        'core_id',
        'meeting_type',
        'from_date',
        'to_date',
        'venue',
        'short_description',
        'attendance',
        'quorum_percent',
        'status',
    ];
}
