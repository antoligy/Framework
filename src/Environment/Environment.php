<?php namespace Wasp\Environment;

use Wasp\Application\Application,
	Wasp\DI\DI;


/**
 * Base environment class, Determines actions taken on Application Launch.
 *
 * @package Wasp
 * @subpackage Environment
 * @author Dan Cox
 */
class Environment
{
	/**
	 * The App instance
	 *
	 * @var Object
	 */
	protected $App;

	/**
	 * The DI instance
	 *
	 * @var Object
	 */
	protected $DI;

	/**
	 * Sets the App and DI instances and Calls the Child Class function if exists
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function load(Application $App)
	{
		$this->App = $App;
		$this->DI = new DI(dirname(__DIR__) . '/Config/');

		// If the Child Environment Class has a Setup function, call it.
		if(method_exists($this, 'setup'))
		{
			$this->setup();
		}

		return $this;
	}

	/**
	 * Loads the DI with a specific Service File
	 * Important to note that this function does not use the CACHED DI Container.
	 *
	 * @param String $serviceFile - the name of the service YAML file
	 * @return void
	 * @author Dan Cox
	 */
	public function createDI($serviceFile = 'core')
	{
		$this->DI->build()->load($serviceFile);
	}

	/**
	 * Creates a DI instance from Cache;
	 *
	 * @param String $serviceFile - the name of the service YAML file
	 * @return void
	 * @author Dan Cox
	 */
	public function createDIFromCache($serviceFile = 'core')
	{
		$this->DI->buildContainerFromCache($serviceFile);
	}

	/**
	 * Returns the DI Instance
	 *
	 * @return Object|NULL
	 * @author Dan Cox
	 */
	public function getDI()
	{
		return $this->DI;
	}


} // END class Environment
