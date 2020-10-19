<?php

namespace Engency\Http\Response;

use Closure;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CanShowView
 *
 * @package Engency\Http\Response
 */
trait CanShowView
{

    private string $view                  = 'pages.raw-data-view';
    private array  $postponedViewOnlyData = [];
    private array  $viewOnlyData          = [];

    /**
     * @param string $view
     *
     * @return $this
     */
    public function view(string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param array|Closure $data
     *
     * @return $this
     */
    public function addDataForViewOnly($data)
    {
        if ($data instanceof Closure) {
            $this->postponedViewOnlyData[] = $data;

            return $this;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->viewOnlyData[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return Response
     * @throws Exception
     */
    protected function doShowView() : Response
    {
        if ($this->view === null || strlen($this->view) === 0) {
            throw new Exception('View "null" not found.');
        }

        $this->calculateViewData();
        $data = ['errors' => $this->getViewErrorBag()] + $this->viewOnlyData + $this->getViewData();

        return $this->getResponseFactory()->view($this->view, $data, $this->getHttpStatusCode());
    }

    /**
     * @return void
     */
    private function calculateViewData()
    {
        foreach ($this->postponedViewOnlyData as $closure) {
            $this->addDataForViewOnly($closure());
        }
    }

    /**
     * @return array
     */
    private function getViewData() : array
    {
        $data = $this->getData()->toArray();

        if (is_int(array_key_first($data))) {
            $data = ['items' => $data];
        }

        return $data;
    }

    /**
     * @return ViewErrorBag
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getViewErrorBag() : ViewErrorBag
    {
        $request  = Container::getInstance()->make(Request::class);
        $errorBag = $this->getErrorBagFromSession($request);
        /** @var MessageBag $bag */
        $bag = $this->getBag();

        if ($errorBag->hasBag('default')) {
            $existingBag = $errorBag->getBag('default');
            $existingBag->merge($bag);
            $bag = $existingBag;
        }

        $errorBag->put('default', $bag);

        return $errorBag;
    }

    /**
     * @param Request $request
     * @return ViewErrorBag
     */
    private function getErrorBagFromSession(Request $request) : ViewErrorBag
    {
        if (!$request->hasSession()) {
            return new ViewErrorBag();
        }

        return $request->session()->get('errors') ?: new ViewErrorBag;
    }
}
