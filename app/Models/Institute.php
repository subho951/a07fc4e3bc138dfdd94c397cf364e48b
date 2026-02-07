<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institute extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'institutes';

    protected $fillable = [
        'name',
        'logo',
        'status',
    ];
}
