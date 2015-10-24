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

use tueena\core\services\injectionTargets\exceptions\TypeHintIsMissingOrNotAClassOrInterfaceName;
use tueena\core\services\injectionTargets\exceptions\TypeHintIsNotAnExistingClassOrInterface;
use tueena\core\services\injectionTargets\exceptions\ParameterIsOptional;
use tueena\core\types\Type;

abstract class BaseInjectionTarget implements IInjectionTarget
{
	/**
	 * @var Type[]
	 */
	private $requiredTypes;

	/**
	 * @var string
	 */
	private $filePath;

	/**
	 * @var int
	 */
	private $lineNumber;

	protected function initialize()
	{
		$this->filePath = debug_backtrace()[1]['file'];
		$this->lineNumber = debug_backtrace()[1]['line'];
		$this->loadRequiredTypes();
	}

	/**
	 * @return Type[]
	 */
	public function getRequiredTypes()
	{
		return $this->requiredTypes;
	}

	/**
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @return int
	 */
	public function getLineNumber()
	{
		return $this->lineNumber;
	}

	/**
	 * @return \ReflectionParameter[]
	 */
	abstract protected function getReflectionParameters();

	private function loadRequiredTypes()
	{
		$this->requiredTypes = [];
		foreach ($this->getReflectionParameters() as $ReflectionParameter)
			$this->requiredTypes[$ReflectionParameter->getName()] = $this->getRequiredType($ReflectionParameter);
	}

	/**
	 * @param \ReflectionParameter $ReflectionParameter
	 * @return Type
	 */
	private function getRequiredType(\ReflectionParameter $ReflectionParameter)
	{
		$parameterName = $ReflectionParameter->getName();

		try {
			$ReflectionClass = $ReflectionParameter->getClass();
		} catch (\Exception $Exception) {
			throw new TypeHintIsNotAnExistingClassOrInterface($this, $parameterName);
		}

		if ($ReflectionClass === null)
			throw new TypeHintIsMissingOrNotAClassOrInterfaceName($this, $parameterName);

		if ($ReflectionParameter->isOptional())
			throw new ParameterIsOptional($this, $parameterName);

		return Type::fromName($ReflectionClass->name);
	}
}
