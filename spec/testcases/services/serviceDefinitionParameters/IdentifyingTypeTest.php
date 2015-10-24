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

namespace tueena\spec\core\services\serviceDefinitionParameters;

use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\types\Type;

class IdentifyingTypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_static_is_method_creates_and_returns_a_new_instance()
	{
		// when
		$ReturnValue = IdentifyingType::is(Type::fromName('tueena\\spec\\core\\stubs\\IMyService'));

		// then
		$this->assertInstanceOf('tueena\\core\\Services\\ServiceDefinitionParameters\\IdentifyingType', $ReturnValue);
	}

	/**
	 * @test
	 */
	public function The_getType_method_returns_the_type_passed_to_the_is_method()
	{
		// given
		$Type = Type::fromName('tueena\\spec\\core\\stubs\\IMyService');

		// when
		$ReturnValue = IdentifyingType::is($Type);

		// then
		$this->assertSame($Type, $ReturnValue->getType());
	}
}

