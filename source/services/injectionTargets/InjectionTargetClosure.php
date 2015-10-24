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

class InjectionTargetClosure extends BaseCallableInjectionTarget
{
	/**
	 * @var \Closure
	 */
	private $Closure;

	/**
	 * @param \Closure $Closure
	 */
	public function __construct(\Closure $Closure)
	{
		$this->Closure = $Closure;
		$this->initialize();
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'closure';
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		return (new \ReflectionFunction($this->Closure))->getParameters();
	}

	/**
	 * @return \Closure
	 */
	protected function getCallable()
	{
		return $this->Closure;
	}
}
