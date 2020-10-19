<?php

namespace Engency\Http;

use Engency\Http\Resource\ResourceNaming;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ManagedResource
{

    private Model $instance;

    use ResourceNaming;

    public function __construct(string $resourceClassName)
    {
        $this->resourceClassName = $resourceClassName;
    }

    /**
     * @param Model $instance
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function setInstance(Model $instance)
    {
        $typeName = get_class($instance);
        if ($this->resourceClassName !== $typeName) {
            throw new Exception('Expecting type \'' . $this->resourceClassName . '\', got \'' . $typeName . '\'');
        }

        $this->instance = $instance;
    }

    /**
     * @param Request $request
     * @return Model
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function getInstance(Request $request) : Model
    {
        if (isset($this->instance)) {
            return $this->instance;
        }

        $propertyName = $this->getResourceName('camel');
        if ($request->route($propertyName) === null) {
            throw new Exception('Instance not found.');
        }

        /** @var Model $instance */
        $instance = $request->route($propertyName);
        $this->setInstance($instance);

        return $this->instance;
    }

    /**
     * @param Request $request
     *
     * @return array
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function getFillableFromRequest(Request $request) : array
    {
        /** @var Model $resource */
        $resource = Container::getInstance()->make($this->resourceClassName);

        return collect($request->only($resource->getFillable()))
            ->filter(fn($value) => $value !== '-1' && $value !== -1)
            ->toArray();
    }
}