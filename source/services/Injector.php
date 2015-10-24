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

use tueena\core\services\injectionTargets\IInjectionTarget;
use tueena\core\services\injectionTargets\exceptions\TypeHintIsNotADefinedService;

class Injector implements IInjector
{
	/**
	 * @var ServiceFactory
	 */
	private $ServiceFactory;

	/**
	 * @param ServiceFactory $ServiceFactory
	 */
	public function __construct(ServiceFactory $ServiceFactory)
	{
		$this->ServiceFactory = $ServiceFactory;
	}

	/**
	 * Calls the passed in method and passes in the requested services.
	 *
	 * @param IInjectionTarget $InjectionTarget
	 * @return mixed
	 */
	public function resolve(IInjectionTarget $InjectionTarget)
	{
		$servicesToInject = $this->getServicesToInject($InjectionTarget);
		return $InjectionTarget->invoke($servicesToInject);
	}

	/**
	 * @param BaseInjectionTarget $InjectionTarget
	 * @return object[]
	 */
	private function getServicesToInject(IInjectionTarget $InjectionTarget)
	{
		$requiredTypes = $InjectionTarget->getRequiredTypes();
		$servicesToInject = [];
		foreach ($requiredTypes as $parameterName => $requestedType) {
			try {
				$Service = $this->ServiceFactory->getService($requestedType, $this);
			} catch (ServiceNotDefined $Exception) {
				throw new TypeHintIsNotADefinedService($InjectionTarget, $parameterName, $requestedType);
			}
			$servicesToInject[] = $Service;
		}
		return $servicesToInject;
	}
}