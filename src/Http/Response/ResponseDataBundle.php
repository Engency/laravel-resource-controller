<?php

namespace Engency\Http\Response;

use Engency\DataStructures\ExportsCustomDataFormats;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ResponseDataBundle implements Arrayable
{
    private array  $data;
    private string $customDataFormat;

    private static string $itemsKey = 'items';

    public function __construct($data)
    {
        $d = [];
        if ($data instanceof Model) {
            $d[Str::camel(class_basename($data))] = $data;
        } elseif ($data instanceof Collection || $data instanceof LengthAwarePaginator) {
            $d[self::$itemsKey] = $data;
        } elseif (is_array($data)) {
            $d = $data;
        } elseif (is_string($data) && file_exists($data)) {
            $d = ['path' => $data];
        }

        $this->data = $d;
    }

    /**
     * @param string $format
     */
    public function setDataFormat(string $format)
    {
        $this->customDataFormat = $format;
    }

    /**
     * @return array|array[]
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getDataForJsonResponse()
    {
        if (count($this->data) === 1) {
            $firstKey = array_key_first($this->data);
            if ($firstKey === self::$itemsKey) {
                return $this->data[self::$itemsKey];
            }

            $attIsModel = $this->data[$firstKey] instanceof Model;
            if ($attIsModel && Str::camel(class_basename($this->data[$firstKey])) === $firstKey) {
                return $this->exportItemForJsonResponse($this->data[$firstKey]);
            }
        }

        return collect($this->data)->map(fn($item) => $this->exportItemForJsonResponse($item))->toArray();
    }

    private function exportItemForJsonResponse($item)
    {
        if (isset($this->customDataFormat) && $item instanceof ExportsCustomDataFormats) {
            return $item->toArrayFormat($this->customDataFormat);
        } elseif ($item instanceof LengthAwarePaginator) {
            if (method_exists($item, 'toArray')) {
                $data         = $item->toArray();
                $data['data'] = $this->exportToCustomDataFormat($item->items());

                return $data;
            }

            return $item;
        } elseif ($item instanceof Arrayable) {
            return $this->exportToCustomDataFormat($item->toArray());
        } else {
            return $item;
        }
    }

    /**
     * @param Arrayable|array $items
     * @return array
     */
    private function exportToCustomDataFormat($items) : array
    {
        return collect($items)->map(function ($subItem) {
            if ($subItem instanceof ExportsCustomDataFormats) {
                return $subItem->toArrayFormat($this->customDataFormat);
            }

            return $subItem->toArray();
        })->toArray();
    }
}
