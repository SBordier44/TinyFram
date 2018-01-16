<?php

namespace Framework\Database;

class Hydrator
{
	/**
	 * @param array $array
	 * @param mixed $object
	 * @return mixed
	 */
	public static function hydrate(array $array, $object)
	{
		$instance = \is_string($object) ? new $object() : $object;
		foreach ($array as $key => $value) {
			$method = self::getSetter($key);
			if (method_exists($instance, $method)) {
				$instance->$method($value);
			} else {
				$property            = lcfirst(self::getProperty($key));
				$instance->$property = $value;
			}
		}
		return $instance;
	}
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	private static function getSetter(string $fieldName): string
	{
		return 'set' . self::getProperty($fieldName);
	}
	
	/**
	 * @param string $fieldName
	 * @return string
	 */
	private static function getProperty(string $fieldName): string
	{
		return implode('', array_map('ucfirst', explode('_', $fieldName)));
	}
}
