<?php /** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */

namespace Engency\Http\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CanDownload
 *
 * @package Engency\Http\Response
 */
trait CanDownload
{

    private bool    $suggestDownload = false;
    private string  $downloadType    = 'json';
    private ?string $downloadName    = null;

    /**
     * @param string $type
     * @param string $name
     *
     * @return $this
     */
    public function downloadDataAs(string $type = 'json', string $name = 'download')
    {
        $this->downloadType    = $type;
        $this->downloadName    = $name;
        $this->suggestDownload = true;

        return $this;
    }

    /**
     * @return bool
     */
    protected function canDownload() : bool
    {
        return $this->suggestDownload;
    }

    /**
     * @return Response
     */
    protected function doDownload() : Response
    {
        return $this->getResponseFactory()
                    ->make($this->getData()->getDataForJsonResponse(), $this->getHttpStatusCode(), $this->getHeaders());
    }

    /**
     * @return array
     */
    private function getHeaders() : array
    {
        return $this->getHeadersForJsonType();
    }

    /**
     * @return array
     */
    private function getHeadersForJsonType() : array
    {
        return [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $this->downloadName . '.json"',
        ];
    }
}
