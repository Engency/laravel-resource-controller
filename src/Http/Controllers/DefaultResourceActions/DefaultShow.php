<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Response\Response;
use Illuminate\Http\Request;

trait DefaultShow
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function show(Request $request)
    {
        return $this->defaultShow($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    private function defaultShow(Request $request) : Response
    {
        $data = [$this->getManagedResource()->getResourceName('camel') => $this->getResourceInstance($request)];

        return $this->success($data, $this->getViewForAction('show'))
                    ->exportDataForJsonResponse(
                        fn(array $data) => $this->exportDataContainingItemForJsonResponse($request, $data)
                    );
    }
}