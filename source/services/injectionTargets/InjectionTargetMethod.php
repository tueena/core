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

class InjectionTargetMethod extends BaseCallableInjectionTarget
{
	/**
	 * @var object
	 */
	private $Object;

	/**
	 * @var string
	 */
	private $methodName;

	/**
	 * @param object $Object
	 * @param string $methodName
	 */
	public function __construct($Object, $methodName)
	{
		$this->Object = $Object;
		$this->methodName = $methodName;
		$this->initialize();
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'method';
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		return (new \ReflectionMethod($this->Object, $this->methodName))->getParameters();
	}

	/**
	 * @return array
	 */
	protected function getCallable()
	{
		return [$this->Object, $this->methodName];
	}
}
