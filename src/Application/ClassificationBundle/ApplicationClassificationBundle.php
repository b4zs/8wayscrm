<?php

namespace Application\ClassificationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationClassificationBundle extends Bundle
{
    public function getParent()
    {
        return 'SonataClassificationBundle';
    }

}
