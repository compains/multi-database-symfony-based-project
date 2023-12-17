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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 *
 */
#[AsCommand(
    name: 'app:database:create',
    description: 'Creates a new database',
    aliases: ['app:create-database'],
    hidden: false
)]
class CreateDatabaseCommand extends Command {


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
            ->addArgument('databaseName', InputArgument::REQUIRED, 'The name of the database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $dbName = $input->getArgument('databaseName');
        if (strpos($dbName, 'app_') === false) {
            $dbName = 'app_' . $dbName;
        }

        /** @var DoctrineMultidatabaseConnection $doctrineConnection */
        $doctrineConnection = $this->doctrine->getConnection();
        $doctrineConnection->changeDatabase($dbName);

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $arguments = [
            'command'          => 'doctrine:database:create',
            '--if-not-exists'  => null,
            '--no-interaction' => null
        ];

        $commandInput = new ArrayInput($arguments);
        $application->run($commandInput, $output);
        unset($application);
        unset($kernel);

        return Command::SUCCESS;
    }

}
