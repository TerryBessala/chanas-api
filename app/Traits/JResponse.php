<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JResponse
{
    /**
     * @param $responsecode
     * @param $message
     * @param null $data
     * @return JsonResponse
     */
    public function json($responsecode, $message, $data = null){
        return response()->json(array('responsecode' => $responsecode, 'msg' => $message, 'data' => $data));
    }

    /**
     * @param null $data
     * @return JsonResponse
     */
    public function jsonValidate($data){
        return response()->json($data);
    }
}
