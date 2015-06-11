<?php
namespace Commands;

use Guzzle\Http\Exception\RequestException;
use Lib\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelfUpdateCommand
 *
 * @package Commands
 */
class SelfUpdateCommand extends Command
{
    /**
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Command updates the client to the latest version.')
            ->setDefinition(array())
            ->setHelp(<<<EOT
Command updates the client to the latest version.
EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws \InvalidArgumentException
     * @throws RequestException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currentVersion = $this->getClientCurrentVersion();
        
        $version = (float)$this->getClientLatestVersion();
                
        if ($currentVersion === $version) {
            $this->outputMessage($output, '<info>You have the latest version of the log-tool client!</info>');
        } elseif ($currentVersion > $version) {
            $this->outputMessage(
                $output,
                '<error>Sorry but the implementation of rollback has not yet implemented.</error>'
            );
        }
        
        $fileName = 'log-tool-client.phar';
        
        $return = false;
        
        try {
            $return = $this->saveNewVersionClient($fileName, $this->getNewVersionClient());
        } catch (\Exception $e) {
            $output->writeln('<error>Update the client failed.</error>');
            $this->outputMessage($output, '<error> ' . $e->getMessage() . '</error>');
        }
        
        if ($return) {
            $this->outputMessage($output, '<info>Update was successful!</info>');
        } else {
            $this->outputMessage($output, '<error>Failed to save a new version of the client!</error>');
        }
    }

    /**
     * Function saves a new version of log tool client with the name log-tool-client.phar.
     *
     * @param string $fileName
     * @param string $fileData
     *
     * @return int
     */
    private function saveNewVersionClient($fileName, $fileData)
    {
        return file_put_contents($fileName, $fileData);
    }

    /**
     * The function returns a new version log tool client.
     *
     * @return string
     *
     * @throws RequestException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    private function getNewVersionClient()
    {
        $request = $this->getHttpClient()->post($this->getApiUrl('last-phar-client'));

        $response = $request->send();

        $phar = $response->getBody(true);
        
        if ($phar === '"false"') {
            throw (new \RuntimeException('Unable to get a new version of the client.'));
        }
        
        return $phar;
    }

    /**
     * The function returns the number of the latest version of the client.
     *
     * @return float
     * @throws \InvalidArgumentException
     */
    private function getClientCurrentVersion()
    {
        return (float)Config::getInstance()->get('version');
    }
    
    /**
     * The function gets the latest version of the client.
     *
     * @return bool
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    private function getClientLatestVersion()
    {
        $url = $this->getApiUrl('client-latest-version');

        $request = $this->getHttpClient()->get($url);

        $request->send();

        $response = $request->getResponse();

        $return = false;

        if ($response->isSuccessful()) {
            $return = json_decode($response->getBody(true))->version;
        }
        
        return $return;
    }
    
    /**
     * The function returns the url by api url-name.
     *
     * @param string $urlName
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    private function getApiUrl($urlName)
    {
        $host = $this->getConfig()->get('host');

        $apiUrl = $this->getConfig()->get("api-routes.{$urlName}");

        $url = $host . $apiUrl;

        return $url;
    }

    /**
     * @return \Guzzle\Http\Client
     *
     * @throws \InvalidArgumentException
     */
    private function getHttpClient()
    {
        return $this->getHelper('httpClient')->getHttpClient();
    }

    /**
     * Function displays messages and terminate the script.
     *
     * @param OutputInterface $output
     *
     * @param string $message
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function outputMessage(OutputInterface $output, $message)
    {
        $output->writeln($message);

        die(0);
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return Config::getInstance();
    }
}
