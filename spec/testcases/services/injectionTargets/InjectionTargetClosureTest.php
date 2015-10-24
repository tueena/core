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

use tueena\core\services\injectionTargets\InjectionTargetClosure;
use tueena\spec\core\stubs\A;
use tueena\spec\core\stubs\B;

class InjectionTargetClosureTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function getRequiredTypes_returns_the_required_types_of_the_injection_target()
	{
		// given
		$Closure = function (A $X, B $Y) {};
		$Target = new InjectionTargetClosure($Closure);

		// when
		$requiredTypes = $Target->getRequiredTypes();

		// then
		$this->assertEquals(2, count($requiredTypes));
		$this->assertTrue(array_key_exists('X', $requiredTypes));
		$this->assertEquals('tueena\\spec\\core\\stubs\\A', $requiredTypes['X']->getName());
		$this->assertTrue(array_key_exists('Y', $requiredTypes));
		$this->assertEquals('tueena\\spec\\core\\stubs\\B', $requiredTypes['Y']->getName());
	}

	/**
	 * @test
	 */
	public function The_invoke_method_calls_the_injection_target_with_the_given_parameters_and_returns_the_result()
	{
		// given
		$A = new A;
		$B = new B;
		$args = [];
		$Closure = function (A $X, B $Y) use (&$args) { $args = func_get_args(); return 42; };
		$Target = new InjectionTargetClosure($Closure);

		// when
		$result = $Target->invoke([$A, $B]);

		// then
		$this->assertEquals(2, count($args));
		$this->assertSame($A, $args[0]);
		$this->assertSame($B, $args[1]);
		$this->assertEquals(42, $result);
	}

	/**
	 * @test
	 */
	public function The_injection_target_object_returns_information_about_where_is_has_been_build()
	{
		// given
		$Closure = function (A $X, B $Y) {};
		$Target = new InjectionTargetClosure($Closure);
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
	public function getInjectionTargetTypeName_returns_the_string_closure()
	{
		// given
		$Closure = function (A $X, B $Y) {};
		$Target = new InjectionTargetClosure($Closure);

		// when
		$injectionTargetTypeName = $Target->getInjectionTargetTypeName();

		// then
		$this->assertEquals('closure', $injectionTargetTypeName);
	}
}
