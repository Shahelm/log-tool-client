<?php
namespace Commands;

use Lib\TempStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('log-tool:stop')
            ->setDescription('Command stops the log-tool-client.')
            ->setDefinition(array())
            ->setHelp(<<<EOT
Command stops the log-tool-client.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pid = TempStorage::getInstance()->getPid();
        
        TempStorage::getInstance()->deleteTempFile();
        
        exec($this->getCommand($pid));
    }
    
    private function getCommand($pid)
    {
        $pid = (int)$pid;
        
        return 'kill ' . $pid;
    }
}