<?php

namespace App\Http\Controllers;

use App\Utils\JResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    use JResponse;
    /**
     */
    public function index()
    {
        $data = array(
            'key1' => 'value1',
            'key2' => 'value2'
        );
        return $this->json(200, 'OK', $data);
    }
}
