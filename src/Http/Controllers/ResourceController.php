<?php

namespace Engency\Http\Controllers;

use Engency\Http\ManagedResource;
use Engency\Http\Response\Response;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

/**
 * Class ResourceController
 *
 * @package App\Http\Controllers\Resource
 */
abstract class ResourceController extends BaseController
{
    use PreparesResponse;
    use ManagesRouteParameters;
    use HandlesCustomActions;
    use ExportsForJsonResponse;

    protected ManagedResource $managedResource;

    private array $basicResourceMethods = ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy',];

    /**
     * @var Model|null
     */
    private ?Model $subject = null;

    public function __construct(string $resourceClassName)
    {
        $this->managedResource = new ManagedResource($resourceClassName);
    }

    /**
     * @return ManagedResource
     */
    public function getManagedResource() : ManagedResource
    {
        return $this->managedResource;
    }

    /**
     * Execute an action on the controller.
     *
     * @param string|mixed $method
     * @param array        $parameters
     *
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     *
     * @noinspection PhpMissingParamTypeInspection
     */
    public function callAction($method, $parameters)
    {
        if (!in_array($method, $this->basicResourceMethods)) {
            return parent::callAction($method, $parameters);
        }

        $this->loadRequestParameters($parameters);

        $request  = Container::getInstance()->make(Request::class);
        $response = $this->performAction($request, $method);

        return $this->prepareResponse($method, $response);
    }

    /**
     * @param Request $request
     * @param string  $method
     *
     * @return Response|array
     */
    private function performAction(Request $request, string $method)
    {
        if ($method == 'update' || $method == 'store') {
            return $this->performDataAcceptingAction($request, $method);
        }

        return call_user_func_array([$this, $method], [$request]);
    }

    /**
     * @param Request $request
     * @param string  $method
     *
     * @return Response|array
     */
    private function performDataAcceptingAction(Request $request, string $method)
    {
        $fieldData = $this->getManagedResource()->getFillableFromRequest($request);

        // check if a custom action is being triggered
        if ($request->has('action')) {
            $customData = $request->get('data', []);
            $allData    = array_merge($customData, $fieldData);
            $action     = (string) $request->get('action');

            return $this->triggerCustomAction($request, $action, $allData);
        }

        return call_user_func_array([$this, $method], [$request, $fieldData]);
    }

    /**
     * @param Request $request
     * @return Model
     * @noinspection PhpDocMissingThrowsInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    protected function getResourceInstance(Request $request) : Model
    {
        return $this->managedResource->getInstance($request);
    }

    /**
     * @param string $action
     *
     * @return string
     */
    protected function getViewForAction(string $action)
    {
        return $this->getViewPrefix() . $action;
    }

    /**
     * @param string $case
     * @return string
     */
    protected function getTranslatedMessage(string $case) : string
    {
        $resourceName = $this->managedResource->getTranslatedResourceName('noun-with-article');
        $messageKey   = 'resource-controller:messages.' . $case;

        // todo, remove try-catch statement
        try {
            // todo, correctly implement language file
            $message = Lang::get($messageKey, ['resource' => $resourceName]);
        } catch (Exception $e) {
            $message = $messageKey;
        }

        return ucfirst($message);
    }

    /**
     * @param string   $path
     * @param int|null $dirsUp
     *
     * @return string
     */
    public function toPathInCurrentScope(string $path = '', ?int $dirsUp = 1)
    {
        $urlResolver         = $this->getUrlResolver();
        $currentUrlParticles = explode('/', $urlResolver->current());
        if ($dirsUp == 0) {
            $sliced = $currentUrlParticles;
        } else {
            $sliced = array_slice($currentUrlParticles, 3, 0 - $dirsUp);
        }

        if (strlen($path) > 0) {
            $path = '/' . $path;
        }

        return $urlResolver->to(implode('/', $sliced) . $path);
    }

    /**
     * @return UrlGenerator
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getUrlResolver() : UrlGenerator
    {
        return Container::getInstance()->make('url');
    }
}
