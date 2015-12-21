<?php


namespace Core\LoggableEntityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Gedmo\Loggable\Entity\LogEntry
 *
 * @ORM\Table(
 *     name="log_entries",
 *  indexes={
 *      @ORM\Index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @ORM\Index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @ORM\Index(name="log_user_lookup_idx", columns={"username"}),
 *      @ORM\Index(name="log_version_lookup_idx", columns={"object_id", "object_class", "version"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class LogEntry extends AbstractLogEntry
{

	/**
	 * @var string
	 * @ORM\Column(name="comment", type="text", nullable=true)
	 */
	private $comment;

	/**
	 * @var string
	 * @ORM\Column(name="custom_action", type="string", length=255, nullable=true)
	 */
	private $customAction;

	/**
	 * @var string
	 * @ORM\Column(name="extra_data", type="json_array", nullable=true)
	 */
	private $extraData = array();

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}

	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	/**
	 * @return string
	 */
	public function getCustomAction()
	{
		return $this->customAction;
	}

	/**
	 * @param string $customAction
	 */
	public function setCustomAction($customAction)
	{
		$this->customAction = $customAction;
	}

	/**
	 * @return string
	 */
	public function getExtraData()
	{
		return $this->extraData;
	}

	/**
	 * @param string $extraData
	 */
	public function setExtraData($extraData)
	{
		$this->extraData = $extraData;
	}

	public function setLoggedAt(\DateTime $value = null)
	{
		if (null === $value) {
			$value = new \DateTime();
		}
		$this->loggedAt = $value;
	}
}
