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

use tueena\core\types\ConcreteClassType;

class ImplementingType implements IImplementingTypeOrFactoryFunction
{
	/**
	 * @var ConcreteClassType
	 */
	private $type;

	/**
	 * @var bool
	 */
	private $isSameAsIdentifyingType = false;


	/**
	 * @param ConcreteClassType $type
	 */
	private function __construct(ConcreteClassType $type = null)
	{
		if ($type != null)
			$this->type = $type;
		else
			$this->isSameAsIdentifyingType = true;
	}

	/**
	 * Returns a new instance of the ImplementingType class. If the implementing
	 * type is the same as the identifying type, the isTheSame() method can be
	 * used instead.
	 *
	 * @param ConcreteClassType $type
	 * @return ImplementingType
	 */
	public static function is(ConcreteClassType $type)
	{
		return new ImplementingType($type);
	}

	/**
	 * Returns a new instance of the ImplementingType class. Use this method to
	 * define, that the implementing type is the same as the identifying type.
	 *
	 * @return ImplementingType
	 */
	public static function isTheSame()
	{
		return new ImplementingType;
	}

	/**
	 * @return ConcreteClassType
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function isSameAsIdentifyingType()
	{
		return $this->isSameAsIdentifyingType;
	}
}