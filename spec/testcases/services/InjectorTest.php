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

use tueena\core\services\Injector;
use tueena\core\services\injectionTargets\exceptions\TypeHintIsMissingOrNotAClassOrInterfaceName;
use tueena\core\services\injectionTargets\exceptions\TypeHintIsNotADefinedService;
use tueena\core\services\injectionTargets\exceptions\TypeHintIsNotAnExistingClassOrInterface;
use tueena\core\services\injectionTargets\exceptions\ParameterIsOptional;
use tueena\core\services\injectionTargets\InjectionTargetClosure;
use tueena\core\services\ServiceDefinitions;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\ServiceFactory;
use tueena\core\types\Type;
use tueena\spec\core\stubs\A;
use tueena\spec\core\stubs\B;

class InjectorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function A_parameterless_injection_target_can_be_resolved()
	{
		// given
		$Target = new Injector(new ServiceFactory(new ServiceDefinitions));
		$callCounter = 0;
		$InjectionTarget = new InjectionTargetClosure(function () use (&$callCounter) { $callCounter++; });

		// when
		$Target->resolve($InjectionTarget);

		// then
		$this->AssertEquals(1, $callCounter);
	}

	/**
	 * @test
	 */
	public function The_resolve_method_injects_service_instances_into_the_injection_target()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\B')),
				ImplementingType::isTheSame()
			);
		$ServiceFactory = new ServiceFactory($serviceDefinitions);
		$Target = new Injector($ServiceFactory);
		$args = [];
		$Closure = function (A $A, B $B) use (&$args) { $args = func_get_args(); };

		// when
		$Target->resolve(new InjectionTargetClosure($Closure));

		// then
		$this->AssertEquals(2, count($args));
		$this->assertInstanceOf('tueena\\spec\\core\\stubs\\A', $args[0]);
		$this->assertInstanceOf('tueena\\spec\\core\\stubs\\B', $args[1]);
	}

	/**
	 * @test
	 */
	public function The_resolve_method_throws_an_exception_if_a_parameter_of_the_injection_target_has_no_type_hint()
	{
		// given
		$ServiceFactory = new ServiceFactory(new ServiceDefinitions);
		$Target = new Injector($ServiceFactory);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->resolve(new InjectionTargetClosure(function ($a) {}));
		} catch (TypeHintIsMissingOrNotAClassOrInterfaceName $Exception) {
			$file = __FILE__;
			$this->assertEquals('a', $Exception->getParameterName());
			$this->assertEquals($file, $Exception->getFilePath());
			$this->assertEquals($line, $Exception->getLineNumber());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_resolve_method_throws_an_exception_if_a_parameter_of_the_injection_target_has_array_as_type_hint()
	{
		// given
		$ServiceFactory = new ServiceFactory(new ServiceDefinitions);
		$Target = new Injector($ServiceFactory);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->resolve(new InjectionTargetClosure(function (array $a) {}));
		} catch (TypeHintIsMissingOrNotAClassOrInterfaceName $Exception) {
			$file = __FILE__;
			$this->assertEquals('a', $Exception->getParameterName());
			$this->assertEquals($file, $Exception->getFilePath());
			$this->assertEquals($line, $Exception->getLineNumber());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_resolve_method_throws_an_exception_if_a_parameter_of_the_injection_target_has_a_not_existing_class_as_type_hint()
	{
		// given
		$ServiceFactory = new ServiceFactory(new ServiceDefinitions);
		$Target = new Injector($ServiceFactory);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->resolve(new InjectionTargetClosure(function (\tueena\spec\core\NotExistingClass $A) {}));
		} catch (TypeHintIsNotAnExistingClassOrInterface $Exception) {
			$file = __FILE__;
			$this->assertEquals('A', $Exception->getParameterName());
			$this->assertEquals($file, $Exception->getFilePath());
			$this->assertEquals($line, $Exception->getLineNumber());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_resolve_method_throws_an_exception_if_a_parameter_of_the_injection_target_is_optional()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::isTheSame()
		);
		$ServiceFactory = new ServiceFactory($ServiceDefinitions);
		$Target = new Injector($ServiceFactory);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->resolve(new InjectionTargetClosure(function (\tueena\spec\core\stubs\A $A = null) {}));
		} catch (ParameterIsOptional $Exception) {
			$file = __FILE__;
			$this->assertEquals('A', $Exception->getParameterName());
			$this->assertEquals($file, $Exception->getFilePath());
			$this->assertEquals($line, $Exception->getLineNumber());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_resolve_method_throws_an_exception_if_a_parameter_of_the_injection_target_requires_a_class_that_is_not_defined_as_service()
	{
		// given
		$ServiceFactory = new ServiceFactory(new ServiceDefinitions);
		$Target = new Injector($ServiceFactory);

		// when, then
		try {
			$line = __LINE__ + 1;
			$Target->resolve(new InjectionTargetClosure(function (\tueena\spec\core\stubs\A $A) {}));
		} catch (TypeHintIsNotADefinedService $Exception) {
			$file = __FILE__;
			$this->assertEquals('A', $Exception->getParameterName());
			$this->assertEquals('tueena\spec\core\stubs\A', $Exception->getRequiredType()->getName());
			$this->assertEquals($file, $Exception->getFilePath());
			$this->assertEquals($line, $Exception->getLineNumber());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function If_a_service_is_not_defined_with_a_factory_function_the_constructor_of_that_service_gets_injected_with_the_required_services()
	{
		// given
		$serviceDefinitions = new ServiceDefinitions;
		$serviceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\D')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\E')),
				ImplementingType::isTheSame()
			);
		$ServiceFactory = new ServiceFactory($serviceDefinitions);
		$Target = new Injector($ServiceFactory);
		$PropertyDOfE = null;
		$Closure = function (\tueena\spec\core\stubs\E $E) use (&$PropertyDOfE) { $PropertyDOfE = $E->D; };

		// when
		$Target->resolve(new InjectionTargetClosure($Closure));

		// then
		$this->assertInstanceOf('tueena\\spec\\core\\stubs\\D', $PropertyDOfE);
	}
}