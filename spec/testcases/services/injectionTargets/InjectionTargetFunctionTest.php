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

require_once __DIR__ . '/../../../stubs/f.php';

use tueena\core\services\injectionTargets\InjectionTargetFunction;
use tueena\spec\core\stubs\B;
use tueena\spec\core\stubs\C;

class InjectionTargetFunctionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function getRequiredTypes_returns_the_required_types_of_the_injection_target()
	{
		// given
		$Target = new InjectionTargetFunction('tueena\\spec\\core\\stubs\\f');

		// when
		$requiredTypes = $Target->getRequiredTypes();

		// then
		$this->assertEquals(2, count($requiredTypes));
		$this->assertTrue(array_key_exists('B', $requiredTypes));
		$this->assertEquals('tueena\\spec\\core\\stubs\\B', $requiredTypes['B']->getName());
		$this->assertTrue(array_key_exists('C', $requiredTypes));
		$this->assertEquals('tueena\\spec\\core\\stubs\\C', $requiredTypes['C']->getName());
	}

	/**
	 * @test
	 */
	public function The_invoke_method_calls_the_injection_target_with_the_given_parameters_and_returns_the_result()
	{
		// given
		$B = new B;
		$C = new C;
		$Target = new InjectionTargetFunction('tueena\\spec\\core\\stubs\\f');

		// when
		$result = $Target->invoke([$B, $C]);

		// then
		$this->assertEquals(3, count($result));
		$this->assertSame($B, $result[0]);
		$this->assertSame($C, $result[1]);
		$this->assertEquals('foo', $result[2]);
	}

	/**
	 * @test
	 */
	public function The_injection_target_object_returns_information_about_where_is_has_been_build()
	{
		// given
		$Target = new InjectionTargetFunction('tueena\\spec\\core\\stubs\\f');
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
	public function getInjectionTargetTypeName_returns_the_string_function()
	{
		// given
		$Target = new InjectionTargetFunction('tueena\\spec\\core\\stubs\\f');

		// when
		$injectionTargetTypeName = $Target->getInjectionTargetTypeName();

		// then
		$this->assertEquals('function', $injectionTargetTypeName);
	}
}
