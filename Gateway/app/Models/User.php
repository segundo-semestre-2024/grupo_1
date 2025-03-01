<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;


    protected $fillable = ['name', 'email', 'password'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
}
