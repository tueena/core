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

use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\types\Type;

class ImplementingTypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_static_is_method_creates_and_returns_a_new_instance()
	{
		// given
		$Type = Type::fromName('tueena\\spec\\core\\stubs\\MyService');

		// when
		$ReturnValue = ImplementingType::is($Type);

		// then
		$this->assertInstanceOf('tueena\\core\\Services\\ServiceDefinitionParameters\\ImplementingType', $ReturnValue);
		$this->assertSame($Type, $ReturnValue->getType());
		$this->assertFalse($ReturnValue->isSameAsIdentifyingType());
	}

	/**
	 * @test
	 */
	public function The_static_isTheSame_method_creates_and_returns_a_new_instance()
	{
		// when
		$ReturnValue = ImplementingType::isTheSame();

		// then
		$this->assertInstanceOf('tueena\\core\\Services\\ServiceDefinitionParameters\\ImplementingType', $ReturnValue);
		$this->assertNull($ReturnValue->getType());
		$this->assertTrue($ReturnValue->isSameAsIdentifyingType());
	}
}

