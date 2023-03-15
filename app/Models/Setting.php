<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'username',
        'password',
        'app_token',
        'app_web_url',
        'gen_key',
        'client_id',
        'client_secret',
        'grant_type',
        'scope',
        'api_sso_url',
        'api_org_url',
    ];

}
