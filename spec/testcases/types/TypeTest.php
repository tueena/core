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

namespace tueena\spec\core\types\Type;

use tueena\core\types\Type;
use tueena\core\types\ConcreteClassType;
use tueena\core\types\AbstractClassType;
use tueena\core\types\InterfaceType;

class TypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_static_of_method_returns_an_instance_of_ConcreteClassType_if_the_passed_in_string_is_the_name_of_a_not_abstract_class()
	{
		// when
		$returnValue = Type::fromName('tueena\\spec\\core\\stubs\\MyService');

		// then
		$this->assertTrue($returnValue instanceof ConcreteClassType);
	}

	/**
	 * @test
	 */
	public function The_static_of_method_returns_an_instance_of_AbstractClassType_if_the_passed_in_string_is_the_name_of_an_abstract_class()
	{
		// when
		$returnValue = Type::fromName('tueena\\spec\\core\\stubs\\MyAbstractService');

		// then
		$this->assertTrue($returnValue instanceof AbstractClassType);
	}

	/**
	 * @test
	 */
	public function The_static_of_method_returns_an_instance_of_InterfaceType_if_the_passed_in_string_is_the_name_of_an_interface()
	{
		// when
		$returnValue = Type::fromName('tueena\\spec\\core\\stubs\\IMyService');

		// then
		$this->assertTrue($returnValue instanceof InterfaceType);
	}

	/**
	 * @test
	 */
	public function The_static_of_method_throws_an_exception_if_the_passed_in_string_is_not_the_name_of_a_class_or_interface()
	{
		// when, then
		try {
			$line = __LINE__ + 1;
			Type::fromName('tueena\\spec\\core\\NotExistingClass');
		} catch (\Exception $Exception) {
			$expectedMessage = sprintf(
				'Parameter passed to tueena\core\types\Type::fromName() must be the name of a class or interface. Class or interface tueena\spec\core\NotExistingClass could not be found. Called in %s on line %d.',
				__FILE__,
				$line
			);
			$this->assertEquals($expectedMessage, $Exception->getMessage());
			return;
		}
		$this->fail('Exception expected.');
	}

	/**
	 * @test
	 */
	public function The_getName_method_returns_the_name_of_the_class_or_interface()
	{
		// given
		$Target = Type::fromName('tueena\\spec\\core\\stubs\\MyService');

		// when
		$returnValue = $Target->getName();

		// then
		$this->assertEquals('tueena\\spec\\core\\stubs\\MyService', $returnValue);
	}

	/**
	 * @test
	 */
	public function The_isInstanceOf_method_returns_true_if_the_type_implements_the_other()
	{
		// given
		$Target = Type::fromName('tueena\\spec\\core\\stubs\\MyService');
		$OtherType = Type::fromName('tueena\\spec\\core\\stubs\\IMyService');

		// when, then
		$this->assertTrue($Target->isInstanceOf($OtherType));
	}

	/**
	 * @test
	 */
	public function The_isInstanceOf_method_returns_true_if_the_type_is_the_same_as_the_other()
	{
		// given
		$Target = Type::fromName('tueena\\spec\\core\\stubs\\MyService');
		$OtherType = Type::fromName('tueena\\spec\\core\\stubs\\MyService');

		// when, then
		$this->assertTrue($Target->isInstanceOf($OtherType));
	}

	/**
	 * @test
	 */
	public function The_isInstanceOf_method_returns_false_if_the_type_does_not_implement_the_other()
	{
		// given
		$Target = Type::fromName('tueena\\spec\\core\\stubs\\MyService');
		$OtherType = Type::fromName(__CLASS__);

		// when, then
		$this->assertFalse($Target->isInstanceOf($OtherType));
	}
}

