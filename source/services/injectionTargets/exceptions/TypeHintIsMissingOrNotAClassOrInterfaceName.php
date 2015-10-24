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

class TypeHintIsMissingOrNotAClassOrInterfaceName extends InvalidInjectionTargetParameter
{
	/**
	 * @param IInjectionTarget $InjectionTarget
	 * @param string $parameterName
	 */
	public function __construct(IInjectionTarget $InjectionTarget, $parameterName)
	{
		$message = 'The type hint is missing or not a class or interface name. Each parameter of an injection target must have an existing class or interface as type hint.';
		parent::__construct($InjectionTarget, $parameterName, $message);
	}
}