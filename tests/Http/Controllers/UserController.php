<?php /** @noinspection PhpUnused */

namespace Engency\Test\Http\Controllers;

use Engency\Http\Controllers\DefaultResourceActions;
use Engency\Http\Controllers\ResourceController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class UserController extends ResourceController
{
    use DefaultResourceActions;

    /** @noinspection PhpUnusedParameterInspection */
    protected function getScope(Request $request) : Builder
    {
        return Model::query();
    }
}