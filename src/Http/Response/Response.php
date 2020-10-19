<?php

namespace Engency\Http\Response;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;

class Response implements Responsable
{
    use CanShowBinaryInline, CanDownload, CanRedirect, CanShowView, CanShowJson, HasNotices;

    private ResponseDataBundle $data;
    private array              $responseMeta   = [];
    private int                $httpStatusCode = 200;
    private string             $resultsFormat;

    /**
     * Response constructor.
     *
     * @param array|Collection|LengthAwarePaginator|Model|null $data
     */
    public function __construct($data = null)
    {
        $this->data = new ResponseDataBundle($data);
        $this->bag  = new MessageBag();
    }

    /**
     * @param int $httpStatusCode
     *
     * @return $this
     */
    public function httpError(int $httpStatusCode)
    {
        $this->httpStatusCode = $httpStatusCode;

        return $this;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function exportInFormat(string $format)
    {
        $this->data->setDataFormat($format);

        return $this;
    }

    protected function getHttpStatusCode() : int
    {
        return $this->httpStatusCode;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Exception
     * @noinspection PhpMissingParamTypeInspection
     */
    public function toResponse($request)
    {
        switch (true) {
            case $this->canShowBinaryInline():
                return $this->doShowBinaryInline();
            case $this->canDownload():
                return $this->doDownload();
            case $this->canRedirect():
                return $this->doRedirect();
            case $this->canShowJson($request):
                return $this->doShowJson();
        }

        return $this->doShowView();

    }

    /**
     * @return ResponseFactory
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function getResponseFactory() : ResponseFactory
    {
        return Container::getInstance()->make(ResponseFactory::class);
    }

    /**
     * @return ResponseDataBundle
     */
    public function getData() : ResponseDataBundle
    {
        return $this->data;
    }

}