<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Dontdrinkandroot\AngularIntegrationBundle\Service\AngularIntegrationService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Twig\Environment;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class AbstractAngularIntegrationCommand extends ContainerAwareCommand
{
    protected function getEnvironment(): string
    {
        return $this->getContainer()->get('kernel')->getEnvironment();
    }

    protected function getIntegrationService(): AngularIntegrationService
    {
        return $this->getContainer()->get('ddr_angular_integration.service.integration');
    }

    protected function getTwig(): Environment
    {
        return $this->getContainer()->get('twig');
    }
}
