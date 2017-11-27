<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Service;

use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularIntegrationService
{
    /**
     * @var string
     */
    private $angularDirectory;

    /**
     * @var string
     */
    private $baseHref;

    /**
     * @var string
     */
    private $angularPath;

    /**
     * @var string
     */
    private $apiPath;

    /**
     * @var Kernel
     */
    private $kernel;

    public function __construct(
        Kernel $kernel,
        string $baseHref,
        string $angularPath,
        string $apiPath,
        string $angularDirectory
    ) {
        $this->angularDirectory = $angularDirectory;
        $this->baseHref = $baseHref;
        $this->angularPath = $angularPath;
        $this->apiPath = $apiPath;
        $this->kernel = $kernel;
    }

    /**
     * @return string
     */
    public function getAngularDirectory(): string
    {
        return $this->angularDirectory;
    }

    public function getAngularBaseHref(): string
    {
        return $this->baseHref . $this->angularPath;
    }

    public function getApiBaseHref(): string
    {
        return $this->baseHref . $this->getEnvPrefix($this->kernel->getEnvironment()) . $this->apiPath;
    }

    protected function getEnvPrefix(string $env): string
    {
        if ('prod' === $env) {
            return '';
        }

        return 'app_' . $env . '.php/';
    }
}
