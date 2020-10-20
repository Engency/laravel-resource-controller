<?php

namespace Engency\Http\Controllers\DefaultResourceActions;

use Engency\Http\Response\Response;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait DefaultIndex
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request)
    {
        return $this->defaultIndex($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    private function defaultIndex(Request $request) : Response
    {
        $items = $this->getScope($request);

        if ($items instanceof Builder || $items instanceof \Illuminate\Database\Query\Builder) {
            $items = $items->get();
        }

        return $this
            ->success(['items' => $items], $this->getViewForAction('index'))
            ->addDataForViewOnly($this->getAttributesFromScope(false))
            ->exportDataForJsonResponse(
                fn(array $data) => $this->exportDataContainingListForJsonResponse($request, $data)
            );
    }
}
