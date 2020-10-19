<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Controllers\ResourceEventInterface;
use Engency\Http\Response\Notice;
use Engency\Http\Response\Response;
use Illuminate\Http\Request;

trait DefaultUpdate
{

    /**
     * @param Request $request
     * @param array   $data
     *
     * @return Response
     */
    public function update(Request $request, array $data)
    {
        return $this->defaultUpdate($request, $data);
    }

    /**
     * @param Request $request
     * @param array   $data
     *
     * @return Response
     */
    private function defaultUpdate(Request $request, array $data) : Response
    {
        $resource = $this->getResourceInstance($request);

        if ($resource === null) {
            return $this->failure()
                        ->redirectBack()
                        ->addNotice($this->getTranslatedMessage('update-error'));
        }

        $resource->validateAndUpdate($data, 'update');

        if ($this instanceof ResourceEventInterface) {
            $this->updated($request, $resource);
        }

        return $this->redirectAfterUpdate($request, $resource);
    }

    /**
     * @param Request $request
     * @param mixed   $instance
     * @return Response
     */
    protected function redirectAfterUpdate(Request $request, $instance) : Response
    {
        return $this
            ->success([$this->getManagedResource()->getResourceName('camel') => $instance->fresh()])
            ->redirectBack()
            ->addNotice($this->getTranslatedMessage('updated'), Notice::NOTICE_SUCCESS)
            ->exportDataForJsonResponse(
                fn(array $data) => $this->exportDataContainingItemForJsonResponse($request, $data)
            );
    }

}