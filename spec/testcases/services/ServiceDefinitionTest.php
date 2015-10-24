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

use tueena\core\services\ServiceDefinition;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\serviceDefinitionParameters\InitFunction;
use tueena\core\services\serviceDefinitionParameters\FactoryFunction;
use tueena\core\types\Type;

class ServiceDefinitionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_ServiceDefinition_makes_all_constructor_parameters_available_through_methods()
	{
		// given
		$file = '/my/path';
		$line = 42;
		$IdentifyingType = Type::fromName(__CLASS__);
		$ImplementingType = Type::fromName(__CLASS__);
		$FactoryFunction = function () {};
		$InitFunction = function () {};
		$Target = new ServiceDefinition(
			$file,
			$line,
			IdentifyingType::is($IdentifyingType),
			ImplementingType::is($ImplementingType),
			FactoryFunction::is($FactoryFunction),
			InitFunction::is($InitFunction)
		);

		// when, then
		$this->AssertEquals($file, $Target->getDefiningFilePath());
		$this->AssertEquals($line, $Target->getDefiningLineNumber());
		$this->AssertSame($IdentifyingType, $Target->getIdentifyingType());
		$this->AssertSame($ImplementingType, $Target->getImplementingType());
		$this->AssertSame($FactoryFunction, $Target->getFactoryFunction());
		$this->AssertSame($InitFunction, $Target->getInitFunction());
		$this->AssertTrue($Target->hasFactoryFunction());
		$this->AssertTrue($Target->hasInitFunction());
	}

	/**
	 * @test
	 */
	public function the_ServiceDefinition_resolves_an_ImplementingType_that_has_been_build_with_the_isTheSame_method()
	{
		// given
		$Target = new ServiceDefinition(
			'/my/path',
			42,
			IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\MyService')),
			ImplementingType::isTheSame()
		);

		// when
		$Result = $Target->getImplementingType();

		// then
		$this->assertEquals('tueena\\spec\\core\\stubs\\MyService', $Result->getName());
	}

	/**
	 * @test
	 */
	public function hasFactoryFunction_returns_false_if_no_factory_function_passed_in()
	{
		// given
		$IdentifyingType = IdentifyingType::is(Type::fromName(__CLASS__));
		$ImplementingType = ImplementingType::is(Type::fromName(__CLASS__));
		$InitFunction = InitFunction::is(function () {});

		// when
		$Target = new ServiceDefinition('', 0, $IdentifyingType, $ImplementingType, null, $InitFunction);

		// then
		$this->AssertFalse($Target->hasFactoryFunction());
		$this->AssertTrue($Target->hasInitFunction());
	}

	/**
	 * @test
	 */
	public function hasInitFunction_returns_false_if_no_init_function_passed_in()
	{
		// given
		$IdentifyingType = IdentifyingType::is(Type::fromName(__CLASS__));
		$ImplementingType = ImplementingType::is(Type::fromName(__CLASS__));
		$FactoryFunction = FactoryFunction::is(function () {});

		// when
		$Target = new ServiceDefinition('', 0, $IdentifyingType, $ImplementingType, $FactoryFunction, null);

		// then
		$this->AssertTrue($Target->hasFactoryFunction());
		$this->AssertFalse($Target->hasInitFunction());
	}
}