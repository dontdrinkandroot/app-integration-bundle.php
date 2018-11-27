<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Service;

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

    /**
     * @var string
     */
    private $packageManager;

    /**
     * @var string
     */
    private $angularSrcDirectory;

    public function __construct(
        KernelInterface $kernel,
        string $baseHref,
        string $angularPath,
        string $apiPath,
        string $angularDirectory,
        string $angularSrcDirectory,
        string $name,
        string $shortName,
        string $themeColor,
        string $backgroundColor,
        array $externalStyles,
        string $packageManager
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
        $this->packageManager = $packageManager;
        $this->angularSrcDirectory = $angularSrcDirectory;
    }

    public function getAngularDirectory(): string
    {
        return realpath($this->angularDirectory);
    }

    public function getAngularSrcDirectory(): string
    {
        return realpath($this->angularSrcDirectory);
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

    public function isProd(): bool
    {
        return 'prod' === $this->getEnvironment();
    }

    protected function getFrontendControllerByEnv(string $env): string
    {
        if (Kernel::VERSION_ID >= 40000 || 'prod' === $env) {
            return '';
        }

        return 'app_' . $env . '.php/';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getThemeColor(): string
    {
        return $this->themeColor;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function getExternalStyles(): array
    {
        return $this->externalStyles;
    }

    public function getPackageManager(): string
    {
        return $this->packageManager;
    }
}
