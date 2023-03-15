<?php
/**
 * Created by PhpStorm.
 * User: eddy
 * Date: 13/12/18
 * Time: 10:57
 */

namespace App\Library\Repository;


interface SSOServiceInterface
{
    public function authentification($data);
    public function find_by_user_id($sso_user_id,$acces);
    public function logout();
    public function update_lang($lang, $access_token);
    public function update_user_org($data, $access_token);
    public function getAllOrgsUser($access_token, $sso_user_id);
    public function rolesUserInApp($access_token, $sso_user_id, $mcr_org_id);
}
