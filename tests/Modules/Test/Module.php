<?php namespace Wasp\Test\Modules\Test;

use Wasp\Modules\ModuleInterface,
	Wasp\Modules\ModuleBuilder;

/**
 * Module Class
 *
 * @package Wasp
 * @subpackage Tests\Modules
 * @author Dan Cox
 */
class Module implements ModuleInterface
{

	/**
	 * Build module
	 *
	 * @param ModuleBuilder $builder
	 * @return void
	 * @author Dan Cox
	 */
	public function setup(ModuleBuilder $builder)
	{
		// Register the route file
		$builder->addRoutesFromFile(__DIR__ . '/routes.php');	

		$builder->registerEntityDirectories([__DIR__ . '/Entity/']);
		$builder->setTemplateDirectory(__DIR__);
		$builder->addCommands([
			'Wasp\Test\Modules\Test\TestCommand'
		]);
	}
	
} // END class Module implements ModuleInterface
