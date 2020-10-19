<?php

namespace Engency\Http\Response;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CanShowJson
 *
 * @package Engency\Http\Response
 */
trait CanShowJson
{

    private bool $forceJson = false;

    /**
     * @var Closure[]
     */
    private array $dataPreparationMethods = [];

    /**
     * @param bool $value
     * @return static
     */
    public function json(bool $value = true)
    {
        $this->forceJson = $value;

        return $this;
    }

    /**
     * @param Closure $closure
     * @return $this
     */
    public function exportDataForJsonResponse(Closure $closure)
    {
        $this->dataPreparationMethods[] = $closure;

        return $this;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function canShowJson(Request $request)
    {
        return $this->forceJson || $request->expectsJson();
    }

    /**
     * @return Response
     */
    protected function doShowJson() : Response
    {
        return $this->getResponseFactory()->json(
            $this->getMessageBagForJsonResponse() + $this->getJsonResponseData(),
            $this->getHttpStatusCode()
        );
    }

    /**
     * @return array
     */
    private function getJsonResponseData() : array
    {
        $data = $this->getData()->getDataForJsonResponse();

        collect($this->dataPreparationMethods)
            ->each(function (Closure $closure) use (&$data) {
                $data = $closure($data);
            });

        return $data;
    }

    /**
     * @return array
     */
    private function getMessageBagForJsonResponse() : array
    {
        /** @var MessageBag $messageBag */
        $messageBag = $this->getBag();

        return ['messageBag' => $messageBag->toArray()];
    }
}
