<?php

namespace Engency\Test;

use Engency\Http\Response\Response;

class ResourceControllerIndexTest extends BaseTestCase
{

    public function testIndex()
    {
        $response = $this->call('index');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(( new UserController() )->testScope(), $response->getData()->toArray()['items']);
    }

    public function testCreate()
    {
        $response = $this->call('create', new User(['name' => 'name']));

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testStore()
    {
        $newName = 'new name';

        $response = $this->call(
            'store',
            null,
            ['name' => $newName]
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertHasNotice($response, 'success', 'Resource-controller:messages.stored');
    }

    public function testShow()
    {
        $response = $this->call('show', new User(['name' => 'name']));

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testEdit()
    {
        $response = $this->call('edit', new User(['name' => 'name']));

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testUpdate()
    {
        $instance         = new User(['name' => 'name']);
        $newName          = 'new name';
        $invalidAttribute = 'invalidAttribute';

        $response = $this->call(
            'update',
            $instance,
            [
                'name'            => $newName,
                $invalidAttribute => 'random'
            ]
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($newName, $instance->name);
        $this->assertArrayNotHasKey($invalidAttribute, $instance->getAttributes());
        $this->assertHasNotice($response, 'success', 'Resource-controller:messages.updated');
    }

    public function testDestroy()
    {
        $instance = new User(['name' => 'name']);

        $response = $this->call('destroy', $instance);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertHasNotice($response, 'success', 'Resource-controller:messages.deleted');
    }

}