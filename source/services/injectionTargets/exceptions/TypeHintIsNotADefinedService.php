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

namespace tueena\core\services\injectionTargets\exceptions;

use tueena\core\services\injectionTargets\IInjectionTarget;
use tueena\core\types\Type;

class TypeHintIsNotADefinedService extends InvalidInjectionTargetParameter
{
	/**
	 * @var Type
	 */
	private $RequiredType;

	/**
	 * @param IInjectionTarget $InjectionTarget
	 * @param string $parameterName
	 * @param Type $RequiredType
	 */
	public function __construct(IInjectionTarget $InjectionTarget, $parameterName, Type $RequiredType)
	{
		$this->RequiredType = $RequiredType;
		$message = $RequiredType->getName() . ' is not defined as a service (means: has not been added to the ServiceDefinitions instance with the add() method). ';
		$message .= 'Each parameter of an injection target must have a defined service as type hint.';
		parent::__construct($InjectionTarget, $parameterName, $message);
	}

	/**
	 * @return Type
	 */
	public function getRequiredType()
	{
		return $this->RequiredType;
	}
}