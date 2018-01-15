<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Dontdrinkandroot\AngularIntegrationBundle\Service\AngularIntegrationService;
use Symfony\Component\Console\Command\Command;
use Twig\Environment;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
abstract class AbstractAngularIntegrationCommand extends Command
{
    /**
     * @var Environment
     */
    private $twigEnvironment;

    /**
     * @var AngularIntegrationService
     */
    private $integrationService;

    /**
     * AbstractAngularIntegrationCommand constructor.
     */
    public function __construct(
        Environment $twigEnvironment,
        AngularIntegrationService $integrationService
    ) {
        parent::__construct();
        $this->twigEnvironment = $twigEnvironment;
        $this->integrationService = $integrationService;
    }

    protected function getIntegrationService(): AngularIntegrationService
    {
        return $this->integrationService;
    }

    protected function getTwig(): Environment
    {
        return $this->twigEnvironment;
    }
}
