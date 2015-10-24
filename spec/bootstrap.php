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
 * @package tueena
 * @subpackage core
 * @author Bastian Fenske <bastian.fenske@tueena.org>
 * @copyright Copyright (c) Bastian Fenske <bastian.fenske@tueena.org>
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link http://tueena.org/
 * @file
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function ($className) {

	$parts = explode('\\', $className);
	$firstPart = array_shift($parts);
	if ($firstPart !== 'tueena')
		return false;
	$secondPart = array_shift($parts);

	switch ($secondPart) {
		case 'core':
			$basePath = __DIR__ . '/../source/';
			break;
		case 'spec':
			array_shift($parts);
			$basePath = __DIR__ . '/';
			break;
	}

	$fileName = $basePath . implode('/', $parts) . '.php';
	if (!file_exists($fileName))
		return false;
	include $fileName;
	return true;
});