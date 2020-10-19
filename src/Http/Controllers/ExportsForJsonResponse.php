<?php

namespace Engency\Http\Controllers;

use Engency\DataStructures\ExportsCustomDataFormats;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait ExportsForJsonResponse
{
    /**
     * @param Request $request
     * @param array   $data
     * @return array
     */
    protected function exportDataContainingItemForJsonResponse(Request $request, array $data) : array
    {
        $keyName = $this->getManagedResource()->getResourceName('camel');

        if (!isset($data[$keyName])) {
            return $data;
        }

        $format         = $this->getExportFormat($request) ?? 'default';
        $data[$keyName] = $this->exportInstanceForJsonResponse($request, $data[$keyName], $format);

        return $data;
    }

    /**
     * @param Request $request
     * @param array   $data
     * @return array
     */
    protected function exportDataContainingListForJsonResponse(Request $request, array $data) : array
    {
        if (!isset($data['items'])) {
            return $data;
        }

        $items = $data['items'];
        if (!( $items instanceof Collection )) {
            $items = collect($items);
        }

        if ($items->count() === 0) {
            return $items;
        }

        $format        = $this->getExportFormat($request) ?? 'default';
        $data['items'] = $items
            ->map(fn($item) => $this->exportInstanceForJsonResponse($request, $item, $format))
            ->toArray();

        return $data;
    }

    /**
     * @param Request $request
     * @param         $instance
     * @param string  $suggestedFormat
     * @return array
     * @noinspection PhpUnusedParameterInspection
     */
    protected function exportInstanceForJsonResponse(Request $request, $instance, string $suggestedFormat) : array
    {
        if ($instance instanceof ExportsCustomDataFormats) {
            return $instance->toArrayFormat($suggestedFormat);
        }

        return $instance;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    final protected function getExportFormat(Request $request) : ?string
    {
        if (!$request->has('exportFormat')) {
            return null;
        }

        return (string) $request->get('exportFormat');
    }
}