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

namespace tueena\spec\core\services\injectionTargets;

use tueena\core\services\injectionTargets\InjectionTargetConstructor;
use tueena\core\types\Type;
use tueena\spec\core\stubs\D;

class InjectionTargetConstructorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function getRequiredTypes_returns_the_required_types_of_the_injection_target()
	{
		// given
		$Target = new InjectionTargetConstructor(Type::fromName('tueena\\spec\\core\\stubs\\E'));

		// when
		$requiredTypes = $Target->getRequiredTypes();

		// then
		$this->assertEquals(1, count($requiredTypes));
		$this->assertTrue(array_key_exists('D', $requiredTypes));
		$this->assertEquals('tueena\\spec\\core\\stubs\\D', $requiredTypes['D']->getName());
	}

	/**
	 * @test
	 */
	public function The_invoke_method_calls_the_constructor_with_the_given_parameters_and_returns_the_new_object()
	{
		// given
		$D = new D;
		$Target = new InjectionTargetConstructor(Type::fromName('tueena\\spec\\core\\stubs\\E'));

		// when
		$result = $Target->invoke([$D]);

		// then
		$this->assertInstanceOf('tueena\\spec\\core\\stubs\\E', $result);
		$this->assertSame($D, $result->D);
	}

	/**
	 * @test
	 */
	public function The_injection_target_object_returns_information_about_where_is_has_been_build()
	{
		// given
		$Target = new InjectionTargetConstructor(Type::fromName('tueena\\spec\\core\\stubs\\E'));
		$expectedLineNumber = __LINE__ - 1;

		// when
		$filePath = $Target->getFilePath();
		$lineNumber = $Target->getLineNumber();

		// then
		$this->assertEquals(__FILE__, $filePath);
		$this->assertEquals($expectedLineNumber, $lineNumber);
	}

	/**
	 * @test
	 */
	public function getInjectionTargetTypeName_returns_the_string_constructor()
	{
		// given
		$Target = new InjectionTargetConstructor(Type::fromName('tueena\\spec\\core\\stubs\\E'));

		// when
		$injectionTargetTypeName = $Target->getInjectionTargetTypeName();

		// then
		$this->assertEquals('constructor', $injectionTargetTypeName);
	}
}
