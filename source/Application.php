<?php

/**
 * tueena framework
 *
 * Copyright (c) Bastian Fenske <bastian.fenske@tueena.org>
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @package core
 * @author Bastian Fenske <bastian.fenske@tueena.org>
 * @copyright Copyright (c) Bastian Fenske <bastian.fenske@tueena.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://tueena.org/
 * @file
 */

namespace tueena\core;

use tueena\core\services\Injector;
use tueena\core\services\injectionTargets\InjectionTargetClosure;
use tueena\core\services\ServiceDefinitions;
use tueena\core\services\ServiceFactory;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\types\Type;

require_once __DIR__ . '/Loader.php';

class Application
{
	/**
	 * @var \Closure
	 */
	private $LoaderConfigurator;

	/**
	 * @var \Closure
	 */
	private $ServiceDefiner;

	/**
	 * @var \Closure
	 */
	private $MainFunction;

	/**
	 * @param \Closure $LoaderConfigurator
	 * @param \Closure $ServiceDefiner
	 * @param \Closure $MainFunction
	 */
	public function __construct(\Closure $LoaderConfigurator, \Closure $ServiceDefiner, \Closure $MainFunction)
	{
		$this->LoaderConfigurator = $LoaderConfigurator;
		$this->ServiceDefiner = $ServiceDefiner;
		$this->MainFunction = $MainFunction;
	}

	/**
	 * @return \Closure
	 */
	public function getLoaderConfigurator()
	{
		return $this->LoaderConfigurator;
	}

	/**
	 * @return \Closure
	 */
	public function getServiceDefiner()
	{
		return $this->ServiceDefiner;
	}

	/**
	 * @return \Closure
	 */
	public function getMainFunction()
	{
		return $this->MainFunction;
	}

	public function run()
	{
		// Call the Loader configurator.
		$Loader = new Loader;
		$LoaderConfigurator = $this->LoaderConfigurator;
		$LoaderConfigurator($Loader);

		// Build the service definitions collection.
		$ServiceDefinitions = new ServiceDefinitions;

		// Call the service definer.
		$ServiceDefiner = $this->ServiceDefiner;
		$ServiceDefiner($ServiceDefinitions);

		// Build the service factory and the injector.
		$ServiceFactory = new ServiceFactory($ServiceDefinitions);
		$Injector = new Injector($ServiceFactory);

		// Register the Injector and the Loader as services.
		$ServiceFactory
			->addService(IdentifyingType::is(Type::fromName('tueena\\core\\services\\IInjector')), $Injector)
			->addService(IdentifyingType::is(Type::fromName('tueena\\core\\ILoader')), $Loader);

		// Call the main function.
		$MainFunction = new InjectionTargetClosure($this->MainFunction);
		$Injector->resolve($MainFunction);
	}
}