<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\DependencyInjection;

use Symfony\Component\Console\Command\Command;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularPrepareCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('ddr:angular:prepare');
    }
}
