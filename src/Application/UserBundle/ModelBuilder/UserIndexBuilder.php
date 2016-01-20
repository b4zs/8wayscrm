<?php

namespace Application\UserBundle\ModelBuilder;

use Application\UserBundle\Entity\User;
use Core\ToolsBundle\ModelBuilder\ChainableModelBuilder;

class UserIndexBuilder extends ChainableModelBuilder
{

    public function build($source, array $options = array(), &$result = array())
    {

        if ($source instanceof User AND $this->shouldBuild($options)) {

            $data = array();

            if($source->getUsername()){
                $data['user_name'] = $source->getUsername();
            }

            if($source->getFirstname() || $source->getLastname()){
                $data['name'] = $source->getFirstname() . ' ' . $source->getLastname();
            }

            if($source->getEmail()){
                $data['email'] = $source->getEmail();
            }

            if($source->getTitle()){
                $data['title'] = $source->getTitle();
            }

            $result = array_merge($result, array_values($data));
        }


        return parent::build($source, $options, $result);
    }

}