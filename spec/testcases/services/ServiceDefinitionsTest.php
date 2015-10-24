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

use tueena\core\services\ServiceDefinitions;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\serviceDefinitionParameters\InitFunction;
use tueena\core\services\serviceDefinitionParameters\FactoryFunction;
use tueena\core\services\InvalidServiceDefinition;
use tueena\core\types\Type;

class ServiceDefinitionsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function A_service_definition_can_be_added_with_an_identifying_and_an_implementing_type()
	{
		// given
		$IdentifyingType = Type::fromName('tueena\\spec\\core\\stubs\\IMyService');
		$ImplementingType = Type::fromName('tueena\\spec\\core\\stubs\\MyService');
		$Target = new ServiceDefinitions;

		// when
		$Target->add(
			IdentifyingType::is($IdentifyingType),
			ImplementingType::is($ImplementingType)
		);
		$line = __LINE__ - 1;

		// then
		$ServiceDefinition = $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'));
		$this->assertEquals(__FILE__, $ServiceDefinition->getDefiningFilePath());
		$this->assertEquals($line, $ServiceDefinition->getDefiningLineNumber());
		$this->assertSame($IdentifyingType, $ServiceDefinition->getIdentifyingType());
		$this->assertSame($ImplementingType, $ServiceDefinition->getImplementingType());
		$this->assertFalse($ServiceDefinition->hasFactoryFunction());
		$this->assertFalse($ServiceDefinition->hasInitFunction());
	}

	/**
	 * @test
	 */
	public function A_service_definition_can_be_added_with_a_factory_function()
	{
		// given
		$FactoryFunction = function () {};
		$Target = new ServiceDefinitions;

		// when
		$Target->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			FactoryFunction::is($FactoryFunction)
		);

		// then
		$ServiceDefinition = $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'));
		$this->assertTrue($ServiceDefinition->hasFactoryFunction());
		$this->assertFalse($ServiceDefinition->hasImplementingType());
		$this->assertSame($FactoryFunction, $ServiceDefinition->getFactoryFunction());
	}

	/**
	 * @test
	 */
	public function A_service_definition_can_be_added_with_an_init_function_as_third_parameter()
	{
		// given
		$InitFunction = function () {};
		$Target = new ServiceDefinitions;

		// when
		$Target->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService')),
			InitFunction::is($InitFunction)
		);

		// then
		$ServiceDefinition = $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'));
		$this->assertTrue($ServiceDefinition->hasInitFunction());
		$this->assertSame($InitFunction, $ServiceDefinition->getInitFunction());
	}

	/**
	 * @test
	 */
	public function A_service_definition_can_be_added_with_a_factory_and_an_init_function()
	{
		// given
		$FactoryFunction = function () {};
		$InitFunction = function () {};
		$Target = new ServiceDefinitions;

		// when
		$Target->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			FactoryFunction::is($FactoryFunction),
			InitFunction::is($InitFunction)
		);

		// then
		$ServiceDefinition = $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'));
		$this->assertSame($FactoryFunction, $ServiceDefinition->getFactoryFunction());
		$this->assertSame($InitFunction, $ServiceDefinition->getInitFunction());
	}

	/**
	 * @test
	 */
	public function The_add_metod_reurns_the_ServiceDefinitions_instance()
	{
		// given
		$Target = new ServiceDefinitions;

		// when
		$returnValue = $Target->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
		);

		// then
		$this->assertSame($Target, $returnValue);
	}

	/**
	 * @test
	 */
	public function The_has_method_returns_true_if_a_service_is_defined_and_false_if_not()
	{
		// given
		$IdentifyingType = Type::fromName('tueena\\spec\\core\\stubs\\IMyService');
		$ImplementingType = Type::fromName('tueena\\spec\\core\\stubs\\MyService');
		$Target = new ServiceDefinitions;
		$Target->add(
			IdentifyingType::is($IdentifyingType),
			ImplementingType::is($ImplementingType)
		);

		// when, then
		$this->assertTrue($Target->has(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')));
		$this->assertFalse($Target->has(Type::fromName('tueena\\spec\\core\\stubs\\MyService')));
	}

	/**
	 * @test
	 */
	public function The_identifying_type_is_unique()
	{
		// given
		$Target = new ServiceDefinitions;
		$Target->add(
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
			ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
		);
		$line1 = __LINE__ - 1;

		// when, then
		try {
			$line2 = __LINE__ + 4;
			$Target->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
				ImplementingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
			);
		} catch (InvalidServiceDefinition $Exception) {
			$expectedExceptionMessage = 'Invalid definition of the service tueena\\spec\\core\\stubs\\IMyService in ' . __FILE__ . ' on line ' . $line2 . ': ';
			$expectedExceptionMessage .= 'A service of type tueena\\spec\\core\\stubs\\IMyService has already been defined in ' . __FILE__ . ' on line ' . $line1 . '. ';
			$expectedExceptionMessage .= 'There cannot be defined two services with the same identifying type.';
			$this->assertEquals($expectedExceptionMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_getAll_method_returns_all_service_definitions()
	{
		// given
		$Target = new ServiceDefinitions;
		$Target
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
				ImplementingType::isTheSame()
			)
			->add(
				IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService')),
				ImplementingType::isTheSame()
			);

		// when
		$result = $Target->getAll();

		// then
		$this->assertEquals(
			[
				'tueena\\spec\\core\\stubs\\IMyService' => $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\IMyService')),
				'tueena\\spec\\core\\stubs\\MyService' => $Target->get(Type::fromName('tueena\\spec\\core\\stubs\\MyService'))
			],
			$result
		);
	}
}