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

namespace tueena\spec\core\stubs;

class A
{
	private $InjectedObject;
	private static $StaticallyInjectedObject;

	public function myMethod(IMyService $InjectedObject)
	{
		$this->InjectedObject = $InjectedObject;
	}

	public function getInjectedObject()
	{
		return $this->InjectedObject;
	}

	public static function myStaticMethod(IMyService $InjectedObject)
	{
		self::$StaticallyInjectedObject = $InjectedObject;
	}

	public static function getStaticallyInjectedObject()
	{
		return self::$StaticallyInjectedObject;
	}
}