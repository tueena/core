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

namespace tueena\spec\core\services;

use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\serviceDefinitionParameters\InitFunction;
use tueena\core\services\serviceDefinitionParameters\FactoryFunction;
use tueena\core\types\Type;
use tueena\core\services\Injector;
use tueena\core\services\InvalidServiceDefinition;
use tueena\core\services\ServiceDefinitions;
use tueena\core\services\ServiceFactory;
use tueena\core\services\ServiceNotDefined;
use tueena\spec\core\stubs\A;
use tueena\spec\core\stubs\B;
use tueena\spec\core\stubs\MyService;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @test
	 */
	public function The_constructor_validates_the_ServiceDefinitions()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::is(Type::fromName(__CLASS__)) // invalid!
		);

		// when, then
		try {
			new ServiceFactory($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			return; // expected behavior.
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_getService_method_throws_an_exception_if_a_service_is_not_defined()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$Target = new ServiceFactory($serviceDefinitions);
		$Injector = new Injector($Target);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);
		} catch (ServiceNotDefined $Exception) {
			$this->assertEquals('tueena\\spec\\core\\stubs\\IMyService', $Exception->getIdentifyingTypeName());
			$this->assertEquals(__FILE__, $Exception->getCallerFilePath());
			$this->assertEquals($line, $Exception->getCallerLineNumber());
			return; // expected result.
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_getService_method_builds_a_service()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
		);
		$Target = new ServiceFactory($serviceDefinitions);
		$Injector = new Injector($Target);

		// when
		$Service = $Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);

		// then
		$this->assertInstanceOf('tueena\\spec\\core\\stubs\\MyService', $Service);
	}

	/**
	 * @test
	 */
	public function The_getService_method_returns_always_the_same_instance_of_a_service()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
		);
		$Target = new ServiceFactory($serviceDefinitions);
		$Injector = new Injector($Target);

		// when
		$Service1 = $Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);
		$Service2 = $Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);

		// then
		$this->AssertSame($Service1, $Service2);
	}

	/**
	 * @test
	 */
	public function If_a_factory_function_is_defined_the_service_is_build_with_this_function()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			FactoryFunction::is(function () {
				$Service = new MyService;
				$Service->setFoo(42);
				return $Service;
			})
		);
		$Target = new ServiceFactory($serviceDefinitions);
		$Injector = new Injector($Target);

		// when, then
		$Service = $Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);
		$this->AssertEquals(42, $Service->getFoo());
	}

	/**
	 * @test
	 */
	public function The_factory_function_can_be_injected()
	{
		// given
		$counter = 0;
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\B')),
				FactoryFunction::is(function (A $A) use (&$counter) {
					$counter++;
					return new B;
				})
			);
		$Target = new ServiceFactory($ServiceDefinitions);
		$Injector = new Injector($Target);

		// when
		$Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\B'), $Injector);

		// then
		//  We just have to ensure, the factory function has been called. A must
		// have been injected.
		$this->AssertEquals(1, $counter);
	}

	/**
	 * @test
	 * @dataProvider getInvalidFactoryFunctionResults
	 */
	public function An_exception_is_thrown_if_the_factory_function_returns_not_an_instance_of_the_defined_implementing_type($factoryFunctionReturnValue, $expectedErrorMessage)
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			FactoryFunction::is(function () use ($factoryFunctionReturnValue) { return $factoryFunctionReturnValue; })
		);
		$line = __LINE__ - 1;
		$Target = new ServiceFactory($serviceDefinitions);
		$Injector = new Injector($Target);

		// when, then
		try {
			$Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'), $Injector);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = 'Invalid definition of the service tueena\\spec\\core\\stubs\\IMyService in ';
			$expectedExceptionMessage .= __FILE__ . ' on line ' . $line . ': ';
			$expectedExceptionMessage .= $expectedErrorMessage . ' ';
			$expectedExceptionMessage .= 'The factory function of a service must return an object that implements the identifying type.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	public static function getInvalidFactoryFunctionResults()
	{
		return [
				[null, 'The factory function returned NULL.'],
				[true, 'The factory function returned (bool) TRUE.'],
				[false, 'The factory function returned (bool) FALSE.'],
				[42, 'The factory function returned (int) 42.'],
				[-.1, 'The factory function returned (float) -0.1.'],
				[['foo' => 'bar'], 'The factory function returned an array.'],
				[fopen(__FILE__, 'r'), 'The factory function returned a resource.'],
				[new A, 'The factory function returned an instance of the class tueena\\spec\\core\\stubs\\A that does not implement tueena\\spec\\core\\stubs\\IMyService.'],
		];
	}

	/**
	 * @test
	 */
	public function If_an_init_function_is_defined_it_will_be_called_after_the_service_has_been_build()
	{
		// given
		$counter = 0;
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService')),
			ImplementingType::isTheSame(),
			InitFunction::is(function () use (&$counter) { $counter++; })
		);
		$Target = new ServiceFactory($ServiceDefinitions);
		$Injector = new Injector($Target);

		// when
		$Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\MyService'), $Injector);

		// then
		$this->AssertEquals(1, $counter);
	}

	/**
	 * @test
	 */
	public function The_init_function_can_be_injected()
	{
		// given
		$counter = 0;
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\B')),
				ImplementingType::isTheSame(),
				InitFunction::is(function (A $A, B $B) use (&$counter) { $counter++; })
			);
		$Target = new ServiceFactory($ServiceDefinitions);
		$Injector = new Injector($Target);

		// when
		$Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\B'), $Injector);

		// then
		//  We just have to ensure, the init function has been called. A and B must
		// have been injected.
		$this->AssertEquals(1, $counter);
	}

	/**
	 * @test
	 */
	public function A_servcie_instance_can_be_added()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$Target = new ServiceFactory($ServiceDefinitions);

		// when
		$A = new A;
		$Target->addService(IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')), $A);
		$Injector = new Injector($Target);

		// then
		$this->assertSame($A, $Target->getService(Type::fromName('tueena\\spec\\core\\stubs\\A'), $Injector));
	}
}