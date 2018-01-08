<?php

namespace Dontdrinkandroot\AngularIntegrationBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Philip Washington Sorst <philip@sorst.net>
 */
class AngularGenerateComponentCommand extends AbstractAngularIntegrationCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ddr:angular:generate:component')
            ->addArgument('module', InputArgument::REQUIRED)
            ->addArgument('name', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $module = $input->getArgument('module');
        $name = $input->getArgument('name');

        $tsFilename = $name . '.component.ts';
        $htmlFilename = $name . '.component.html';
        $className = $this->dashesToCamelCase($name). 'Component';

        $content = $this->getTwig()->render(
            'DdrAngularIntegrationBundle::component.ts.twig',
            [
                'htmlFilename' => $htmlFilename,
                'className' => $className
            ]
        );

        $directory = $this->getIntegrationService()->getAngularDirectory() . '/src/' . $module . '/';
        if (!is_dir($directory)) {
            mkdir($directory);
        }

        file_put_contents($directory .$tsFilename, $content);
        file_put_contents($directory .$htmlFilename, '');
    }

    protected function dashesToCamelCase($string, $capitalizeFirstCharacter = true)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }
}
