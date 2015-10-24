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

namespace tueena\spec\core;

class applicationFactoryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function Returns_a_new_instance_of_Application()
	{
		// given
		$loaderConfigurator = function () {};
		$serviceDefiner = function () {};
		$mainFunction = function () {};
		$applicationFactory = include __DIR__ . '/../../source/applicationFactory.php';

		// when
		$Target = $applicationFactory($loaderConfigurator, $serviceDefiner, $mainFunction);

		// then
		$this->assertInstanceOf('\\tueena\\core\\Application', $Target);
		$this->AssertEquals($loaderConfigurator, $Target->getLoaderConfigurator());
		$this->AssertEquals($serviceDefiner, $Target->getServiceDefiner());
		$this->AssertEquals($mainFunction, $Target->getMainFunction());
	}
}