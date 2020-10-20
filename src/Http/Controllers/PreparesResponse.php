<?php

namespace Engency\Http\Controllers;

use Engency\Http\ManagedResource;
use Engency\Http\Response\Response;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Illuminate\Http\Response as IlluminateResponse;

trait PreparesResponse
{
    /**
     * @param array|Model $data
     * @param string      $view
     *
     * @return Response
     */
    abstract protected function success($data = [], string $view = '') : Response;

    /**
     * @return ManagedResource
     */
    abstract public function getManagedResource() : ManagedResource;

    /**
     * @param string         $method
     * @param Response|array $response
     *
     * @return Response
     * @throws Exception
     *
     */
    private function prepareResponse(string $method, $response)
    {
        if ($response instanceof Response) {
            return $response;
        } elseif ($response instanceof IlluminateResponse || $response instanceof SymfonyResponse) {
            return $response;
        } elseif (is_array($response)) {
            return $this->prepareArrayResponse($method, $response);
        }

        throw new Exception('Invalid response-type "' . gettype($response) . '"');
    }

    /**
     * @param string $method
     * @param array  $response
     *
     * @return Response
     */
    private function prepareArrayResponse(string $method, array $response)
    {
        return $this->success($response, $this->getViewPrefix() . $method);
    }

    /**
     * @return string
     */
    protected function getViewPrefix() : string
    {
        $pathPrefix = '';

        // todo: allow modules

        return $pathPrefix . 'pages.' . $this->getManagedResource()->getResourceName() . '.';
    }
}
