<?php

namespace Engency\Http\Response;

use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Trait CanShowBinaryInline
 *
 * @package Engency\Http\Response
 */
trait CanShowBinaryInline
{

    private bool     $suggestShowBinaryInline = false;
    private ?string  $downloadName            = null;
    private ?string  $etag                    = null;
    private bool     $forceInline             = true;
    private bool     $removeAfterSend         = false;

    /**
     * @param string|null $downloadWithName
     * @param string|null $version
     * @return $this
     */
    public function download(string $downloadWithName = null, string $version = null)
    {
        $this->suggestShowBinaryInline = true;
        $this->forceInline             = false;
        $this->etag                    = $version;
        $this->downloadName            = $downloadWithName;

        return $this;
    }

    /**
     * @param string|null $version
     * @return $this
     */
    public function showInline(string $version = null)
    {
        $this->suggestShowBinaryInline = true;
        $this->forceInline             = true;
        $this->etag                    = $version;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function removeAfterSend(bool $value = true)
    {
        $this->removeAfterSend = $value;

        return $this;
    }

    /**
     * @return bool
     */
    protected function canShowBinaryInline() : bool
    {
        return $this->suggestShowBinaryInline;
    }

    /**
     * @return Response
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function doShowBinaryInline() : Response
    {
        if ($this->forceInline) {
            return $this->getResponseFactory()
                        ->file($this->getFilePath(), $this->getShowBinaryHeaders())
                        ->deleteFileAfterSend($this->removeAfterSend);
        } else {
            return $this->getResponseFactory()
                        ->download($this->getFilePath(), $this->downloadName, $this->getShowBinaryHeaders())
                        ->deleteFileAfterSend($this->removeAfterSend);
        }
    }

    /**
     * @return array
     */
    private function getShowBinaryHeaders() : array
    {
        switch ($this->downloadType) {
            case 'jpg':
                return $this->getInlineHeadersForJpgType();
            default:
                return [];
        }
    }

    /**
     * @return array
     */
    private function getInlineHeadersForJpgType() : array
    {
        return [
            'Content-Type' => 'image/jpg',
            'etag'         => $this->etag,
        ];
    }

    /**
     * @return string
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getFilePath() : string
    {
        $path = $this->getData()->toArray()['path'] ?? null;
        if ($path === null || !file_exists($path)) {
            throw new Exception('File does not exist');
        }

        return $path;
    }
}
