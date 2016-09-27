<?php
namespace Application\CrmBundle\Block;

use Application\CrmBundle\Enum\ClientStatus;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientsBlockService extends BaseBlockService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var Pool
     */
    protected $admin_pool;

    /**
     * AdminSearchBlockService constructor.
     * @param string $name
     * @param EngineInterface $templating
     * @param EntityManagerInterface $em
     */
    public function __construct(
        $name,
        EngineInterface $templating,
        EntityManagerInterface $em,
        Pool $pool
    )
    {
        parent::__construct($name, $templating);
        $this->em = $em;
        $this->admin_pool = $pool;
    }

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => static::getTemplate()
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        if(false === $blockContext->getTemplate()) {
            $blockContext->setSetting('template', static::getTemplate());
        }

        $counts = array();
        $clientStatuses = ClientStatus::getChoices();

        foreach ($clientStatuses as $key => $val) {
            $counts[$key] = $this->getClientsRepository()->getClientCountByStatus($val);
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'         => $blockContext->getBlock(),
            'settings'      => $blockContext->getSettings(),
            'client_statuses' => $clientStatuses,
            'client_counts' => $counts,
            'new_clients'   => $this->getNewClients(),
            'admin'         => $this->getAdmin()
        ), $response);
    }

    /**
     * @return string
     */
    protected static function getTemplate() {
        return 'ApplicationCrmBundle:Block:clients_block.html.twig';
    }

    /**
     * @return \Application\CrmBundle\Entity\ClientRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getClientsRepository() {
        return $this->em->getRepository('ApplicationCrmBundle:Client');
    }

    protected function getNewClients() {

        $now = new \DateTime();

        $since = $now->sub(
            new \DateInterval(
                sprintf(
                    'P%sDT%sH%sM%sS',
                    ($now->format('N') - 1),
                    $now->format('H'),
                    $now->format('i'),
                    ($now->format('s') === 0 ? 1 : $now->format('s') - 1)
                )
            )
        );

        return $this->getClientsRepository()->getNewClientsCountSince($since);
    }

    /**
     * @return null|\Sonata\AdminBundle\Admin\AdminInterface
     */
    protected function getAdmin() {
        return $this->admin_pool->getAdminByClass('Application\CrmBundle\Entity\Client');
    }
}