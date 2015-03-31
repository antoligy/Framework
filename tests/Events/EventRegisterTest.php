<?php

use Wasp\Test\TestCase,
	Wasp\DI\ServiceMockery;


/**
 * Test case for the register events class
 *
 * @package Wasp
 * @subpackage Tests\Events
 * @author Dan Cox
 */
class EventRegisterTest extends TestCase
{
	
	/**
	 * Set up test env
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function setUp()
	{
		$dispatch = new ServiceMockery('event_dispatch');
		$dispatch->add();

		parent::setUp();
	}

	/**
	 * Test adding registered event type classes
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_AddingRegisteredEventTypes()
	{
		$register = $this->DI->get('event_register');

		// Register some event types
		$register->registerTypes(['event.name' => 'TestEventRegistrationClass']);
		$register->registerType('event.test', 'SecondEventRegistrationClass');

		$this->assertEquals(
			['event.name' => 'TestEventRegistrationClass', 'event.test' => 'SecondEventRegistrationClass'],
			$register->getEventTypes()
		);
	}

	/**
	 * Test a working event type class
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_AddingAWorkingEventType()
	{
		$dispatch = $this->DI->get('event_dispatch');
		$dispatch->shouldReceive('dispatch')->once();

		$register = $this->DI->get('event_register');
		$register->registerType('event.test', 'Wasp\Test\Events\EventTypes\TestEventRegisterType');

		$event = $register->initEvent('event.test', Array('foo' => 'test', 'bar' => 'test2'));
		$this->assertInstanceOf('Wasp\Test\Events\EventTypes\TestEventRegisterType', $event);
		$this->assertEquals('test', $event->getFoo());
		$this->assertEquals('test2', $event->getBar());

		$register->fire('event.test', Array('foo' => 'test', 'bar' => 'test'));
	}

} // END class EventRegisterTest extends TestCase

