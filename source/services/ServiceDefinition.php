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

namespace tueena\core\services;

use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\services\serviceDefinitionParameters\ImplementingType;
use tueena\core\services\serviceDefinitionParameters\FactoryFunction;
use tueena\core\services\serviceDefinitionParameters\InitFunction;
use tueena\core\types\Type;

class ServiceDefinition
{
	/**
	 * @var string
	 */
	private $definingFilePath;

	/**
	 * @var int
	 */
	private $definingLineNumber;

	/**
	 * @var Type
	 */
	private $IdentifyingType;

	/**
	 * @var tueena\core\types\ConcreteClassType
	 */
	private $ImplementingType;

	/**
	 * @var \Closure
	 */
	private $FactoryFunction;

	/**
	 * @var \Closure
	 */
	private $InitFunction;

	/**
	 * @param string $definingFilePath
	 * @param int $definingLineNumber
	 * @param IdentifyingType $IdentifyingType
	 * @param ImplementingType $ImplementingType
	 * @param FactoryFunction $FactoryFunction
	 * @param InitFunction $InitFunction
	 */
	public function __construct($definingFilePath, $definingLineNumber, IdentifyingType $IdentifyingType, ImplementingType $ImplementingType = null, FactoryFunction $FactoryFunction = null, InitFunction $InitFunction = null)
	{
		$this->definingFilePath = $definingFilePath;
		$this->definingLineNumber = $definingLineNumber;

		$this->IdentifyingType = $IdentifyingType->getType();
		if ($ImplementingType === null) {
			$this->ImplementingType = null;
		} else {
			if ($ImplementingType->isSameAsIdentifyingType())
				$this->ImplementingType = $this->IdentifyingType;
			else
				$this->ImplementingType = $ImplementingType->getType();
		}
		$this->FactoryFunction = $FactoryFunction !== null ? $FactoryFunction->getFactoryFunction() : null;
		$this->InitFunction = $InitFunction !== null ? $InitFunction->getInitFunction() : null;
	}

	/**
	 * @return string
	 */
	public function getDefiningFilePath()
	{
		return $this->definingFilePath;
	}

	/**
	 * @return int
	 */
	public function getDefiningLineNumber()
	{
		return $this->definingLineNumber;
	}

	/**
	 * @return Type
	 */
	public function getIdentifyingType()
	{
		return $this->IdentifyingType;
	}

	/**
	 * @return bool
	 */
	public function hasImplementingType()
	{
		return $this->ImplementingType !== null;
	}

	/**
	 * @return tueena\core\types\ConcreteClassType
	 */
	public function getImplementingType()
	{
		return $this->ImplementingType;
	}

	/**
	 * @return bool
	 */
	public function hasFactoryFunction()
	{
		return $this->FactoryFunction !== null;
	}

	/**
	 * @return \Closure
	 */
	public function getFactoryFunction()
	{
		return $this->FactoryFunction;
	}

	/**
	 * @return bool
	 */
	public function hasInitFunction()
	{
		return $this->InitFunction !== null;
	}

	/**
	 * @return \Closure
	 */
	public function getInitFunction()
	{
		return $this->InitFunction;
	}
}
