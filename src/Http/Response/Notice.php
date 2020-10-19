<?php

namespace Engency\Http\Response;

/**
 * Class Notice
 *
 * @package Engency\Http\Response
 */
class Notice
{
    public const NOTICE_ALERT   = 'alert';
    public const NOTICE_SUCCESS = 'success';
    public const NOTICE_WARNING = 'warning';
    public const NOTICE_ERROR   = 'error';
    public const NOTICE_INFO    = 'information';
    public const NOTICES        = [
        self::NOTICE_ALERT,
        self::NOTICE_SUCCESS,
        self::NOTICE_WARNING,
        self::NOTICE_ERROR,
        self::NOTICE_INFO,
    ];
}
