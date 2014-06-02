<?php
namespace Commands;

use Lib\Storage\Storage;
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
        $this->setName('stop')
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
        /**
         * @var Storage $storage
         */
        $storage = $this->getHelper('storageHelper')->getStorage();
        
        $storage->flushAll();
    }    
}