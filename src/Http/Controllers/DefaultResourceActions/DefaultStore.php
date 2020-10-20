<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Controllers\ResourceEventInterface;
use Engency\Http\Response\Notice;
use Engency\Http\Response\Response;
use Illuminate\Http\Request;

trait DefaultStore
{
    /**
     * @param Request $request
     * @param array   $data
     *
     * @return Response
     */
    public function store(Request $request, array $data)
    {
        return $this->defaultStore($request, $data);
    }

    /**
     * @param Request $request
     * @param array   $data
     *
     * @return Response
     */
    private function defaultStore(Request $request, array $data) : Response
    {
        $data = array_merge($data, $this->getAttributesFromScope());

        $resource = call_user_func(
            [$this->getManagedResource()->getResourceClassName(), 'validateAndCreateNew'],
            $data
        );

        if ($this instanceof ResourceEventInterface) {
            $this->stored($request, $resource);
        }

        return $this->redirectAfterStore($request, $resource);
    }

    /**
     * @param Request $request
     * @param mixed   $instance
     * @return Response
     */
    protected function redirectAfterStore(Request $request, $instance) : Response
    {
        return $this
            ->success([$this->getManagedResource()->getResourceName('camel') => $instance])
            ->redirectToUrl($this->toPathInCurrentScope($instance->getKey(), 0))
            ->addNotice($this->getTranslatedMessage('stored'), Notice::NOTICE_SUCCESS)
            ->exportDataForJsonResponse(
                fn(array $data) => $this->exportDataContainingItemForJsonResponse($request, $data)
            );
    }
}
