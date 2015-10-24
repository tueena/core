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

class InvalidInjectionTargetParameter extends InvalidInjectionTarget
{
	/**
	 * @var string
	 */
	private $parameterName;

	/**
	 * @param IInjectionTarget $InjectionTarget
	 * @param string $parameterName
	 * @param string $specificMessage
	 */
	public function __construct(IInjectionTarget $InjectionTarget, $parameterName, $specificMessage)
	{
		$this->parameterName = $parameterName;
		$message = sprintf(
			'Invalid parameter $%s: %s',
			$this->getParameterName(),
			$specificMessage
		);
		parent::__construct($InjectionTarget, $message);
	}

	/**
	 * @return string
	 */
	public function getParameterName()
	{
		return $this->parameterName;
	}
}