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

use tueena\core\services\injectionTargets\exceptions\InvalidInjectionTarget;
use tueena\core\services\injectionTargets\InjectionTargetClosure;
use tueena\core\services\InvalidServiceDefinition;
use tueena\core\types\Type;

class ServiceDefinitionsValidator
{
	/**
	 * @param ServiceDefinitions $ServiceDefinitions
	 */
	public function validate(ServiceDefinitions $ServiceDefinitions)
	{
		foreach ($ServiceDefinitions->getAll() as $ServiceDefinition)
			self::valiadateServiceDefinition($ServiceDefinition, $ServiceDefinitions);
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 */
	private static function valiadateServiceDefinition(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions)
	{
		self::validateImplementingTypeIsInstanceOfIdentifyingType($ServiceDefinition);
		if ($ServiceDefinition->hasFactoryFunction())
			self::validateFunction($ServiceDefinition, $ServiceDefinitions, 'factory', $ServiceDefinition->getFactoryFunction());
		if ($ServiceDefinition->hasInitFunction())
			self::validateFunction($ServiceDefinition, $ServiceDefinitions, 'init', $ServiceDefinition->getInitFunction());
		self::validateReferences($ServiceDefinition, $ServiceDefinitions);
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 */
	private static function validateImplementingTypeIsInstanceOfIdentifyingType(ServiceDefinition $ServiceDefinition)
	{
		$IdentifyingType = $ServiceDefinition->getIdentifyingType();
		$ImplementingType = $ServiceDefinition->getImplementingType();

		// The only case, the implementing is NULL is, when a factory function is
		// defined. We cannot validate, if the the factory function retuns a valid
		// type without building the service, so we validate that in the
		// ServiceFactory when the service is build.
		if (is_null($ImplementingType))
			return;

		if ($ImplementingType === $IdentifyingType)
			return;

		if (!$ImplementingType->isInstanceOf($IdentifyingType)) {
			$message = $ImplementingType->getName() . ' does not implement ' . $IdentifyingType->getName() . '. ';
			$message .= 'The implementing type of a service must be an instance of the identifying type.';
			throw new InvalidServiceDefinition($ServiceDefinition, $message);
		}
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 * @param string $functionType
	 * @param \Closure $function
	 */
	private static function validateFunction(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions, $functionType, \Closure $function)
	{
		try {
			$InjectionTraget = new InjectionTargetClosure($function);
		} catch (InvalidInjectionTarget $InvalidInjectionTargetException) {
			$message = "Invalid $functionType function: ";
			$message .= $InvalidInjectionTargetException->getSpecificMessage();
			throw new InvalidServiceDefinition($ServiceDefinition, $message);
		}
		$requiredTypes = $InjectionTraget->getRequiredTypes();

		foreach ($requiredTypes as $parameterName => $requiredType) {
			self::ensureParameterIsAService($ServiceDefinition, $ServiceDefinitions, $functionType, $requiredType, $parameterName);
			self::ensureFactoryFunctionDoesNotRequireTheServiceItself($ServiceDefinition, $functionType, $requiredType, $parameterName);
		}
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 * @param string $functionType
	 * @param Type $RequiredType
	 * @param string $parameterName
	 */
	private static function ensureParameterIsAService(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions, $functionType, Type $RequiredType, $parameterName)
	{
		if ($ServiceDefinitions->has($RequiredType))
			return;

		$functionTypeString = ($functionType === 'factory') ? 'a factory function' : 'an init function';
		$message = "Invalid parameter \$$parameterName: ";
		$message .= $RequiredType->getName() . ' is not defined as a service (means: has not been added to the ServiceDefinitions instance with the add() method). ';
		$message .= "Each parameter of $functionTypeString must have a defined service as type hint.";
		throw new InvalidServiceDefinition($ServiceDefinition, $message);
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param string $functionType
	 * @param Type $RequiredType
	 * @param string $parameterName
	 */
	private static function ensureFactoryFunctionDoesNotRequireTheServiceItself(ServiceDefinition $ServiceDefinition, $functionType, Type $RequiredType, $parameterName)
	{
		if ($functionType === 'init')
			return;

		if ($RequiredType == $ServiceDefinition->getIdentifyingType()) {
			$message = 'The type hint of the parameter $' . $parameterName . ' is ' . $RequiredType->getName() . '. ';
			$message .= 'A factory function cannot be injected with the service it is about to build.';
			throw new InvalidServiceDefinition($ServiceDefinition, $message);
		}
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 */
	public static function validateReferences(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions)
	{
		self::throwOnCircularReferenceOfRequiredServices($ServiceDefinition, $ServiceDefinitions, $ServiceDefinition);
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 * @param ServiceDefinition $InitialServiceDefinition
	 * @param ServiceDefinition[] $alreadyCheckedRequirements
	 * @param array[] $trace
	 */
	private static function throwOnCircularReferenceOfRequiredServices(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions, ServiceDefinition $InitialServiceDefinition, array &$alreadyCheckedRequirements = [], array $trace = [])
	{
		$traceForFactoryMethodCheck = $trace;
		$traceForFactoryMethodCheck[] = [$ServiceDefinition, 'factory'];
		self::throwOnCircularReferenceOfRequiresServicesHelper($ServiceDefinition, $ServiceDefinitions, $InitialServiceDefinition, $alreadyCheckedRequirements, $traceForFactoryMethodCheck, $ServiceDefinition->getFactoryFunction());

		$traceForInitMethodCheck = $trace;
		$traceForInitMethodCheck[] = [$ServiceDefinition, 'init'];
		self::throwOnCircularReferenceOfRequiresServicesHelper($ServiceDefinition, $ServiceDefinitions, $InitialServiceDefinition, $alreadyCheckedRequirements, $traceForInitMethodCheck, $ServiceDefinition->getInitFunction());
	}

	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param ServiceDefinitions $ServiceDefinitions
	 * @param ServiceDefinition $InitialServiceDefinition
	 * @param ServiceDefinition[] $alreadyCheckedRequirements
	 * @param array[] $trace
	 * @param \Closure $Closure
	 */
	private static function throwOnCircularReferenceOfRequiresServicesHelper(ServiceDefinition $ServiceDefinition, ServiceDefinitions $ServiceDefinitions, ServiceDefinition $InitialServiceDefinition, array &$alreadyCheckedRequirements, array $trace, \Closure $Closure = null)
	{
		if ($Closure === null)
			return;

		$InjectionTarget = new InjectionTargetClosure($Closure);
		foreach ($InjectionTarget->getRequiredTypes() as $requiredType) {
			$ServiceDefinition = $ServiceDefinitions->get($requiredType);
			if (in_array($ServiceDefinition, $alreadyCheckedRequirements))
				continue;

			// The second part of the condition ensures, that no exception is thrown
			// if an init function of a service requires the service itself. This
			// is absolutely valid.
			if ($ServiceDefinition === $InitialServiceDefinition && $trace[0][1] !== 'init')
				self::throwCircularReferenceException($InitialServiceDefinition, $trace);

			$alreadyCheckedRequirements[] = $ServiceDefinition;
			self::throwOnCircularReferenceOfRequiredServices($ServiceDefinition, $ServiceDefinitions, $InitialServiceDefinition, $alreadyCheckedRequirements, $trace);
		}
	}

	/**
	 * @param ServiceDefinition $InitialServiceDefinition
	 * @param array[] $trace
	 * @throws InvalidServiceDefinition
	 */
	private static function throwCircularReferenceException(ServiceDefinition $InitialServiceDefinition, array $trace)
	{
		$trace[] = [$InitialServiceDefinition, ''];
		$message = '';
		while (count($trace) > 1) {
			$injectedService = array_shift($trace);
			$requiredService = $trace[0];
			if ($message !== '')
				$message .= ', ';
			$message .= $injectedService[0]->getIdentifyingType()->getName() . ' requires ' . $requiredService[0]->getIdentifyingType()->getName() . ' in it\'s ' . $injectedService[1] . ' function (defined in ' . $injectedService[0]->getDefiningFilePath() . ' on line ' . $injectedService[0]->getDefiningLineNumber() . ')';
		}
		$message = 'Circular reference detected: ' . $message . '.';
		throw new InvalidServiceDefinition($InitialServiceDefinition, $message);
	}
}