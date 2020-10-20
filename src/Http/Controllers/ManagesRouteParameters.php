<?php

namespace Engency\Http\Controllers;

use Engency\Http\ManagedResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait ManagesRouteParameters
{
    private array $requestParameters = [];

    /**
     * @return ManagedResource
     */
    abstract public function getManagedResource() : ManagedResource;

    /**
     * @param array $parameters
     */
    public function loadRequestParameters(array $parameters)
    {
        $this->requestParameters = [];

        foreach ($parameters as $key => $value) {
            $key = Str::camel($key);
            if (property_exists($this, $key)) {
                $this->$key                    = $value;
                $this->requestParameters[$key] = $value;
            }
        }

        $this->selectSubjectInstance();
    }

    /**
     * @param bool|null $onlyIdentifiers
     *
     * @return array
     */
    protected function getAttributesFromScope(?bool $onlyIdentifiers = true) : array
    {
        $data         = [];
        $resourceName = $this->getManagedResource()->getResourceName('camel');

        foreach ($this->requestParameters as $key => $value) {
            if ($key === $resourceName) {
                continue;
            }

            if ($value instanceof Model) {
                $data[$onlyIdentifiers ? ucfirst($key) : $key] =
                    $onlyIdentifiers ? $value->{$value->getKeyName()} : $value;
            }
        }

        return $data;
    }

    /**
     *
     */
    private function selectSubjectInstance()
    {
        $managedResource = $this->getManagedResource();
        $propertyName    = $managedResource->getResourceName('camel');
        if (property_exists($this, $propertyName)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $managedResource->setInstance($this->{$propertyName});

            return;
        }

        // todo, instance was not found... throw 404 maybe?
    }
}
