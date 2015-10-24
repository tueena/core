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

class InjectionTargetFunction extends BaseCallableInjectionTarget
{
	/**
	 * @var string
	 */
	private $functionName;

	/**
	 * @param string $functionName
	 */
	public function __construct($functionName)
	{
		$this->functionName = $functionName;
		$this->initialize();
	}

	/**
	 * @return string
	 */
	public function getInjectionTargetTypeName()
	{
		return 'function';
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	protected function getReflectionParameters()
	{
		return (new \ReflectionFunction($this->functionName))->getParameters();
	}

	/**
	 * @return string
	 */
	protected function getCallable()
	{
		return $this->functionName;
	}
}
