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

class ServiceNotDefined extends \Exception
{
	/**
	 * @var string
	 */
	private $identifyingTypeName;

	/**
	 * @var string
	 */
	private $callerFilePath;

	/**
	 * @var int
	 */
	private $callerLineNumber;

	/**
	 * @param string $identifyingTypeName
	 * @param string $callerFilePath
	 * @param int $callerLineNumber
	 */
	public function __construct($identifyingTypeName, $callerFilePath, $callerLineNumber)
	{
		$this->identifyingTypeName = $identifyingTypeName;
		$this->callerFilePath = $callerFilePath;
		$this->callerLineNumber = $callerLineNumber;
		$message = 'The service factory cannot build a service that has not been defined. ';
		$message .= "A service with the identifying type $identifyingTypeName has not been defined. ";
		$message .= "Is is requested in $callerFilePath on line $callerLineNumber.";
		parent::__construct($message);
	}

	/**
	 * @return string
	 */
	public function getIdentifyingTypeName()
	{
		return $this->identifyingTypeName;
	}

	/**
	 * @return string
	 */
	public function getCallerFilePath()
	{
		return $this->callerFilePath;
	}

	/**
	 * @return int
	 */
	public function getCallerLineNumber()
	{
		return $this->callerLineNumber;
	}
}