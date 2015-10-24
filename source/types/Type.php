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

namespace tueena\core\types;

abstract class Type
{
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @param string $name
	 */
	protected final function __construct($name)
	{
		$this->name = $name;
	}

	/**
	 * Builds and returns an instance of ConcreteClassType, AbstractClassType or
	 * InterfaceType or throws an exception if the passed in string is not the
	 * name of an existing class or interface.
	 *
	 * @param string $typeName
	 * @return Type
	 * @throws \Exception
	 */
	public static function fromName($typeName)
	{
		if (!class_exists($typeName) && !interface_exists($typeName)) {
			$filePath = debug_backtrace()[0]['file'];
			$lineNumber = debug_backtrace()[0]['line'];
			throw new \Exception(sprintf(
				'Parameter passed to %s() must be the name of a class or interface. Class or interface %s could not be found. Called in %s on line %d.',
				__METHOD__,
				$typeName,
				$filePath,
				$lineNumber
			));
		}

		if (interface_exists($typeName))
			return new InterfaceType($typeName);
		if ((new \ReflectionClass($typeName))->isAbstract())
			return new AbstractClassType($typeName);
		return new ConcreteClassType($typeName);
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param Type $OtherType
	 * @return bool
	 */
	public function isInstanceOf(Type $OtherType)
	{
		$thisTypeName = $this->getName();
		$otherTypeName = $OtherType->getName();

		if ($thisTypeName === $otherTypeName)
			return true;

		return (new \ReflectionClass($thisTypeName))->isSubclassOf($otherTypeName);
	}
}