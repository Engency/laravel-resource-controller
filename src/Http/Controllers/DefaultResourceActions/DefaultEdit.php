<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Response\Response;
use Illuminate\Http\Request;

trait DefaultEdit
{
    /**
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request)
    {
        return $this->defaultEdit($request);
    }

    /**
     * @param Request $request
     * @return Response
     */
    private function defaultEdit(Request $request) : Response
    {
        $data = [$this->getManagedResource()->getResourceName('camel') => $this->getResourceInstance($request)];

        return $this
            ->success($data, $this->getViewForAction('edit'))
            ->exportDataForJsonResponse(
                fn(array $data) => $this->exportDataContainingItemForJsonResponse($request, $data)
            );
    }
}
