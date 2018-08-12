<?php

namespace Dontdrinkandroot\AppIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularBuildCommand extends AbstractAngularIntegrationCommand
{
    protected static $defaultName = 'ddr:app:angular:build';

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption('force-prod', null, InputOption::VALUE_NONE);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $forceProd = $input->getOption('force-prod');

        $output->writeln('Building Angular');
        $angularBuildCommandLine = 'npm run ng -- build --no-progress --base-href ' . $this->getIntegrationService(
            )->getAngularBaseHref();
        if ($forceProd || $this->getIntegrationService()->isProd()) {
            $angularBuildCommandLine .= ' --prod --aot';
        }
        $angularBuildProcess = new Process($angularBuildCommandLine);
        $angularBuildProcess->setTimeout(300);
        $angularBuildProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
        $output->writeln('Executing: ' . $angularBuildProcess->getCommandLine());
        $angularBuildProcess->mustRun();
    }
}
