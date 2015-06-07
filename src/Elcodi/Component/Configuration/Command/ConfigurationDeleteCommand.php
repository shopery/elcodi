<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2015 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Component\Configuration\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Elcodi\Component\Configuration\Services\ConfigurationManager;

/**
 * Class ConfigurationDeleteCommand
 */
class ConfigurationDeleteCommand extends Command
{
    /**
     * @var ConfigurationManager
     *
     * Configuration manager
     */
    private $configurationManager;

    /**
     * Constructor
     *
     * @param ConfigurationManager $configurationManager Configuration manager
     */
    public function __construct(ConfigurationManager $configurationManager)
    {
        parent::__construct();

        $this->configurationManager = $configurationManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('elcodi:configuration:delete')
            ->setDescription('Deletes a specific configuration value')
            ->addArgument(
                'identifier',
                InputArgument::REQUIRED,
                'Configuration identifier'
            );
    }

    /**
     * This command saves a configuration value
     *
     * @param InputInterface  $input  The input interface
     * @param OutputInterface $output The output interface
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurationIdentifier = $input->getArgument('identifier');

        $this
            ->configurationManager
            ->delete(
                $configurationIdentifier
            );

        $formatter = $this->getHelper('formatter');
        $formattedLine = $formatter->formatSection(
            'OK',
            'Deleted configuration "' . $configurationIdentifier . '"'
        );

        $output->writeln($formattedLine);
    }
}
