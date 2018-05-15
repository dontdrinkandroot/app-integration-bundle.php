<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularPrepareCommand extends AbstractAngularIntegrationCommand
{
    protected static $defaultName = 'ddr:app:angular:prepare';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption('skip-install', InputOption::VALUE_OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true !== $input->getOption('skip-install')) {
            $this->runInstall($output);
        }
        $this->configureApiEndpoint($output);
        $this->writeIndex($output);
        $this->writeManifest($output);
        $this->generateIcons($output);
    }

    private function runInstall(OutputInterface $output)
    {
        $packageManager = $this->getIntegrationService()->getPackageManager();

        switch ($packageManager) {

            case 'npm':

                $output->writeln('Installing Node Packages: npm install');

                $installProcess = new Process('npm install');
                $installProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
                $installProcess->mustRun(
                    function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > ' . $buffer;
                        } else {
                            echo 'OUT > ' . $buffer;
                        }
                    }
                );

                $outdatedProcess = new Process('npm outdated');
                $outdatedProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
                $outdatedProcess->run(
                    function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > ' . $buffer;
                        } else {
                            echo 'OUT > ' . $buffer;
                        }
                    }
                );

                break;

            case 'yarn':

                $output->writeln('Installing Node Packages: yarn install');
                $installProcess = new Process('yarn install');
                $installProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
                $installProcess->mustRun(
                    function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > ' . $buffer;
                        } else {
                            echo 'OUT > ' . $buffer;
                        }
                    }
                );

                $outdatedProcess = new Process('yarn outdated');
                $outdatedProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
                $outdatedProcess->run(
                    function ($type, $buffer) {
                        if (Process::ERR === $type) {
                            echo 'ERR > ' . $buffer;
                        } else {
                            echo 'OUT > ' . $buffer;
                        }
                    }
                );

                break;

            default:
                throw new \RuntimeException('Unsupported package manager: ' . $packageManager);
        }
    }

    private function configureApiEndpoint(OutputInterface $output)
    {
        $output->writeln('Configuring API endpoint: ' . $this->getIntegrationService()->getApiBaseHref());
        $apiConfigTs = $this->getTwig()->render(
            '@DdrAppIntegration/api-config.ts.twig',
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
            '@DdrAppIntegration/index.html.twig',
            [
                'startUrl'        => $this->getIntegrationService()->getAngularBaseHref(),
                'name'            => $this->getIntegrationService()->getName(),
                'shortName'       => $this->getIntegrationService()->getShortName(),
                'themeColor'      => $this->getIntegrationService()->getThemeColor(),
                'backgroundColor' => $this->getIntegrationService()->getBackgroundColor(),
                'externalStyles'  => $this->getIntegrationService()->getExternalStyles(),
            ]
        );
        file_put_contents($this->getIntegrationService()->getAngularDirectory() . '/src/index.html', $manifestContent);
    }

    private function writeManifest(OutputInterface $output)
    {
        $output->writeln('Writing Manifest');
        $manifestContent = $this->getTwig()->render(
            '@DdrAppIntegration/manifest.json.twig',
            [
                'startUrl'        => $this->getIntegrationService()->getAngularBaseHref(),
                'name'            => $this->getIntegrationService()->getName(),
                'shortName'       => $this->getIntegrationService()->getShortName(),
                'themeColor'      => $this->getIntegrationService()->getThemeColor(),
                'backgroundColor' => $this->getIntegrationService()->getBackgroundColor(),
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

        $sizes = [16, 32, 48, 72, 96, 128, 144, 152, 180, 192, 384, 512];

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
