<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /* checking user role */
    public function inRole($code)
    {
        $role = $this->join('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->join('roles', 'user_roles.role_id', '=', 'roles.id')
            ->where('roles.code', '=', trim($code))
            ->where('user_roles.user_id','=',$this->id)
            ->first();

        if (!$role) {return false;}

        return true;
    }

    /* adding user role */
    public function addRole($code)
    {
        $role = Role::where('code', '=', trim($code))->first();
        $this->roles()->attach($role);
        return true;
    }

    /* getting user role */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

}
