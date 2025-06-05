<?php

namespace MyBuilder\Bundle\CronosBundle\Command;

use MyBuilder\Bundle\CronosBundle\Exporter\AnnotationCronExporter;
use MyBuilder\Cronos\Formatter\Cron;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CommandBase extends Command
{
    public function __construct(private ContainerInterface $container)
    {
        parent::__construct();
    }

    protected function addServerOption(): void
    {
        $this
            ->addOption('server', null, InputOption::VALUE_REQUIRED, 'Only include cron jobs for the specified server', AnnotationCronExporter::ALL_SERVERS);
    }

    protected function configureCronExport(InputInterface $input, OutputInterface $output): Cron
    {
        $options = [
            'serverName' => $input->getOption('server'),
            'environment' => $input->getOption('env'),
        ];

        $output->writeln(sprintf('Server <comment>%s</comment>', $options['serverName']));
        $cron = $this->exportCron($options);
        $output->writeln(sprintf('<comment>Found %d lines</comment>', $cron->countLines()));

        return $cron;
    }

    private function exportCron($options): Cron
    {
        $commands = $this->getApplication()->all();

        /** @var AnnotationCronExporter $exporter */
        $exporter = $this->container->get('mybuilder.cronos_bundle.annotation_cron_exporter');

        return $exporter->export($commands, $options);
    }
}