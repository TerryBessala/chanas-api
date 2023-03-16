<?php

namespace App\Http\Controllers;
use App\Utils\JResponse;
use Illuminate\Http\Request;
use http\Client;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Routing\Controller;

class PaymentController extends  Controller
{
    use JResponse;

    public function  list(Request $request)
    {
        $page= $request->page;
        $per_page = $request->per_page;
        $paginate =$request->paginate;

        $result['data'] = DB::select(' call sp_client_list(?,?,?)',[$paginate,$page,$per_page]);


        $result['total'] = DB::select(' call count_client()')[0]->total ;

        $result['page'] = $page ;
        $result['per_page'] = $per_page;
        $result['from'] = (($page - 1) * $per_page) + 1;
        $result['to'] =  $result['from'] + sizeof($result['data']) - 1;
        return $this->json(200, 'OK', $result);

    }

}
