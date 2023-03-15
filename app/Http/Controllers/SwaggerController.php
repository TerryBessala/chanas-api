<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 *
 *  @OA\Info(
 *     version="1.0.0",
 *     title = "SALES API",
 *     description= "This is a demo service, which provides the function of demonstrating the App api",
 *      @OA\Contact(
 *         email="contact@nexah.net",
 *         name="NEXAH"
 *     )
 * )
 *
 *  @OA\Server(
 *     url="http://sales-dev.nexah.net/api/v1",
 *     description= "development environment"
 * )
 *
 *  @OA\package App\Http\Controllers
 */

class SwaggerController extends BaseController
{ }
