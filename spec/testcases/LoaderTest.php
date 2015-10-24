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

use tueena\core\Loader;

class LoaderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function A_directory_can_be_defined_where_all_classes_of_a_namespace_are_found()
	{
		// given
		$Loader = new Loader;
		$this->assertFalse(class_exists('tueena\\spec\\core\\something\\foo\\Bar'));

		// when
		$Loader->defineNamespaceDirectory('tueena\\spec\\core\\something', __DIR__ . '/../stubs/loader-test');

		// then
		$this->assertTrue(class_exists('tueena\\spec\\core\\something\\foo\\Bar'));
	}

	/**
	 * @test
	 */
	public function A_cloaure_can_be_defined_as_loader()
	{
		// given
		$results = [];
		$Loader = new Loader;

		// when
		$Loader->addLoader(function ($className) use (&$results) {
			$results[] = $className;
		});
		class_exists('tueena\\spec\\core\\notExistingClass');

		// then
		$this->assertEquals(['tueena\\spec\\core\\notExistingClass'], $results);
	}

	/**
	 * @test
	 */
	public function Loaders_are_called_in_the_order_of_definition()
	{
		// given
		$results = [];
		$Loader = new Loader;

		// when
		$Loader
			->addLoader(function ($className) use (&$results) {
				$results[] = 'First';
			})
			->addLoader(function ($className) use (&$results) {
				$results[] = 'Second';
			})
			->addLoader(function ($className) use (&$results) {
				$results[] = 'Third';
			});
		class_exists('tueena\\spec\\core\\notExistingClass');

		// then
		$this->assertEquals(['First', 'Second', 'Third'], $results);
	}
}