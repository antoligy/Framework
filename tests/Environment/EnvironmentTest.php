<?php

use Wasp\Environment\Environment,
	Wasp\DI\ServiceMockery,
	Wasp\DI\ServiceMockeryLibrary,
	Wasp\Application\Profile,
	Symfony\Component\Filesystem\Filesystem,
	Wasp\Application\Application,
	\Mockery as m;

/**
 * Environment class test
 *
 * @package Wasp
 * @subpackage Tests\Environment
 * @author Dan Cox
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * App instance
	 *
	 * @var Object
	 */
	protected $app;

	/**
	 * Setup Class env
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function setUp()
	{	
		$profile = new Profile(m::mock('filesystem'));

		$this->app = new Application;
		$this->app->profile = $profile;
	}

	/**
	 * Test building the DI
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_buildDI()
	{
		$env = new Environment();
		$env->load($this->app);

		$env->createDI('core');

		$this->assertInstanceOf('Wasp\DI\DI', $env->getDI());
		$this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerBuilder', $env->getDI()->getContainer());
		$this->assertInstanceOf('Wasp\Application\Application', $env->getDI()->get('application'));
		$this->assertInstanceOf('Wasp\Application\Profile', $env->getDI()->get('profile'));
	}

	/**
	 * Build the DI from the cache
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_buildDIFromCache()
	{
		$env = new Environment;
		$env->load($this->app);
		$env->createDIFromCache('core');

		$this->assertInstanceOf('Wasp\DI\DI', $env->getDI());
		$this->assertInstanceOf('Wasp\Application\Cache\AppCache', $env->getDI()->getContainer());
	}

	/**
	 * Test connecting to a name connection
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_connection()
	{
		$env = new Environment;
		$env->load($this->app);
		$env->createDI('core');

		$env->getDI()->get('connections')->add('test', [
			'driver'			=> 'pdo_mysql',
			'user'				=> 'user',
			'dbname'			=> 'wasp',
			'models'			=> ENTITIES	
		]);

		$env->connectTo('test');
	}

	/**
	 * Test that the connection throws an exception which is caught 
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_connectionFailsWithBadDetails()
	{
		$rmock = new ServiceMockery('response');
		$rmock->add();

		$env = new Environment;
		$env->load($this->app);
		$env->getDI()->addCompilerPass( new \Wasp\DI\Pass\MockeryPass );

		$env->createDI('core');

		$env->getDI()->get('connections')->add('test', [
			'driver'			=> 'baddriver',
			'user'				=> '',
			'dbname'			=> '',
			'models'			=> ENTITIES	
		]);

		$resp = $env->getDI()->get('response');

		$resp->shouldReceive('make')->once()->andReturn($resp);
		$resp->shouldReceive('send')->once();

		$env->connectTo('test');
	}

	/**
	 * Test similar to above but with a missing connection
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_connectionWithUnknownConnection()
	{
		$rmock = new ServiceMockery('response');
		$rmock->add();

		$env = new Environment;
		$env->load($this->app);
		$env->getDI()->addCompilerPass( new \Wasp\DI\Pass\MockeryPass );

		$env->createDI('core');

		$resp = $env->getDI()->get('response');

		$resp->shouldReceive('make')->once()->andReturn($resp);
		$resp->shouldReceive('send')->once();

		$env->connectTo('missing');
	}

	/**
	 * Test that we can connect using profile settings
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_connectThroughProfileSettings()
	{
		$profile = new Profile(new FileSystem);
		$profile->setSettings(['application' => Array('default_connection' => 'test')]);

		$this->app->profile = $profile;

		$env = new Environment;
		$env->load($this->app);
		$env->createDI('core');

		$env->getDI()->get('connections')->add('test', [
			'driver'			=> 'pdo_mysql',
			'user'				=> 'user',
			'dbname'			=> 'wasp',
			'models'			=> ENTITIES
		]);

		$status = $env->connect();

		$this->assertTrue($status);
	}

	/**
	 * Test Adding connections
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_addingConnections()
	{
		$profile = new Profile(new FileSystem);
		$profile->setSettings(['database' => ['connections' => ['default' => ['driver' => 'pdo_mysql', 'user' => 'user']]]]);
		
		$this->app->profile = $profile;

		$env = new Environment;
		$env->load($this->app);
		$env->createDI('core');

		$env->setupConnections();

		$this->assertTrue(is_object($env->getDI()->get('connections')->find('default')));
	}

	/**
	 * Test gracefull handling of lack of settings
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_connectWithoutSettingsSet()
	{
		$profile = new Profile(new FileSystem);
		$profile->setSettings(['application' => Array('default_connection' => '')]);

		$this->app->profile = $profile;

		$env = new Environment;
		$env->load($this->app);
		$env->createDI('core');

		$status = $env->connect();

		$this->assertFalse($status);
	}

	/**
	 * Test adding the twig configuration
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function test_templatingWithTwigConfig()
	{
		$profile = new Profile(new Filesystem);
		$profile->setSettings(['templates' => Array('twig' => Array('debug' => true))]);

		$this->app->profile = $profile;

		$env = new Environment;
		$env->load($this->app);
	
		$env->getDI()->addCompilerPass(new \Wasp\DI\Pass\MockeryPass);

		$t = new ServiceMockery('twigengine');
		$t->add();

		$temp = new ServiceMockery('template');
		$temp->add();

		$env->createDI();
		$twig = $env->getDI()->get('twigengine');
		$template = $env->getDI()->get('template');

		$twig->shouldReceive('create')->once()->with(Array('debug' => true));
		$template->shouldReceive('setDirectory')->andReturn($template);
		$template->shouldReceive('getDirectory')->andReturn(__DIR__);
		$template->shouldReceive('addEngine')->andReturn($template);
		$template->shouldReceive('start');

		$env->startTemplating(dirname(__DIR__) . '/Templating/Templates/');

		// Clean Up
		$library = new ServiceMockeryLibrary();
		$library->clear();
	}

	
} // END class EnvironmentTest extends \PHPUnit_Framework_TestCase

