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

namespace tueena\core\services\injectionTargets;

interface IInjectionTarget
{
	/**
	 * @return tueena\core\types\Type[]
	 */
	public function getRequiredTypes();

	/**
	 * @param object[] $servicesToInject
	 * @return mixed
	 */
	public function invoke(array $servicesToInject);

	/**
	 * Returns the path of the file, where the injection target has been build.
	 *
	 * @return string
	 */
	public function getFilePath();

	/**
	 * Returns the line number, where the injection target has been build.
	 *
	 * @return int
	 */
	public function getLineNumber();

	/**
	 * Returns the name of type of the injection target (for example "closure" or
	 * "static method") for the use in error messages.
	 *
	 * @return string
	 */
	public function getInjectionTargetTypeName();
}
