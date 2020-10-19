<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Response\Response;

trait DefaultCreate
{

    /**
     * @return Response
     */
    public function create()
    {
        return $this->defaultCreate();
    }

    /**
     * @return Response
     */
    private function defaultCreate() : Response
    {
        return $this->success($this->getAttributesFromScope(), $this->getViewForAction('create'));
    }

}