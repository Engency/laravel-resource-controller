<?php

namespace Engency\Http\Controllers;

use Engency\Http\Response\Response;
use Illuminate\Http\Request;

/**
 * Interface ResourceEventInterface
 *
 * @package App\Http\Controllers\Resource
 */
interface ResourceEventInterface
{

    /**
     * @param Request $request
     * @param mixed   $resource
     *
     * @return mixed
     */
    public function stored(Request $request, $resource);

    /**
     * @param Request $request
     * @param mixed   $resource
     *
     * @return mixed
     */
    public function updated(Request $request, $resource);

    /**
     * @param Request $request
     * @param mixed   $resource
     *
     * @return mixed
     */
    public function destroyed(Request $request, $resource);

    /**
     * @param Request $request
     * @param mixed   $instance
     *
     * @return Response
     */
    public function redirectAfterStore(Request $request, $instance) : Response;

    /**
     * @param Request $request
     * @param mixed   $instance
     *
     * @return Response
     */
    public function redirectAfterUpdate(Request $request, $instance) : Response;

    /**
     * @param Request $request
     * @param mixed   $instance
     *
     * @return Response
     */
    public function redirectAfterDestroy(Request $request, $instance) : Response;
}
