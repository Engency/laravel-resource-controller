<?php

namespace Engency\Http\Resource;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

trait ResourceNaming
{
    protected string $resourceClassName;

    /**
     * @return string
     */
    public function getResourceClassName() : string
    {
        return $this->resourceClassName;
    }

    /**
     * @param string $caseType
     *
     * @return string
     */
    public function getResourceName(string $caseType = 'kebab') : string
    {
        $possibleCasings = ['kebab', 'camel', 'studly', 'title',];
        if (!in_array($caseType, $possibleCasings)) {
            $caseType = $possibleCasings[0];
        }

        $name = class_basename($this->getResourceClassName());

        return Str::$caseType($name);
    }

    /**
     * @param string $exportFormat
     *
     * @return string
     */
    public function getTranslatedResourceName(string $exportFormat = '')
    {
        if (strlen($exportFormat) > 0) {
            $exportFormat = '.' . $exportFormat;
        }

        // todo, remove try-catch statement
        try {
            // todo add resource translations
            return Lang::get('resources.' . $this->getResourceName() . $exportFormat);
        } catch (\Exception $e) {
            return 'resources.' . $this->getResourceName() . $exportFormat;
        }
    }
}