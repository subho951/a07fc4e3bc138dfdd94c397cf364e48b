<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorePoint extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'core_points';

    protected $fillable = [
        'core_id',
        'member_id',
        'event_id',
        'credited_points',
        'note',
    ];
}
