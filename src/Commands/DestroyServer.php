<?php
declare(strict_types=1);

namespace D4rk0s\Vultr\Commands;

use D4rk0s\Vultr\Services\VultrService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vultr\VultrPhp\Services\Instances\Instance;
use Vultr\VultrPhp\VultrClient;

class DestroyServer extends Command
{
    protected static $defaultName = "DestroyServer";
    private ?Instance $currentInstance;
    private VultrClient $vultrClient;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->vultrClient = VultrService::getVultrClient();
        $this->currentInstance = VultrService::getCurrentInstance();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln("");
        try {
            if ($this->currentInstance) {
                $this->manageSnapshot($output);
                $this->deleteServer($output);
            } else {
                $output->writeln("No instance to destroy. Program terminated");
            }
            $output->writeln("\nProgram terminated");
        } catch (\Exception $exception) {
            $output->writeln("\nProgram terminated");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function manageSnapshot(OutputInterface $output) : void
    {
        // Ancien snapshot
        $oldSnapshot = VultrService::getCurrentSnapshot();

        // On crée notre snapshot
        $output->write("Snapshot creation");
        $snapshot = VultrService::createSnapshot();
        if($snapshot === null) {
            $output->write("Unable to create snapshot. Aborting.");
            throw new \Exception("INVALID_SNAPSHOT");
        }

        while($this->vultrClient->snapshots->getSnapshot($snapshot->getId())->getStatus() === "pending") {
            sleep(30);
            $output->write('.');
        }
        $output->writeln(' OK');

        // On efface l'ancien snapshot
        if($oldSnapshot) {
            $output->write("Deleting previous snapshot...");
            $this->vultrClient->snapshots->deleteSnapshot($oldSnapshot->getId());
            $output->writeln("OK");
        }
    }

    private function deleteServer(OutputInterface $output) : void
    {
        $output->write('Destroy instance...');

        // Supprime le serveur
        $this->vultrClient->instances->deleteInstance($this->currentInstance->getId());
        $output->writeln("OK");
    }
}