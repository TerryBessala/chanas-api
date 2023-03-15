<?php

namespace App;

use App\Models\Campaign;
use App\Models\UserOrgSetting;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     properties={
 *     }
 * )
 * User model
 * Specifying the User properties
 * @author Ronaldo
 */

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $primaryKey = "id";

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sso_user_id',
        'access_token',
        'api_token',
        'expires_in',
        'created_at',
        'updated_at'
    ];
}
