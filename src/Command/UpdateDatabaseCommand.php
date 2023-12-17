<?php

namespace App\Command;

use App\Connection\DoctrineMultidatabaseConnection;
use App\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 *
 */
#[AsCommand(
    name: 'app:database:update',
    description: 'Updates an existing database.',
    aliases: ['app:update-database'],
    hidden: false
)]
class UpdateDatabaseCommand extends Command {
    /**
     * @param ManagerRegistry $doctrine
     * @param KernelInterface $kernel
     */
    public function __construct(private ManagerRegistry $doctrine, private KernelInterface $kernel) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void {
        $this
            ->addArgument('databaseName', InputArgument::OPTIONAL, 'The name of the database')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Update all databases');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        if ($input->getOption('all')) {
            $output->writeln('Updating all databases');
            $dbs = $this->doctrine->getConnection()->getDatabases();
            foreach ($dbs as $db) {
                $kernel = new Kernel(
                    $this->kernel->getEnvironment(),
                    $this->kernel->isDebug()
                );
                $application = new Application($kernel);
                $application->setAutoExit(false);

                $arguments = [
                    'command'      => 'app:database:update',
                    'databaseName' => $db
                ];

                $greetInput = new ArrayInput($arguments);
                $application->run($greetInput, $output);
            }
        } else {
            if ($input->getArgument('databaseName')) {
                $output->writeln('Updating ' . $input->getArgument('databaseName') . ' database');
                $this->updateSingleDatabase($input->getArgument('databaseName'), $output);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @param string $databaseName
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    private function updateSingleDatabase(string $databaseName, OutputInterface $output) {
        $this->doctrine->getConnection()->changeDatabase($databaseName);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $arguments = [
            'command'              => 'doctrine:migrations:migrate',
            '--no-interaction'     => '',
            '--no-debug'           => '',
            '--allow-no-migration' => ''
        ];

        $commandInput = new ArrayInput($arguments);
        $commandInput->setInteractive(false);
        $application->run($commandInput, $output);
    }
}
