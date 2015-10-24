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

class InjectionTargetConstructor extends BaseInjectionTarget
{
	/**
	 * @var ConcreteClassType
	 */
	private $ClassType;

	/**
	 * @param ConcreteClassType $ClassType
	 */
	public function __construct(ConcreteClassType $ClassType)
	{
		$this->ClassType = $ClassType;
		$this->initialize();
	}

	/**
	 * @param object[] $servicesToInject
	 * @return object
	 */
	public function invoke(array $servicesToInject)
	{
		$className = $this->ClassType->getName();
		if (!$this->hasConstructor())
			return new $className;

		$reflectionClass = new \ReflectionClass($className);
		return $reflectionClass->newInstanceArgs($servicesToInject);
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'constructor';
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		$className = $this->ClassType->getName();
		if (!$this->hasConstructor())
				return [];
		return (new \ReflectionMethod($className, '__construct'))->getParameters();
	}

	/**
	 * @return bool
	 */
	private function hasConstructor()
	{
		return method_exists($this->ClassType->getName(), '__construct');
	}
}
