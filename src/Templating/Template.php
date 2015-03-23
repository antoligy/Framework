<?php namespace Wasp\Templating;

use Symfony\Component\Templating\DelegatingEngine,
	Wasp\Exceptions\Templating\DirectoryNotSet,
	Wasp\DI\DependencyInjectionAwareTrait;

/**
 * Template class
 *
 * @package Wasp
 * @subpackage Templating
 * @author Dan Cox
 */
class Template
{
	use DependencyInjectionAwareTrait;
	
	/**
	 * Instance of the Delegation Engine
	 *
	 * @var Symfony\Component\Templating\DelegatingEngine
	 */
	protected $delegator;

	/**
	 * The directory that templates are kept in
	 *
	 * @var String
	 */
	protected $directory;

	/**
	 * An Array of available template engines
	 *
	 * @var Array
	 */
	protected $engines;

	/**
	 * Creates a delegating engine.
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function start()
	{
		if(is_null($this->directory))
		{
			throw new DirectoryNotSet;
		}

		$this->delegator = new DelegatingEngine($this->engines);
	}

	/**
	 * Renders the template from its name with the appropriate engine
	 *
	 * @return void
	 * @author Dan Cox
	 */
	public function make($template, $params = Array())
	{
		return $this->delegator->render($template, $params);
	}

	/**
	 * Sets the template directory
	 *
	 * @param String $directory
	 * @return Template
	 * @author Dan Cox
	 */
	public function setDirectory($directory)
	{
		$this->directory = $directory;
		return $this;
	}

	/**
	 * Returns the template directory
	 *
	 * @return String
	 * @author Dan Cox
	 */
	public function getDirectory()
	{
		return $this->directory;
	}
	
	/**
	 * Adds an engine to the array
	 *
	 * @return Template
	 * @author Dan Cox
	 */
	public function addEngine($engine)
	{	
		$this->engines[] = $engine;
		return $this;
	}


} // END class Template