<?php


namespace Application\QuotationGeneratorBundle\View;


use Application\QuotationGeneratorBundle\Entity\GraphTraversal;
use Application\UserBundle\Entity\User;
use Doctrine\Common\Inflector\Inflector;
use Sonata\ClassificationBundle\Model\Tag;
use Symfony\Component\DependencyInjection\ContainerAware;

class QuotationPdfRenderer extends ContainerAware
{
	public function generate(GraphTraversal $traversal, $options = array('Attachment' => true))
	{
		$html = $this->renderAsHtml($traversal);
		$domPdf = $this->initializeDomPdf();

//		echo $html;die;
		$html = $this->processHtmlSrcPathsForPdf($html);


		$domPdf->load_html($html);
		$domPdf->render();
		$domPdf->stream(sprintf(
			"%s-%s.pdf",
			Tag::slugify($traversal->getQuotationName()),
			date('Y-m-d')
		), $options);
	}

	/**
	 * @param User $user
	 * @return string
	 * @throws \Exception
	 */
	public function renderAsHtml(GraphTraversal $traversal)
	{
		/** @var \Twig_Environment $environment */
		$environment = $this->container->get('twig');

		/** @var \Twig_Template $template */
		$template = $environment->loadTemplate('ApplicationQuotationGeneratorBundle:Pdf:quotation_summary.html.twig');

		return $template->render($environment->mergeGlobals(array(
			'traversal' => $traversal,
			'pdf'       => true,
		)));
	}

	/**
	 * @return \DOMPDF
	 */
	private function initializeDomPdf()
	{
		if (!defined('DOMPDF_ENABLE_AUTOLOAD')) {
			define('DOMPDF_ENABLE_AUTOLOAD', false);
		}

		require_once $this->container->getParameter('kernel.root_dir') . '/../vendor/dompdf/dompdf/dompdf_config.inc.php';

		return new \DOMPDF();
	}

	/**
	 * @param $html
	 * @return mixed
	 */
	private function processHtmlSrcPathsForPdf($html)
	{
		$webRoot = realpath($this->container->getParameter('kernel.root_dir') . '/../web/') . '/';
		$html = preg_replace_callback('/\"(http[s]?:\/\/[a-zA-Z1-9\.\-]+\/)[a-zA-Z0-9\.\-_\/]+\.(png|jpg|jpeg|css)/', function ($v) use ($webRoot) {
			return str_replace($v[1], $webRoot, $v[0]);
		}, $html);

		return $html;
	}
} 