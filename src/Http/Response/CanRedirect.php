<?php

namespace Engency\Http\Response;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CanRedirect
 *
 * @package Engency\Http\Response
 */
trait CanRedirect
{

    private bool $suggestRedirect = false;

    /**
     * @var RedirectResponse|null
     */
    private ?RedirectResponse $redirectObject = null;

    /**
     * @return $this
     */
    public function redirectBack()
    {
        $this->suggestRedirect = true;
        $this->redirectObject  = null;

        return $this;
    }

    /**
     * @param string $routeName
     * @param array  $parameters
     * @return CanRedirect
     */
    public function redirectTo(string $routeName, array $parameters = [])
    {
        return $this->redirectToUrl($this->getUrlResolver()->route($routeName, $parameters));
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function redirectToUrl(string $url)
    {
        $this->suggestRedirect = true;
        $this->redirectObject  = $this->getRedirectResolver()->to($url);

        return $this;
    }

    /**
     * @return bool
     */
    protected function canRedirect()
    {
        return $this->suggestRedirect;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function clientAllowsRedirect(Request $request) : bool
    {
        return $request->header('allow-redirects', '') !== 'false';
    }

    /**
     * @return Response
     */
    protected function doRedirect() : Response
    {
        return $this->attachErrors($this->getRedirectResponse());
    }

    /**
     * @param RedirectResponse $redirectResponse
     *
     * @return RedirectResponse
     */
    private function attachErrors(RedirectResponse $redirectResponse) : RedirectResponse
    {
        /** @var MessageBag $bag */
        $bag = $this->getBag();
        if ($bag->count() > 0) {
            return $redirectResponse->withErrors($bag);
        }

        return $redirectResponse;
    }

    /**
     * @return RedirectResponse
     */
    private function getRedirectResponse() : RedirectResponse
    {
        return $this->redirectObject === null
            ? $this->getRedirectResolver()->back()->withInput()
            : $this->redirectObject;
    }

    /**
     * @return Redirector
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getRedirectResolver() : Redirector
    {
        return Container::getInstance()->make('redirect');
    }

    /**
     * @return UrlGenerator
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getUrlResolver() : UrlGenerator
    {
        return Container::getInstance()->make('url');
    }
}
