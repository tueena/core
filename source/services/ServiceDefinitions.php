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
use tueena\core\services\serviceDefinitionParameters\IImplementingTypeOrFactoryFunction;
use tueena\core\services\serviceDefinitionParameters\InitFunction;
use tueena\core\types\Type;

class ServiceDefinitions
{
	/**
	 * @var ServiceDefinition[]
	 */
	private $serviceDefinitions = [];

	/**
	 * Defines a service. The service is identified by a type. This can be a class
	 * or an interface. It is implemented by a class, that is an instance of the
	 * identifying type. If both are the same, the second parameter can also be
	 * an instance of ImplementingTypeIsSameAsIdentifyingType. To define a
	 * service, an optional init or/and an optional factory function could be
	 * passed. The factory function is passed as the third parameter, the init
	 * function as fourthe or, if no init function is passed, as third parameter.
	 *
	 * @param IdentifyingType $IdentifyingType
	 * @param IImplementingTypeOrFactoryFunction $ImplementingTypeOrFactoryFunction
	 * @param InitFunction $InitFunction
	 * @return self
	 */
	public function add(IdentifyingType $IdentifyingType, IImplementingTypeOrFactoryFunction $ImplementingTypeOrFactoryFunction, InitFunction $InitFunction = null)
	{
		$definingFilePath = debug_backtrace()[0]['file'];
		$definingLineNumber = debug_backtrace()[0]['line'];

		$Type = $IdentifyingType->getType();
		$identifyingTypeName = $Type->getName();

		if ($this->has($Type)) {
			$AlreadyDefinedServiceDefinition = $this->get($Type);
			$InvalidServiceDefinition = new ServiceDefinition($definingFilePath, $definingLineNumber, $IdentifyingType);
			$message = 'A service of type ' . $identifyingTypeName . ' has already been defined in ' . $AlreadyDefinedServiceDefinition->getDefiningFilePath() . ' on line ' . $AlreadyDefinedServiceDefinition->getDefiningLineNumber() . '. ';
			$message .= 'There cannot be defined two services with the same identifying type.';
			throw new InvalidServiceDefinition($InvalidServiceDefinition, $message);
		}

		if ($ImplementingTypeOrFactoryFunction instanceOf ImplementingType) {
			$ImplementingType = $ImplementingTypeOrFactoryFunction;
			$FactoryFunction = null;
		} else {
			$ImplementingType = null;
			$FactoryFunction = $ImplementingTypeOrFactoryFunction;
		}

		$this->serviceDefinitions[$identifyingTypeName] = new ServiceDefinition($definingFilePath, $definingLineNumber, $IdentifyingType, $ImplementingType, $FactoryFunction, $InitFunction);
		return $this;
	}

	/**
	 * @param Type $IdentifyingType
	 * @return bool
	 */
	public function has(Type $IdentifyingType)
	{
		return isset($this->serviceDefinitions[$IdentifyingType->getName()]);
	}

	/**
	 * @param Type $IdentifyingType
	 * @return ServiceDefinition
	 */
	public function get(Type $IdentifyingType)
	{
		$identifyingTypeName = $IdentifyingType->getName();
		return isset($this->serviceDefinitions[$identifyingTypeName]) ? $this->serviceDefinitions[$identifyingTypeName] : null;
	}

	/**
	 * @return ServiceDefinition[]
	 */
	public function getAll()
	{
		return $this->serviceDefinitions;
	}
}