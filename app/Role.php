<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Grants the role the given permission.
     *
     * @param Permission $permission The permission to grant.
     * @return self
     */
    public function grant(Permission $permission)
    {
        $this->permissions()->attach($permission);

        return $this;
    }

    /**
     * Revokes the given permission from the role.
     *
     * @param Permission $permission The permission to revoke.
     * @return self
     */
    public function revoke(Permission $permission)
    {
        $this->permissions()->detach($permission);

        return $this;
    }
}
