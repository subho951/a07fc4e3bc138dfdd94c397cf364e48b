<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'venue',
        'venue_google_map_link',
        'dress_code',
        'dining',
        'check_in',
        'event_date',
        'event_time',
        'photo',
        'video',
        'status',
    ];
}
