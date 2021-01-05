<?php

namespace Engency\Http\Controllers;

use Engency\Http\Response\DefaultResponse;
use Engency\Http\Response\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;

abstract class BaseController extends Controller
{
    /**
     * @param array|Collection|LengthAwarePaginator|Model|null $data
     * @param string                                           $view
     *
     * @return Response
     */
    protected function success($data = [], string $view = '') : Response
    {
        $response = new Response($data);
        if (strlen($view) > 0) {
            $response->view($view);
        }

        return $response;
    }

    /**
     * @param int $httpErrorCode
     *
     * @return Response
     */
    protected function failure(int $httpErrorCode = IlluminateResponse::HTTP_CONFLICT) : Response
    {
        if ($httpErrorCode == IlluminateResponse::HTTP_CONFLICT) {
            return DefaultResponse::unprocessable('')->redirectBack();
        }

        return ( new Response )->httpError($httpErrorCode);
    }
}
