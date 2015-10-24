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

class InvalidInjectionTarget extends \Exception implements IInvalidInjectionTarget
{
	/**
	 * @var IInjectionTarget
	 */
	private $InjectionTarget;

	/**
	 * @var string
	 */
	private $specificMessage;

	/**
	 * @param IInjectionTarget $InjectionTarget
	 * @param string $specificMessage
	 */
	public function __construct(IInjectionTarget $InjectionTarget, $specificMessage)
	{
		$this->InjectionTarget = $InjectionTarget;
		$this->specificMessage = $specificMessage;
		$message = sprintf(
			'The signature of the %s that has been passed to the InjectionTarget constructor in %s on line %d is invalid: %s',
			$InjectionTarget->getInjectionTargetTypeName(),
			$InjectionTarget->getFilePath(),
			$InjectionTarget->getLineNumber(),
			$specificMessage
		);
		parent::__construct($message);
	}

	/**
	 * @return string
	 */
	public function getSpecificMessage()
	{
		return $this->specificMessage;
	}

	/**
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->InjectionTarget->getFilePath();
	}

	/**
	 * @return int
	 */
	public function getLineNumber()
	{
		return $this->InjectionTarget->getLineNumber();
	}
}