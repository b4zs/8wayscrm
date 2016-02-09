<?php

namespace Application\ObjectIdentityBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApplicationObjectIdentityBundle extends Bundle
{
	public function getParent()
	{
		return 'CoreObjectIdentityBundle';
	}

}
