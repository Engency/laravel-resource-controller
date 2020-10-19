<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Controllers\ResourceEventInterface;
use Engency\Http\Response\Notice;
use Engency\Http\Response\Response;
use Exception;
use Illuminate\Http\Request;

trait DefaultDestroy
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     *
     */
    public function destroy(Request $request)
    {
        return $this->defaultDestroy($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception
     */
    private function defaultDestroy(Request $request) : Response
    {
        $resource = $this->getResourceInstance($request);

        if ($resource === null) {
            return $this->failure()
                        ->redirectBack()
                        ->addNotice($this->getTranslatedMessage('delete-error'));
        }

        $resource->delete();

        if ($this instanceof ResourceEventInterface) {
            $this->destroyed($request, $resource);
        }

        return $this->redirectAfterDestroy($request, $resource);
    }

    /**
     * @param Request $request
     * @param mixed   $instance
     * @return Response
     * @noinspection PhpUnusedParameterInspection
     */
    protected function redirectAfterDestroy(Request $request, $instance) : Response
    {
        return $this->success()
                    ->redirectBack()
                    ->addNotice($this->getTranslatedMessage('deleted'), Notice::NOTICE_SUCCESS);
    }
}