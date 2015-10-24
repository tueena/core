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

use tueena\core\types\ConcreteClassType;

class InjectionTargetStaticMethod extends BaseCallableInjectionTarget
{
	/**
	 * @var ConcreteClassType
	 */
	private $classType;

	/**
	 * @var string
	 */
	private $methodName;

	/**
	 * @param ConcreteClassType $classType
	 * @param string $methodName
	 */
	public function __construct(ConcreteClassType $classType, $methodName)
	{
		$this->classType = $classType;
		$this->methodName = $methodName;
		$this->initialize();
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'static method';
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		return (new \ReflectionMethod($this->classType->getName(), $this->methodName))->getParameters();
	}

	/**
	 * @return string[]
	 */
	protected function getCallable()
	{
		return [$this->classType->getName(), $this->methodName];
	}
}
