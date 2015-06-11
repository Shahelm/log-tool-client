<?php
namespace Commands;

use Lib\Storage\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StopCommand
 *
 * @package Commands
 */
class StopCommand extends Command
{
    /**
     * @throws \InvalidArgumentException
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
        
        $isFlush = $storage->flushAll();
        
        if ($isFlush) {
            $output->writeln('<info>Stop successful</info>');
        } else {
            $output->writeln('<error>Failed to stop. Perhaps the service is already stopped.</error>');
        }
    }
}
