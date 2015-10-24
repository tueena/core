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

class InjectionTargetInvokeMethod extends BaseCallableInjectionTarget
{
	/**
	 * @var object
	 */
	private $Object;

	/**
	 * @param callable $Object
	 */
	public function __construct(callable $Object)
	{
		$this->Object = $Object;
		$this->initialize();
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'invoke method';
	}

	/**
	 * @return object
	 */
	protected function getCallable()
	{
		return $this->Object;
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		return (new \ReflectionMethod($this->Object, '__invoke'))->getParameters();
	}
}
