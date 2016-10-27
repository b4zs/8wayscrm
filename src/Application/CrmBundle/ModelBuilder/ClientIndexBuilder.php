<?php

namespace Application\CrmBundle\ModelBuilder;

use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Project;
use Core\ToolsBundle\ModelBuilder\ChainableModelBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientIndexBuilder extends ChainableModelBuilder
{

    public function build($source, array $options = array(), &$result = array())
    {

        if ($source instanceof AbstractClient AND $this->shouldBuild($options)) {

            $data = array();

            if($source->getOwner() instanceof UserInterface){
                $data['owner'] = $source->getOwner()->getFullname();
            }

            if($source->getProjectManager() instanceof UserInterface){
                $data['pm'] = $source->getProjectManager()->getUsername();
            }

            if($source->getReferral()){
                $data['referral'] = $source->getReferral();
            }

            if($source->getProjects()){
                foreach($source->getProjects() as $project){
                    if($project instanceof Project){
                        $data['projects'][] = array(
                            'name' => $project->getName(),
                            'desc' => $project->getDescription(),
                        );
                    }
                }
                $data['projects'] = isset($data['projects']) ? implode(' ', array_map(function($item) {
                    return implode(' ', $item);
                }, $data['projects'])) : null;
            }

            if($source->getCompany() instanceof Company){
                $data['company'] = array(
                    'name' => $source->getCompany()->getName(),
                    'country' => $source->getCompany()->getCountry(),
                    'email' => $source->getCompany()->getEmail(),
                    'phone1' => $source->getCompany()->getPhone1(),
                    'phone2' => $source->getCompany()->getPhone2(),
                    'soa' => $source->getCompany()->getSectorOfActivity(),
                );
            }

            $data['company'] = isset($data['company']) ? implode(' ', $data['company']) : null;

            foreach ($source->getContacts() as $contact) {
                $data['contacts'][] = array(
                    'name' => $contact->getPerson()->getFullName(),
                    'cemail' => $contact->getPerson()->getCompanyEmail(),
                    'cphone' => $contact->getPerson()->getCompanyPhone(),
                    'lphone' => $contact->getPerson()->getDirectLinePhone(),
                    'pemail' => $contact->getPerson()->getPrivateEmail(),
                    'pphone' => $contact->getPerson()->getPrivatePhone(),
                    'skype' => $contact->getPerson()->getSkypeId(),
                    'note' => $contact->getNote(),
                );
            }

            $data['contacts'] = isset($data['contacts']) ? implode(' ', array_map(function($item) {
                return implode(' ', $item);
            }, $data['contacts'])) : null;

            $result = array_merge($result, array_values($data));
        }


        return parent::build($source, $options, $result);
    }

}