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
            } elseif (( $item = $this->data[$firstKey] ) instanceof Model && Str::camel(class_basename($item)) === $firstKey) {
                return $this->exportItemForJsonResponse($item);
            }
        }

        return collect($this->data)->map(fn($item) => $this->exportItemForJsonResponse($item))->toArray();
    }

    private function exportItemForJsonResponse($item)
    {
        if (isset($this->customDataFormat) && $item instanceof ExportsCustomDataFormats) {
            return $item->toArrayFormat($this->customDataFormat);
        } elseif ($item instanceof LengthAwarePaginator) {
            return $item->items();
        } elseif ($item instanceof Arrayable) {
            return $item->toArray();
        } else {
            return $item;
        }
    }
}