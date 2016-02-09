<?php

namespace Application\CrmBundle\ModelBuilder;

use Application\CrmBundle\Entity\AbstractClient;
use Application\CrmBundle\Entity\Company;
use Application\CrmBundle\Entity\Project;
use Application\CrmBundle\Enum\ProjectStatus;
use Core\ToolsBundle\ModelBuilder\ChainableModelBuilder;
use Symfony\Component\Security\Core\User\UserInterface;

class ProjectIndexBuilder extends ChainableModelBuilder
{

    public function build($source, array $options = array(), &$result = array())
    {

        if ($source instanceof Project AND $this->shouldBuild($options)) {

            $data = array();

            if($source->getName()){
                $data['name'] = $source->getName();
            }

            if($source->getDescription()){
                $data['description'] = $source->getDescription();
            }

            if($source->getStatus()){
                $projectStatuses = ProjectStatus::getChoices();
                $data['status'] = $projectStatuses[$source->getStatus()];
            }

            $result = array_merge($result, array_values($data));
        }


        return parent::build($source, $options, $result);
    }

}