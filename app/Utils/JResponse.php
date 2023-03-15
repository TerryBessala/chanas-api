<?php


namespace App\Utils;


trait JResponse
{
    public function json($errcode, $message, $data = null){
        if ($data != null){
            $result = array('errcode' => $errcode, 'msg' => $message, 'data' => $data);
        }else{
            $result = array('errcode' => $errcode, 'msg' => $message);
        }
        return response()->json($result, $errcode);
    }
}
