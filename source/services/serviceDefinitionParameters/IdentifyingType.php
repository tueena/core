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

namespace tueena\core\services\serviceDefinitionParameters;

use tueena\core\types\Type;

class IdentifyingType
{
	/**
	 *
	 * @var Type
	 */
	private $type;

	/**
	 * @param Type $type
	 */
	private function __construct(Type $type)
	{
		$this->type = $type;
	}

	/**
	 * Returns a new instance of the IdentifyingType class.
	 *
	 * @param Type $type
	 * @return IdentifyingType
	 */
	public static function is(Type $type)
	{
		return new IdentifyingType($type);
	}

	/**
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}
}