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

abstract class BaseCallableInjectionTarget extends BaseInjectionTarget
{
	/**
	 * @param object[] $servicesToInject
	 * @return mixed
	 */
	public function invoke(array $servicesToInject)
	{
		return call_user_func_array($this->getCallable(), $servicesToInject);
	}

	/**
	 * Should return the value, that can be passed to call_user_func_array().
	 *
	 * @return mixed
	 */
	abstract protected function getCallable();
}
