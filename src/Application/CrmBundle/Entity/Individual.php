<?php


namespace Application\CrmBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;

class Individual extends Person
{
	private $leads;

	public function __construct()
	{
		parent::__construct();
		$this->leads = new ArrayCollection();
	}

	/**
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getLeads()
	{
		return $this->leads;
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $leads
	 */
	public function setLeads($leads)
	{
		$this->leads = $leads;
	}

	public function addLead(\Application\CrmBundle\Entity\Lead $lead)
	{
		$lead->setIndividual($this);
		$this->websites[] = $lead;

		return $this;
	}

	public function removeLead(\Application\CrmBundle\Entity\Lead $lead)
	{
		$lead->setIndividual(null);
		$this->websites->removeElement($lead);
	}

}