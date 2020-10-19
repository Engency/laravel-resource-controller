<?php

namespace Engency\Http\Controllers;

use Engency\Http\Controllers\DefaultResourceActions\DefaultCreate;
use Engency\Http\Controllers\DefaultResourceActions\DefaultDestroy;
use Engency\Http\Controllers\DefaultResourceActions\DefaultEdit;
use Engency\Http\Controllers\DefaultResourceActions\DefaultIndex;
use Engency\Http\Controllers\DefaultResourceActions\DefaultShow;
use Engency\Http\Controllers\DefaultResourceActions\DefaultStore;
use Engency\Http\Controllers\DefaultResourceActions\DefaultUpdate;
use Engency\Http\Response\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Trait DefaultResourceActions
 *
 * @package App\Http\Controllers\Resource
 */
trait DefaultResourceActions
{

    use DefaultIndex, DefaultCreate, DefaultStore, DefaultShow, DefaultEdit, DefaultUpdate, DefaultDestroy;

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|Collection|\Illuminate\Database\Query\Builder
     */
    protected abstract function getScope(Request $request);

    abstract protected function getAttributesFromScope(?bool $onlyIdentifiers = true) : array;

    abstract protected function getResourceInstance(Request $request) : Model;

    abstract protected function getTranslatedMessage(string $case) : string;

    abstract protected function getViewPrefix() : string;

    abstract protected function getViewForAction(string $action);

    abstract protected function success($data = [], string $view = '') : Response;

    abstract protected function failure(int $httpErrorCode = 406) : Response;

}
