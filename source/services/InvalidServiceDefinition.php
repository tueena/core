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

use tueena\core\services\ServiceDefinition;

class InvalidServiceDefinition extends \Exception
{
	/**
	 * @param ServiceDefinition $ServiceDefinition
	 * @param string $specificMessage
	 */
	public function __construct(ServiceDefinition $ServiceDefinition, $specificMessage)
	{
		$message = sprintf(
			"Invalid definition of the service %s in %s on line %d: %s",
			$ServiceDefinition->getIdentifyingType()->getName(),
			$ServiceDefinition->getDefiningFilePath(),
			$ServiceDefinition->getDefiningLineNumber(),
			$specificMessage
		);
		parent::__construct($message);
	}
}