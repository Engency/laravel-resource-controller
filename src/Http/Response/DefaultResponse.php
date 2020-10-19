<?php /** @noinspection PhpUnused */

namespace Engency\Http\Response;

use Illuminate\Http\Response as IlluminateResponse;

/**
 * Class DefaultResponse
 *
 * @package Engency\Http\Response
 */
class DefaultResponse
{

    /**
     * @param string $message
     *
     * @return Response
     */
    public static function pageNotFound(string $message = 'Page not found')
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_NOT_FOUND)
            ->addNotice($message)
            ->view('pages.error.notfound');
    }

    /**
     * @param string $message
     * @return Response
     */
    public static function unauthorized(string $message = 'Please authenticate before proceeding.')
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_UNAUTHORIZED)
            ->addNotice($message)
            ->view('pages.error.unauthorized');
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    public static function forbidden(string $message = 'You don\'t have permissions to access this page.')
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_FORBIDDEN)
            ->addNotice($message)
            ->view('pages.error.forbidden');
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    public static function unprocessable(string $message)
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->addNotice($message)
            ->view('pages.error.conflict');
    }

    /**
     * @param string $message
     *
     * @return Response
     */
    public static function internalError(string $message = 'An unknown error occurred.')
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)
            ->addNotice($message)
            ->view('pages.error.500');
    }

    /**
     * @param string $field
     * @param string $message
     * @return Response
     */
    public static function validationErrorOnField(string $field, string $message) : Response
    {
        return ( new Response() )
            ->httpError(IlluminateResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->addNoticeOnField($field, $message)
            ->view('pages.error.conflict');
    }
}
