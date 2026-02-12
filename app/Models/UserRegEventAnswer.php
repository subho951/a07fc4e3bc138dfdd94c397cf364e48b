<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRegEventAnswer extends Authenticatable
{
    protected $table = 'user_reg_event_answers';

    protected $fillable = [
        'userid',
        'eventid',
        'event_question',
        'event_answer_type',
        'event_answer',
        'status',
    ];
}
