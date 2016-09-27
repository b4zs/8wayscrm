<?php

namespace Application\AdminBundle\Util\Search;


class ResultProviderPool
{

    /** @var ResultProviderInterface[] */
    private $providers = array();

    /**
     * @param ResultProviderInterface $provider
     * @param string $class
     */
    public function addProvider(ResultProviderInterface $provider, $class)
    {
        $this->providers[$class] = $provider;
    }

    /**
     * @param string $class
     * @return ResultProviderInterface
     */
    public function getProvider($class)
    {
        if(!$this->hasProvider($class)){
            throw new \InvalidArgumentException(sprintf('The requested ResultProvider for class "%s", can not be found in the pool.', $class));
        }

        return $this->providers[$class];
    }

    /**
     * @param string $class
     * @return bool
     */
    public function hasProvider($class)
    {
        return isset($this->providers[$class]);
    }

}