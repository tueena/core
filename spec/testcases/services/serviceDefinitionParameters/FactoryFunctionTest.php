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

use tueena\core\services\serviceDefinitionParameters\FactoryFunction;

class FactoryFunctionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function The_static_is_method_creates_and_returns_a_new_instance()
	{
		// given
		$function = function () {};

		// when
		$ReturnValue = FactoryFunction::is($function);

		// then
		$this->assertInstanceOf('tueena\\core\\Services\\ServiceDefinitionParameters\\FactoryFunction', $ReturnValue);
		$this->assertSame($function, $ReturnValue->getFactoryFunction());
	}
}
