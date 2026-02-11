<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Core extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'cores';

    protected $fillable = [
        'name',
        'points',
        'photo',
        'description',
        'status',
    ];
}
