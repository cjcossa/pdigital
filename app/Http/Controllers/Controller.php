<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    private $response;

    protected function setResponse($response)
    {
        $this->response = $response;
    }

    protected function getResponse()
    {
        return $this->response;
    }

}
