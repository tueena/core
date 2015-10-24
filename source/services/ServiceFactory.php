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

use tueena\core\services\ServiceDefinitionsValidator;
use tueena\core\services\injectionTargets\InjectionTargetClosure;
use tueena\core\services\injectionTargets\InjectionTargetConstructor;
use tueena\core\services\serviceDefinitionParameters\IdentifyingType;
use tueena\core\types\Type;

class ServiceFactory
{
	/**
	 * @var ServiceDefinitions
	 */
	private $ServiceDefinitions;

	/**
	 * Key is the identifying type name, value the service object.
	 *
	 * @var object[]
	 */
	private $builtServices = [];

	/**
	 * @param ServiceDefinitions $ServiceDefinitions
	 */
	public function __construct(ServiceDefinitions $ServiceDefinitions)
	{
		$Validator = new ServiceDefinitionsValidator;
		$Validator->validate($ServiceDefinitions);

		$this->ServiceDefinitions = $ServiceDefinitions;
	}

	/**
	 * Returns a service instance for the passed in identifying type name. If the
	 * instance does not exist, it creates one.
	 *
	 * @param Type $IdentifyingType
	 * @param Injector $Injector
	 * @return object
	 * @throws ServiceNotDefined
	 */
	public function getService(Type $IdentifyingType, Injector $Injector)
	{
		$identifyingTypeName = $IdentifyingType->getName();

		if (isset($this->builtServices[$identifyingTypeName]))
			return $this->builtServices[$identifyingTypeName];

		$ServiceDefinition = $this->ServiceDefinitions->get($IdentifyingType);

		if (is_null($ServiceDefinition)) {
			$callerFilePath = debug_backtrace()[0]['file'];
			$callerLineNumber = debug_backtrace()[0]['line'];
			throw new ServiceNotDefined($IdentifyingType->getName(), $callerFilePath, $callerLineNumber);
		}

		if ($ServiceDefinition->hasFactoryFunction()) {
			$InjectionTarget = new InjectionTargetClosure($ServiceDefinition->getFactoryFunction());
			$Service = $Injector->resolve($InjectionTarget);
			$this->ensureValueImplementsIdentifyingType($ServiceDefinition, $Service);
		} else {
			$InjectionTarget = new InjectionTargetConstructor($ServiceDefinition->getImplementingType());
			$Service = $Injector->resolve($InjectionTarget);
		}
		$this->builtServices[$identifyingTypeName] = $Service;
		if ($ServiceDefinition->hasInitFunction()) {
			$InjectionTarget = new InjectionTargetClosure($ServiceDefinition->getInitFunction());
			$Injector->resolve($InjectionTarget);
		}

		return $Service;
	}

	/**
	 * @param IdentifyingType $IdentifyingType
	 * @param object $ServiceInstance
	 * @return self
	 */
	public function addService(IdentifyingType $IdentifyingType, $ServiceInstance)
	{
		$this->builtServices[$IdentifyingType->getType()->getName()] = $ServiceInstance;
		return $this;
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param mixed $value
	 * @throws InvalidServiceDefinition
	 */
	private function ensureValueImplementsIdentifyingType(ServiceDefinition $ServiceDefinition, $value)
	{
		$identifyingTypeName = $ServiceDefinition->getIdentifyingType()->getName();
		if (is_object($value) && ($value instanceof $identifyingTypeName))
			return;

		$message = 'The factory function returned ' . self::getValueInfo($value, $identifyingTypeName) . '. ';
		$message .=	'The factory function of a service must return an object that implements the identifying type.';
		throw new InvalidServiceDefinition($ServiceDefinition, $message);
	}

	/**
	 * @param mixed $value
	 * @param string $identifyingTypeName
	 * @return string
	 */
	private static function getValueInfo($value, $identifyingTypeName)
	{
		if (is_null($value))
			return 'NULL';

		if (is_array($value))
			return 'an array';

		if (is_resource($value))
			return 'a resource';

		if (is_object($value))
			return 'an instance of the class ' . get_class($value) . ' that does not implement ' . $identifyingTypeName;

		$valueInfo = $value;
		if (is_string($value))
			$valueInfo = '"' . str_replace ('"', '\\"', $valueInfo);
		else if (is_bool($value))
			$valueInfo = $valueInfo ? 'TRUE' : 'FALSE';

		return '(' . self::getTypeInfo($value) . ') ' . $valueInfo;
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	private static function getTypeInfo($value)
	{
		$typeTranslationMap = [
			'boolean' => 'bool',
			'integer' => 'int',
			'double' => 'float',
		];
		$type = gettype($value);
		return isset($typeTranslationMap[$type]) ? $typeTranslationMap[$type] : $type;
	}
}