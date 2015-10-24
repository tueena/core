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

namespace tueena\core\services\serviceDefinitionParameters;

class InitFunction
{
	/**
	 *
	 * @var \Closure
	 */
	private $InitFunction;

	/**
	 * @param \Closure $InitFunction
	 */
	private function __construct(\Closure $InitFunction)
	{
		$this->InitFunction = $InitFunction;
	}

	/**
	 * Returns a new instance of the InitFunction class.
	 *
	 * @param \Closure $InitFunction
	 * @return InitFunction
	 */
	public static function is(\Closure $InitFunction)
	{
		return new InitFunction($InitFunction);
	}

	/**
	 * @return \Closure
	 */
	public function getInitFunction()
	{
		return $this->InitFunction;
	}
}