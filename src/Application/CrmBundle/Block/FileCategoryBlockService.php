<?php


namespace Application\CrmBundle\Block;


use Application\CrmBundle\Enum\FileCategoryEnum;
use Application\MediaBundle\Entity\Media;
use Application\ObjectIdentityBundle\Entity\ObjectIdentity;
use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Templating\EngineInterface;

class FileCategoryBlockService extends BaseBlockService
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
    ) {
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
        $context = $blockContext->getBlock()->getSetting('context');

        $contextIds = array_map(function(ObjectIdentityAware $o){
            if(!$o->getObjectIdentity() instanceof ObjectIdentity) {
                $id = null;
                if(method_exists($o, 'getId')) {
                    $id = $o->getId();
                }
                throw new \ErrorException(sprintf('No ObjectIdentity for object %s:%s', get_class($o), $id));
            }
            return $o->getObjectIdentity()->getId();
        }, $context);


        //get related objects

        if (false === $blockContext->getTemplate()) {
            $blockContext->setSetting('template', static::getTemplate());
        }
        $categories = array();

        $fileCategories = FileCategoryEnum::getChoices();

        foreach ($fileCategories as $key => $val) {
            $categories[$key] = $this->getMediaRepository()->findBy([
                'fileCategory' => $key
            ]);
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block' => $blockContext->getBlock(),
            'settings' => $blockContext->getSettings(),
            'file_categories' => $categories,
        ), $response);
    }

    /**
     * @return string
     */
    protected static function getTemplate()
    {
        return 'ApplicationCrmBundle:Block:file_category_block.html.twig';
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getMediaRepository()
    {
        return $this->em->getRepository(Media::class);
    }
}