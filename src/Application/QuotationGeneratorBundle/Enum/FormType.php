<?php


namespace Application\QuotationGeneratorBundle\Enum;


class FormType
{
	public static function getChoices()
	{
		return array(
			'singlechoice'      => 'single choice',
			'multiplechoice'    => 'multiple choice',
			'number'            => 'number',
			'text'              => 'text',
			'textarea'          => 'textarea',
		);

	}

	public static function getOptionBasedTypes()
	{
		return array('singlechoice', 'multiplechoice');
	}

}