<?php

namespace D4rk0s\Vultr\Commands;

use D4rk0s\Vultr\Services\VultrService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Vultr\VultrPhp\Services\Instances\Instance;
use Vultr\VultrPhp\VultrClient;

class CreateInstance extends Command
{
    protected static $defaultName = "CreateInstance";
    private VultrClient $vultrClient;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->vultrClient = VultrService::getVultrClient();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $instance = $this->createInstance($output);

        $output->writeln("New instance ip address : ".$instance->getMainIp());
        $output->writeln("");
        $output->writeln("Program terminated");

       return Command::SUCCESS;
    }

    private function createInstance(OutputInterface $output) : Instance
    {
        $output->writeln("");
        $output->write("Creating new instance");
        $instance = VultrService::createInstance();

        while($this->vultrClient->instances->getInstance($instance->getId())->getStatus() !== "active" ||
          $this->vultrClient->instances->getInstance($instance->getId())->getPowerStatus() === "stopped") {
            $output->write(".");
            sleep(30);
        }

        $output->writeln(" OK");

        return $this->vultrClient->instances->getInstance($instance->getId());
    }
}