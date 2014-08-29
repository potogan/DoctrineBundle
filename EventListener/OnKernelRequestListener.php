<?php
namespace Potogan\DoctrineBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * The ExportableReportListener class handles the @ExportableReport annotation.
 */
class OnKernelRequestListener extends ContainerAware
{
	/**
	 * Renders the template and initializes a new response object with the
	 * rendered template content.
	 *
	 * @param GetResponseEvent $event A GetResponseEvent instance
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		$config = $this->container->get('doctrine.orm.entity_manager')->getConfiguration();

		$config->addCustomHydrationMode('entities', 'Potogan\\DoctrineBundle\\Hydrators\\MultipleEntitiesHydrator');
		$config->addCustomStringFunction('IF', 'Potogan\\DoctrineBundle\\Query\\Functions\\MysqlIf');
	}

}
