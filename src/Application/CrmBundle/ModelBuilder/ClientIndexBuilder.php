<?php

namespace Application\CrmBundle\ModelBuilder;

use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Project;
use Core\ToolsBundle\ModelBuilder\ChainableModelBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientIndexBuilder extends ChainableModelBuilder
{

    public function build($source, array $options = array(), &$result = array())
    {

        if ($source instanceof AbstractClient AND $this->shouldBuild($options)) {

            $data = array();

            if($source->getOwner() instanceof UserInterface){
                $data['owner'] = $source->getOwner()->getUsername();
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
                        $data['projects'][] = $project->getName();
                    }
                }
                $data['projects'] = isset($data['projects']) ? implode(' ', $data['projects']) : null;
            }

            if($source->getCompany() instanceof Company){
                $data['company'] = $source->getCompany()->getName();
            }

            $result = array_merge($result, array_values($data));
        }


        return parent::build($source, $options, $result);
    }

}