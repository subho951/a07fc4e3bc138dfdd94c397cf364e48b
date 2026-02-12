<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRegEvent extends Authenticatable
{
    protected $table = 'user_reg_events';

    protected $fillable = [
        'userid',
        'eventid',
        'is_spouse',
        'note',
        'date',
        'time',
        'qrcode',
        'entry_timestamp',
        'status',
        'is_regret',
        'regret_reason',
        'regret_timestamp',
    ];
}
