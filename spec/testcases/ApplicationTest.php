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

namespace tueena\spec\core;

use tueena\core\Application;
use tueena\core\ILoader;
use tueena\core\services\IInjector;
use tueena\core\types\Type;
use tueena\core\services\ServiceDefinitions;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\spec\core\stubs\IMyService;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_run_method_calls_the_loaderConfigurator_then_the_serviceDefiner_than_the_mainFunction()
	{
		// given
		$callStack = [];
		$loaderConfigurator = function () use (&$callStack) {
			$callStack[] = 'loaderConfigurator';
		};
		$serviceDefiner = function () use (&$callStack) {
			$callStack[] = 'serviceDefiner';
		};
		$mainFunction = function () use (&$callStack) {
			$callStack[] = 'mainFunction';
		};
		$Target = new Application($loaderConfigurator, $serviceDefiner, $mainFunction);

		// when
		$Target->run();

		// then
		$this->AssertEquals(['loaderConfigurator', 'serviceDefiner', 'mainFunction'], $callStack);
	}

	/**
	 * @test
	 */
	public function An_instance_of_Loader_is_passed_to_the_loaderConfigurator()
	{
		// given
		$args = [];
		$loaderConfigurator = function () use (&$args) {
			$args = func_get_args();
		};
		$serviceDefiner = function () {};
		$mainFunction = function () {};
		$Target = new Application($loaderConfigurator, $serviceDefiner, $mainFunction);

		// when
		$Target->run();

		// then
		$this->AssertEquals(1, count($args));
		$this->assertInstanceOf('\\tueena\\core\\Loader', $args[0]);
	}

	/**
	 * @test
	 */
	public function An_instance_of_ServiceDefinitions_is_passed_to_the_serviceDefiner()
	{
		// given
		$args = [];
		$loaderConfigurator = function () {};
		$serviceDefiner = function () use (&$args) {
			$args = func_get_args();
		};
		$mainFunction = function () {};
		$Target = new Application($loaderConfigurator, $serviceDefiner, $mainFunction);

		// when
		$Target->run();

		// then
		$this->AssertEquals(1, count($args));
		$this->assertInstanceOf('\\tueena\\core\\Services\\ServiceDefinitions', $args[0]);
	}

	/**
	 * @test
	 */
	public function The_main_method_is_resolved_by_the_Injector()
	{
		// given
		$loaderConfigurator = function () {};
		$serviceDefiner = function (ServiceDefinitions $ServiceDefinitions) {
			$ServiceDefinitions
				->add(
					IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
					ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
				);
		};

		$passedInServiceInstance = null;
		$mainFunction = function (IMyService $MyService) use (&$passedInServiceInstance) {
			$passedInServiceInstance = $MyService;
		};
		$Target = new Application($loaderConfigurator, $serviceDefiner, $mainFunction);

		// when
		$Target->run();

		// then
		$this->assertInstanceOf('\\tueena\\spec\\core\\stubs\\MyService', $passedInServiceInstance);
	}

	/**
	 * @test
	 */
	public function The_Injector_and_the_Loader_are_defined_as_built_in_services()
	{
		// given
		$LoaderInstancePassedToLoaderConfigurator = null;
		$loaderConfigurator = function (ILoader $Loader) use (&$LoaderInstancePassedToLoaderConfigurator) { $LoaderInstancePassedToLoaderConfigurator = $Loader; };
		$serviceDefiner = function (ServiceDefinitions $ServiceDefinitions) {};

		$passedInServiceInstances = [];
		$mainFunction = function (ILoader $Loader, IInjector $Injector) use (&$passedInServiceInstances) {
			$passedInServiceInstances = [$Loader, $Injector];
		};
		$Target = new Application($loaderConfigurator, $serviceDefiner, $mainFunction);

		// when
		$Target->run();

		// then
		$this->assertEquals(2, count($passedInServiceInstances));
		$this->assertSame($LoaderInstancePassedToLoaderConfigurator, $passedInServiceInstances[0]);
		$this->assertInstanceOf('\\tueena\\core\\services\\IInjector', $passedInServiceInstances[1]); // ensure, that not NULL has been passed in
	}
}