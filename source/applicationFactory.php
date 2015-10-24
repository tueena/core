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

require_once __DIR__ . '/Application.php';

use \tueena\core\Application;

$minVersion = '5.3.0';

if (version_compare(phpversion(), $minVersion, '<'))
	throw new \Exception('At least php version ' . $minVersion . ' is required to use the tueena framework. Version ' . phpversion() . ' is running.');

return function (\Closure $LoaderConfigurator, \Closure $ServiceDefiner, \Closure $MainFunction) {
	return new Application($LoaderConfigurator, $ServiceDefiner, $MainFunction);
};