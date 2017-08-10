<?php

namespace Application\QuotationGeneratorBundle\Enum;

class ActionType
{
	const
		ADD_SPECIFICATION_ITEM  = 1,
		ADD_QUOTATION_ITEM      = 2,
		IMPLY_QUESTION          = 3;

	const
		SERVICE_NAME_PREFIX = 'application_event.action_type.';

	public static function getChoices()
	{
		return array(
			self::IMPLY_QUESTION            => 'Imply Question',
			self::ADD_SPECIFICATION_ITEM    => 'Add Specification Item',
			self::ADD_QUOTATION_ITEM        => 'Add Quotation Item',
		);
	}

	public static function getValuetoServiceMap()
	{
		return array(
			self::IMPLY_QUESTION            => self::SERVICE_NAME_PREFIX . 'imply_question',
			self::ADD_SPECIFICATION_ITEM    => self::SERVICE_NAME_PREFIX . 'add_specification_item',
			self::ADD_QUOTATION_ITEM        => self::SERVICE_NAME_PREFIX . 'add_quotation_item',
		);
	}

	public static function mapValueToService($value)
	{
		$map = self::getValuetoServiceMap();
		return isset($map[$value]) ? $map[$value] : null;
	}

} 