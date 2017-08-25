<?php


namespace Application\ClassificationBundle\Form\DataTransformer;


use Application\ClassificationBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;

class TagsDataTransformer implements DataTransformerInterface
{
	const DELIMITER = ',';
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $entityManager;

	function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}


	public function transform($value)
	{
		if ($value instanceof Collection) {
			$value = $value->toArray();
		}

		if (is_array($value)) {
			$result = array();
			/** @var Tag $tag  */
			foreach ($value as $tag) {
				$result[] = $tag->getName();
			}

			$value = implode(self::DELIMITER, $result);
		}


		return $value;
	}

	public function reverseTransform($value)
	{
		if (null === $value) {
			$value = '';
		}

		if (is_string($value)) {
			$value = explode(self::DELIMITER, $value);
		}
		if (is_array($value)) {
			$repository = $this->entityManager->getRepository('ApplicationClassificationBundle:Tag');
			$queryBuilder = $repository
				->createQueryBuilder('tag')
				->andWhere('tag.name IN (:tags)')
				->setParameter('tags', $value)
			;
			$result = $storedTags = $queryBuilder->getQuery()->execute();
			$storedTagNames = explode(self::DELIMITER, $this->transform($storedTags));
			$nonStoredTagNames = array_diff($value, $storedTagNames);

			foreach ($nonStoredTagNames as $tagName) {
				$tag = new Tag();
				$tag->setName($tagName);

				$result[] = $tag;
			}

			return new ArrayCollection($result);
		}

		return $value;
	}

	private function getLanguage()
	{
		return 'en';
	}
}