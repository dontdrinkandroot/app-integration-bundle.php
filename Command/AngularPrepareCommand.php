<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularPrepareCommand extends AbstractAngularIntegrationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ddr:angular:prepare');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->runNpmInstall($output);
        $this->configureApiEndpoint($output);
        $this->writeIndex($output);
        $this->writeManifest($output);
        $this->generateIcons($output);
    }

    private function runNpmInstall(OutputInterface $output)
    {
        $output->writeln('Installing Node Packages: npm install');
        $npmInstallProcess = new Process('npm install');
        $npmInstallProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
        $npmInstallProcess->mustRun(
            function ($type, $buffer) {
                if (Process::ERR === $type) {
                    echo 'ERR > ' . $buffer;
                } else {
                    echo 'OUT > ' . $buffer;
                }
            }
        );
    }

    private function configureApiEndpoint(OutputInterface $output)
    {
        $output->writeln('Configuring API endpoint: ' . $this->getIntegrationService()->getApiBaseHref());
        $apiConfigTs = $this->getTwig()->render(
            'DdrAngularIntegrationBundle::api-config.twig.ts',
            [
                'baseUrl' => $this->getIntegrationService()->getApiBaseHref()
            ]
        );
        file_put_contents(
            $this->getIntegrationService()->getAngularDirectory() . '/src/environments/api-config.ts',
            $apiConfigTs
        );
    }

    private function writeIndex(OutputInterface $output)
    {
        $output->writeln('Writing Index');
        $manifestContent = $this->getTwig()->render(
            'DdrAngularIntegrationBundle::index.html.twig',
            [
                'startUrl'        => $this->getIntegrationService()->getAngularBaseHref(),
                'name'            => $this->getContainer()->getParameter('ddr_angular_integration.name'),
                'shortName'       => $this->getContainer()->getParameter('ddr_angular_integration.short_name'),
                'themeColor'      => $this->getContainer()->getParameter('ddr_angular_integration.theme_color'),
                'backgroundColor' => $this->getContainer()->getParameter('ddr_angular_integration.background_color'),
            ]
        );
        file_put_contents($this->getIntegrationService()->getAngularDirectory() . '/src/index.html', $manifestContent);
    }

    private function writeManifest(OutputInterface $output)
    {
        $output->writeln('Writing Manifest');
        $manifestContent = $this->getTwig()->render(
            'DdrAngularIntegrationBundle::manifest.twig.json',
            [
                'startUrl'        => $this->getIntegrationService()->getAngularBaseHref(),
                'name'            => $this->getContainer()->getParameter('ddr_angular_integration.name'),
                'shortName'       => $this->getContainer()->getParameter('ddr_angular_integration.short_name'),
                'themeColor'      => $this->getContainer()->getParameter('ddr_angular_integration.theme_color'),
                'backgroundColor' => $this->getContainer()->getParameter('ddr_angular_integration.background_color'),
            ]
        );
        file_put_contents(
            $this->getIntegrationService()->getAngularDirectory() . '/src/manifest.json',
            $manifestContent
        );
    }

    private function generateIcons(OutputInterface $output)
    {
        $output->writeln('Generating Icons');

        $sizes = [16, 32, 48, 96, 144, 180, 192];

        //convert -background none angular/src/assets/icons/template.svg -resize 192x192 angular/src/assets/icons/icon_192.png

        foreach ($sizes as $size) {
            $convertProcess = new Process(
                'rsvg-convert -w ' . $size . ' -h ' . $size . ' -o src/assets/icons/icon_' . $size . '.png src/assets/icons/template.svg'
            );
            $output->writeln($convertProcess->getCommandLine());
            $convertProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
            $convertProcess->mustRun(
                function ($type, $buffer) {
                    if (Process::ERR === $type) {
                        echo 'ERR > ' . $buffer;
                    } else {
                        echo 'OUT > ' . $buffer;
                    }
                }
            );
        }
    }
}
