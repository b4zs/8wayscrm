<?php

namespace Application\QuotationGeneratorBundle\Model;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class PropertyAccessorWrapper
{
	private $wrappedObject;

	private static function getPropertyAccessorInstance()
	{
		static $propertyAccessor;

		if (null === $propertyAccessor) {
			$propertyAccessor = new PropertyAccessor(false, true);
		}

		return $propertyAccessor;
	}

	function __construct($wrappedObject)
	{
		$this->wrappedObject = $wrappedObject;
	}


	public function __get($field)
	{
		$result = self::getPropertyAccessorInstance()
			->getValue($this->wrappedObject, $field);

		if (is_object($result)) {
			return new self($result);
		} else {
			return $result;
		}
	}

	public function __set($field, $value)
	{
		self::getPropertyAccessorInstance()
			->setValue($this->wrappedObject, $field, $value);
	}

	function __call($name, $arguments)
	{
		return call_user_func_array(array($this->wrappedObject, $name), $arguments);
	}


}