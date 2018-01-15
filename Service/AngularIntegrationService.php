<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Service;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $themeColor;

    /**
     * @var string
     */
    private $backgroundColor;

    /**
     * @var array
     */
    private $externalStyles;

    public function __construct(
        KernelInterface $kernel,
        string $baseHref,
        string $angularPath,
        string $apiPath,
        string $angularDirectory,
        string $name,
        string $shortName,
        string $themeColor,
        string $backgroundColor,
        array $externalStyles
    ) {
        $this->angularDirectory = $angularDirectory;
        $this->baseHref = $baseHref;
        $this->angularPath = $angularPath;
        $this->apiPath = $apiPath;
        $this->kernel = $kernel;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->themeColor = $themeColor;
        $this->backgroundColor = $backgroundColor;
        $this->externalStyles = $externalStyles;
    }

    /**
     * @return string
     */
    public function getAngularDirectory(): string
    {
        return realpath($this->angularDirectory);
    }

    public function getAngularBaseHref(): string
    {
        return $this->baseHref . $this->angularPath;
    }

    public function getApiBaseHref(): string
    {
        return $this->baseHref . $this->getFrontendControllerByEnv($this->getEnvironment()) . $this->apiPath;
    }

    public function getEnvironment(): string
    {
        return $this->kernel->getEnvironment();
    }

    protected function getFrontendControllerByEnv(string $env): string
    {
        if (Kernel::VERSION_ID >= 40000 || 'prod' === $env) {
            return '';
        }

        return 'app_' . $env . '.php/';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getThemeColor(): string
    {
        return $this->themeColor;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    /**
     * @return array
     */
    public function getExternalStyles(): array
    {
        return $this->externalStyles;
    }
}
