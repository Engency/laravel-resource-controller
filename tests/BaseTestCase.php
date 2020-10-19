<?php
/**
 * @noinspection PhpDocMissingThrowsInspection
 * @noinspection PhpUnhandledExceptionInspection
 */

namespace Engency\Test;

use Engency\Http\Controllers\DefaultResourceActions;
use Engency\Http\Controllers\ResourceController;
use Engency\Http\Response\Notice;
use Engency\Http\Response\Response;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;

class User extends Model
{
    protected $fillable = [
        'name'
    ];

    public function validateAndUpdate(array $data, string $occasion)
    {
        $this->attributes = $data;

        return null;
    }

    public static function validateAndCreateNew(array $data)
    {
        return new self($data);
    }

    public function getKey()
    {
        return 1;
    }
}

class UserController extends ResourceController
{
    use DefaultResourceActions;

    public function __construct()
    {
        parent::__construct(User::class);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder|Collection|\Illuminate\Database\Query\Builder
     */
    protected function getScope(Request $request)
    {
        return $this->testScope();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|Collection
     */
    public function testScope()
    {
        return collect([new User(['name' => 'A']), new User(['name' => 'B']), new User(['name' => 'C'])]);
    }

    /**
     * @param Request $request
     * @param mixed   $instance
     * @return Response
     */
    protected function redirectAfterStore(Request $request, $instance) : Response
    {
        return $this
            ->success([$this->getManagedResource()->getResourceName('camel') => $instance])
            ->redirectBack()
            ->addNotice($this->getTranslatedMessage('stored'), Notice::NOTICE_SUCCESS)
            ->exportDataForJsonResponse(
                fn(array $data) => $this->exportDataContainingItemForJsonResponse($request, $data)
            );
    }
}

class BaseTestCase extends TestCase
{

    /**
     * @param string     $method
     * @param Model|null $instance
     * @param array      $attributes
     * @return Response
     */
    public function call(string $method = 'index', ?Model $instance = null, array $attributes = []) : Response
    {
        $container = Container::getInstance();
        $request   = $this->decorateRequest($instance, $attributes);

        {
            // store request in container
            $container->instance(Request::class, $request);

            $response = ( new UserController )->callAction($method, []);

            // forget request
            $container->forgetInstance(Request::class);
        }

        return $response;
    }

    /**
     * @param Model|null $instance
     * @param array|null $attributes
     * @return Request
     */
    public function decorateRequest(?Model $instance = null, ?array $attributes = null) : Request
    {
        $request = new Request();

        if ($instance !== null) {
            $route = ( new Route([], '', []) )->bind($request);
            $route->setParameter(Str::kebab(class_basename($instance)), $instance);
            $request->setRouteResolver(fn() => $route);
        }

        if ($attributes !== null) {
            $request->query = new InputBag($attributes);
        }

        return $request;
    }

    protected function assertHasNotice(Response $response, string $noticeType, string $message = null)
    {
        $this->assertNotNull($response->getBag());
        $this->assertArrayHasKey('notice-' . $noticeType, $response->getBag()->toArray());
        if ($message !== null) {
            $this->assertContains($message, $response->getBag()->toArray()['notice-' . $noticeType]);
        }
    }
}