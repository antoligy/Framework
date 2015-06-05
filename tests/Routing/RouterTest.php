<?php

use Wasp\Test\TestCase,
	Wasp\DI\ServiceMockery;

/**
 * Test case for the router class
 *
 * @package Wasp
 * @subpackage Tests\Routing
 * @author Dan Cox
 */
class RouterTest extends TestCase
{

	/**
	 * Set up test env
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function setUp()
	{
		$mock = new ServiceMockery('dispatcher');
		$mock->add();

		parent::setUp();
	}

	/**
	 * Test resolving a defined route
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_resolveRoute()
	{
		$dispatcher = $this->DI->get('dispatcher');
		$dispatcher->shouldReceive('dispatch')->with('TestController::Action', Array(), Array(), NULL)->once();

		// Fabricate a request
		$this->DI->get('request')->make('/test', 'GET');

		$this->DI->get('route')
				 ->add('test.router', '/test', ['GET'], ['controller' => 'TestController::Action']);

		$this->DI->get('router')->resolve('/test');
	}

	/**
	 * Test resolving a route with arguments
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_withArguments()
	{
		$dispatcher = $this->DI->get('dispatcher');
		$dispatcher->shouldReceive('dispatch')->with('TestController::Action', ['id' => 4], Array(), NULL)->once();

		$this->DI->get('request')->make('/test/4', 'GET');
		$this->DI->get('route')
				 ->add('test.params', '/test/{id}', ['GET'], ['controller' => 'TestController::Action']);

		$this->DI->get('router')->resolve('/test/4');
	}

	/**
	 * Test dispatching a route with a trigger
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_withTrigger()
	{
		$dispatcher = $this->DI->get('dispatcher');
		$dispatcher->shouldReceive('dispatch')->with('TestController::Action', Array(), Array('before' => ['filter' => 'TestFilter', 'method' => 'method']), NULL)->once();

		$this->DI->get('request')->make('/test', 'POST');
		$this->DI->get('route')
				 ->add('test.trigger', '/test', ['POST'], ['controller' => 'TestController::Action', 'before' => ['TestFilter', 'method']]);

		$this->DI->get('router')->resolve('/test');

	}

	/**
	 * Test a resource route
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_ResourceRoute()
	{
		$dispatcher = $this->DI->get('dispatcher');
		$dispatcher->shouldReceive('dispatch')->with('Wasp\Controller\RestController::show', Array('id' => 3), Array(), 'Wasp\Test\Entity\Entities\Test');

		$this->DI->get('request')->make('/test', 'GET');
		$this->DI->get('route')
			->resource('resource', '/test', 'Wasp\Test\Entity\Entities\Test');

		$this->DI->get('router')->resolve('/test/3');
	}

} // END class RouterTest extends TestCase
