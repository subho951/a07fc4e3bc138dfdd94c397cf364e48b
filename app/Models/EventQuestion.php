<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventQuestion extends Authenticatable
{
    protected $table = 'event_questions';

    protected $fillable = [
        'event_id',
        'event_question',
        'event_answer_type',
        'event_answer_options',
        'is_bydefault_show',
        'status',
    ];
}
