<?php


namespace Application\QuotationGeneratorBundle\Form\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class YamlToArrayTransformer implements DataTransformerInterface
{

	public function transform($value)
	{
		if (is_array($value)) {
			return Yaml::dump($value);
		}

		return $value;
	}

	public function reverseTransform($value)
	{
		if (is_string($value)) {
			try {
				return Yaml::parse($value);
			} catch (ParseException $e) {
				return $value;
			}
		}

		return null;
	}


} 