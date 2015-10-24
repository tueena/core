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
use tueena\core\services\ServiceDefinitions;
use tueena\core\services\ServiceDefinitionsValidator;
use tueena\core\types\Type;
use tueena\spec\core\stubs\A;
use tueena\spec\core\stubs\B;
use tueena\spec\core\stubs\C;
use tueena\spec\core\stubs\D;
use tueena\core\services\InvalidServiceDefinition;

class ServiceDefinitionsValidatorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function An_empty_ServiceDefinitions_is_valid()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		$Target->validate($ServiceDefinitions);
	}

	/**
	 * @test
	 */
	public function A_valid_ServiceDefinitions_does_not_return_anything()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\B')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\C')),
				FactoryFunction::is(function (A $A) {}),
				InitFunction::is(function (B $B) {})
			);
		$Target = new ServiceDefinitionsValidator;

		// when, then
		$Target->validate($ServiceDefinitions);
	}

	/**
	 * @test
	 */
	public function A_definition_is_invalid_if_the_implementing_type_is_not_an_instance_of_the_identifying_type()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::is(Type::fromName(__CLASS__))
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= __CLASS__ . ' does not implement tueena\\spec\\core\\stubs\\A. ';
			$expectedExceptionMessage .= 'The implementing type of a service must be an instance of the identifying type.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function InvalidInjectionTarget_exception_messages_are_part_of_the_InvalidServcieDefinition_messages_on_invalid_factory_functions()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			FactoryFunction::is(function ($Z) {})
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= 'Invalid factory function: Invalid parameter $Z: ';
			$expectedExceptionMessage .= 'The type hint is missing or not a class or interface name. ';
			$expectedExceptionMessage .= 'Each parameter of an injection target must have an existing class or interface as type hint.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function InvalidInjectionTarget_exception_messages_are_part_of_the_InvalidServcieDefinition_messages_on_invalid_init_functions()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::isTheSame(),
			InitFunction::is(function ($Z) {})
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= 'Invalid init function: Invalid parameter $Z: ';
			$expectedExceptionMessage .= 'The type hint is missing or not a class or interface name. ';
			$expectedExceptionMessage .= 'Each parameter of an injection target must have an existing class or interface as type hint.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function A_definition_is_invalid_if_a_factory_function_requires_itself()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			FactoryFunction::is(function (\tueena\spec\core\stubs\A $A) {})
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= 'The type hint of the parameter $A is tueena\\spec\\core\\stubs\\A. ';
			$expectedExceptionMessage .= 'A factory function cannot be injected with the service it is about to build.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function A_definition_is_invalid_if_a_factory_function_requires_a_class_that_is_not_defined_as_service()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			FactoryFunction::is(function (\tueena\spec\core\services\ServiceDefinitionsValidatorTest $NoService) {})
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= 'Invalid parameter $NoService: tueena\\spec\\core\\services\\ServiceDefinitionsValidatorTest is not defined as a service (means: has not been added to the ServiceDefinitions instance with the add() method). ';
			$expectedExceptionMessage .= 'Each parameter of a factory function must have a defined service as type hint.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function A_definition_is_invalid_if_an_init_function_requires_a_class_that_is_not_defined_as_service()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::isTheSame(),
			InitFunction::is(function (\tueena\spec\core\services\ServiceDefinitionsValidatorTest $NoService) {})
		);
		$line = __LINE__ - 1;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\A in $file on line $line: ";
			$expectedExceptionMessage .= 'Invalid parameter $NoService: tueena\\spec\\core\\services\\ServiceDefinitionsValidatorTest is not defined as a service (means: has not been added to the ServiceDefinitions instance with the add() method). ';
			$expectedExceptionMessage .= 'Each parameter of an init function must have a defined service as type hint.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function Circular_references_are_invalid()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
				FactoryFunction::is(function (B $B) {})
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\B')),
				FactoryFunction::is(function (C $C) {})
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\C')),
				FactoryFunction::is(function (D $D) {})
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\D')),
				ImplementingType::isTheSame(),
				InitFunction::is(function (B $B) {})
			);
		$line3 = __LINE__ - 1;
		$line2 = $line3 - 5;
		$line1 = $line2 - 4;
		$file = __FILE__;
		$Target = new ServiceDefinitionsValidator;

		// when, then
		try {
			$Target->validate($ServiceDefinitions);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = "Invalid definition of the service tueena\\spec\\core\\stubs\\B in $file on line $line1: ";
			$expectedExceptionMessage .= "Circular reference detected: tueena\\spec\\core\\stubs\\B requires tueena\\spec\\core\\stubs\\C in it's factory function (defined in $file on line $line1), tueena\\spec\\core\\stubs\\C requires tueena\\spec\\core\\stubs\\D in it's factory function (defined in $file on line $line2), tueena\\spec\\core\\stubs\\D requires tueena\\spec\\core\\stubs\\B in it's init function (defined in $file on line $line3).";
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function If_a_service_requires_itself_in_its_init_function_this_is_not_considered_as_circular_reference()
	{
		// given
		$ServiceDefinitions = new ServiceDefinitions;
		$ServiceDefinitions->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\A')),
			ImplementingType::isTheSame(),
			InitFunction::is(function (A $A) {})
		);
		$Target = new ServiceDefinitionsValidator;

		// when, then
		$Target->validate($ServiceDefinitions);
	}
}