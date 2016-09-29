<?php

namespace Application\CrmBundle\Mailer;

use Core\ObjectIdentityBundle\Model\ObjectIdentityAware;
use Doctrine\Common\Util\ClassUtils;
use FOS\UserBundle\Model\UserInterface;
use Octet\Ticketing\Lib\Message\Command\SendReminderNotificationCommand;
use Symfony\Component\DependencyInjection\ContainerAware;

class CrmMailer extends ContainerAware
{
	public function handleSendReminderNotificationCommand(SendReminderNotificationCommand $command)
	{
        $this->setupRouter();
        $relatedObjects = array();
        /** @var ObjectIdentityAware $reminderHasRelatedObject */

        foreach ($command->getSubject()->getNote()->getRelatedObjects() as $noteHasRelatedObject) {
			$relatedObjects[] = array(
				'url'   => $this->getAdminPool()->getAdminByClass(ClassUtils::getClass($noteHasRelatedObject))->generateObjectUrl('edit', $noteHasRelatedObject, array(), true),
				'title' => (string)$noteHasRelatedObject,
			);
		}

		if(null === $command->getSubject()->getAssignee()) {
            return;
        }

		$this->sendMailTemplate($command->getSubject()->getAssignee(), 'ApplicationCrmBundle:Email:reminder_notification.html.twig', array(
			'reminder'          => $command->getSubject(),
			'user'              => $command->getSubject()->getAssignee(),
			'note_admin'        => $this->getAdminPool()->getAdminByClass(ClassUtils::getClass($command->getSubject()->getNote())),
			'related_objects'   => $relatedObjects,
		));
	}

	private function sendMailTemplate(UserInterface $recipient, $template, array $vars)
	{
		$this->setupRouter();
		$template = $this->getTwig()->loadTemplate($template);
		$subject = $template->renderBlock('subject', $vars);
		$body = $template->renderBlock('html_body', $vars);
		$sender = $this->getSender();

		$message = \Swift_Message::newInstance()
			->setSubject($subject)
			->setFrom($sender, $sender)
			->setTo($recipient->getEmail())
			->setBody($body, 'text/html');

		$this->getMailer()->send($message);
	}

	private function setupRouter()
	{
		$router = $this->getRouter();
		if ('localhost' === $router->getContext()->getHost()) {
			$router->getContext()->setHost($this->getApplicationDomain());
		}
	}

	private function getTwig()
	{
		return $this->container->get('twig');
	}

	private function getSender()
	{
		return $this->container->getParameter('mailer_sender');
	}

	private function getAdminPool()
	{
		return $this->container->get('sonata.admin.pool');
	}

	private function getRouter()
	{
		return $this->container->get('router');
	}

	private function getApplicationDomain()
	{
		return $this->container->getParameter('static_domain');
	}

	private function getMailer()
	{
		return $this->container->get('mailer');
	}

}