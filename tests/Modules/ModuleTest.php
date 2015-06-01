<?php

use Wasp\Test\TestCase;

/**
 * Test Case for the Module Manager Class
 *
 * @package Wasp
 * @subpackage Tests\Modules
 * @author Dan Cox
 */
class ModuleTest extends TestCase
{
	/**
	 * Module settings
	 *
	 * @var Array
	 */
	protected $settings;

	/**
	 * Setup test env
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function setUp()
	{
		parent::setUp();

		$this->settings = [
			'available_record'		=> __DIR__ . '/modules.json',
			'cache_file'			=> __DIR__ . '/cache.json'
		];
	}
	
	/**
	 * Test that the settings are loaded in correct sections
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_loadSettings()
	{
		$manager = $this->DI->get('module.manager');		
		$manager->loadSettings($this->settings);
			
		$settings = $manager->getSettings();

		$this->assertEquals(2, count($settings));

		$record = $settings->get('Record');
		$cache = $settings->get('Cache');

		$this->assertEquals(1, count($record));
		$this->assertEquals(1, count($cache));
	}

	/**
	 * Test activating a module
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_activateAModule()
	{
		$manager = $this->DI->get('module.manager');
		$manager->loadSettings($this->settings);
		
		$manager->initFiles();		
		$manager->activate('test');

		$cache = $manager->getCache()->data();
		$records = $cache->all();

		$this->assertEquals(1, count($records));
		$this->assertTrue(array_key_exists('test', $records));
	}

	/**
	 * Deactivate a module
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_removeModule()
	{
		$manager = $this->DI->get('module.manager');
		$manager->loadSettings($this->settings);
		$manager->initFiles();
		
		$manager->deactivate('test');

		$cache = $manager->getCache()->data();
		$records = $cache->all();

		$this->assertEquals(0, count($records));
	}

	/**
	 * Test deactivating an unknown module
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_deactivateUnknownModule()
	{
		$this->setExpectedException('Wasp\Exceptions\Modules\UnknownModule');

		$manager = $this->DI->get('module.manager');
		$manager->loadSettings($this->settings);
		
		$manager->initFiles();
		$manager->deactivate('Fake');
	}

	/**
	 * Test Deactivating a module that is not active
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_decactivateUnactiveModule()
	{
		$this->setExpectedException('Wasp\Exceptions\Modules\ModuleNotActive');

		$manager = $this->DI->get('module.manager');
		$manager->loadSettings($this->settings);

		$manager->initFiles();
		$manager->deactivate('test');
	}

	/**
	 * Test that exception is thrown when an unknown module is activated
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_throwExceptionOnUnknownModule()
	{
		$this->setExpectedException('Wasp\Exceptions\Modules\UnknownModule');

		$manager = $this->DI->get('module.manager');
		$manager->loadSettings($this->settings);

		$manager->initFiles();
		$manager->activate('Fake');
	}

	/**
	 * Test the manager's build instance
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_buildInstance()
	{
		$manager = $this->DI->get('module.manager');

		$manager->loadSettings($this->settings)->initFiles();

		$this->assertInstanceOf('Wasp\Modules\ModuleBuilder', $manager->getBuilder());
	}

} // END class ModuleTest extends TestCase
