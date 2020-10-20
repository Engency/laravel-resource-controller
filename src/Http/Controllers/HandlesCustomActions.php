<?php

namespace Engency\Http\Controllers;

use Engency\Http\Response\DefaultResponse;
use Engency\Http\Response\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;

trait HandlesCustomActions
{
    /**
     * @param Request $request
     * @param string  $customAction
     * @param array   $data
     *
     * @return Response|array
     */
    private function triggerCustomAction(Request $request, string $customAction, array $data = [])
    {
        try {
            $methodName = Str::camel('action-' . $customAction);
            $reflection = new ReflectionClass(static::class);
            if ($reflection->hasMethod($methodName)) {
                return $this->$methodName($request, $data);
            }
        } catch (ReflectionException $e) {
            // dispose error
        }

        $message = Lang::get('resource-controller:messages.unknown-action', ['action' => $customAction]);

        return DefaultResponse::pageNotFound($message);
    }
}
