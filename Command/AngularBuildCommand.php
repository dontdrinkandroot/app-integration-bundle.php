<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularBuildCommand extends AbstractAngularIntegrationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ddr:angular:build');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Building Angular');
        $angularBuildCommandLine = 'ng build';
        $angularBuildCommandLine .= ' --quiet -bh ' . $this->getIntegrationService()->getAngularBaseHref();
        if ('prod' === $this->getEnvironment()) {
            $angularBuildCommandLine .= ' -prod -aot';
        }
        $angularBuildProcess = new Process($angularBuildCommandLine);
        $angularBuildProcess->setTimeout(300);
        $angularBuildProcess->setWorkingDirectory($this->getIntegrationService()->getAngularDirectory());
        $output->writeln('Executing: ' . $angularBuildProcess->getCommandLine());
        $angularBuildProcess->mustRun();
    }
}
