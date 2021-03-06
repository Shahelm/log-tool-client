<?php
namespace Commands;

use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Lib\Config;
use Lib\Notifier;
use Lib\OSHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class StartCommand
 *
 * @package Commands
 */
class StartCommand extends Command
{
    /**
     * Storage script errors.
     *
     * @var array
     */
    private $errorMessages = array();

    /**
     * @var int
     */
    private $timeOut;
    
    /**
     * @var int
     */
    private $numberOfErrors;

    /**
     * @var int
     */
    private $lifeTimePopup;
    
    /**
     * @var int
     */
    private $pid;
    
    /**
     * @var bool
     */
    private $debugMode;

    /**
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('start')
            ->setDescription('The command to start the client.')
            ->setDefinition(array(
                new InputOption(
                    'time-out',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    $this->getConfig()->get('min-time-out')
                    . 'The time interval (in sec) through which checks errors. (minimum values: ' . ')',
                    $defaultTimeOut = $this->getConfig()->get('time-out')
                ),
                new InputOption(
                    'number-of-errors',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'The number of errors that call alert.',
                    $defaultNumberOfErrors = $this->getConfig()->get('number-of-errors')
                ),
                new InputOption(
                    'lifetime-popup',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Specifies the time in milliseconds notice will hang on the screen. (min values: '
                    . $this->getConfig()->get('lifetime-popup-min') .', max values: '
                    . $this->getConfig()->get('lifetime-popup-max') . ')',
                    $lifetimePopup = $this->getConfig()->get('lifetime-popup')
                )
                ,
                new InputOption(
                    'debug',
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Enable debug mode.',
                    false
                )
            ))
            ->setHelp(<<<EOT
The command to start the client.
EOT
            );
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return Config::getInstance();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input);
        
        if (!$this->isPossibleRunClient()) {
            $this->outputErrorMessage($output);
        }
              
        $notifier = new Notifier($this->lifeTimePopup);

        /**
         * @var ConsoleOutputInterface $output
         */
        $errorOutput = $output->getErrorOutput();

        while (true) {
            if (false === $this->getStorage()->get('isEnabled')) {
                if ($this->isDebug()) {
                    $output->writeln('<info>Successful stop the process!</info>');
                }
                break;
            }
            
            try {
                $numberOfErrorsForLastMinutes = $this->getErrorsForLastMinutes();
                $numberOfErrorsForFiveMinutes = $this->getErrorsForLastFiveMinutes();

                if ($this->isDebug()) {
                    $output->writeln("<info>Minute: {$numberOfErrorsForLastMinutes}</info>");
                    $output->writeln("<info>Five minute: {$numberOfErrorsForFiveMinutes}</info>");
                }

                if ($numberOfErrorsForLastMinutes >= $this->numberOfErrors) {
                    $notifier->notify(
                        $numberOfErrorsForLastMinutes,
                        $numberOfErrorsForFiveMinutes,
                        $this->getLastErrorTime()
                    );
                }
            } catch (ServerErrorResponseException $e) {
                $this->showServicesError($notifier, $errorOutput, $e);
            } catch (CurlException $e) {
                $this->showServicesError($notifier, $errorOutput, $e);
            } catch (\Exception $e) {
                echo get_class($e), "\n";
                $errorOutput->writeln("Fatal error: {$e->getMessage()}");
                $this->getStorage()->flushAll();
                break;
            }
            
            sleep($this->timeOut);
        }
    }
    
    /**
     * @param $input
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function init(InputInterface $input)
    {
        $this->timeOut = (int)$input->getOption('time-out');

        $this->numberOfErrors = (int)$input->getOption('number-of-errors');

        $this->lifeTimePopup = (int)$input->getOption('lifetime-popup');

        $this->debugMode = (bool)$input->getOption('debug');
        
        $osHelper = new OSHelper();

        $this->pid = $osHelper->getProcessId();

        $this->getStorage()->flushAll();

        $isSaved = $this->getStorage()->save('isEnabled', true);
        
        if (false === $isSaved) {
            throw new \RuntimeException('Failed to save the configuration.(isEnabled)');
        }

        $isSaved = $this->getStorage()->save('pid', $this->pid);

        if (false === $isSaved) {
            throw new \RuntimeException('Failed to save the configuration.(isEnabled)');
        }
    }
    
    /**
     * @return \Lib\Storage\Storage
     *
     * @throws \InvalidArgumentException
     */
    private function getStorage()
    {
        return $this->getHelper('storageHelper')->getStorage();
    }

    /**
     * The function returns true if all the conditions for starting are fulfilled.
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function isPossibleRunClient()
    {
        $return = true;
        
        if (!$this->validateInput() || !$this->checkCurl() || !$this->checkStorage() || !$this->checkProcessId()) {
            $return = false;
        }

        return $return;
    }

    /**
     * This function checks input data.
     *     time-out > self::MIN_TIME_OUT
     *     number-of-errors > 0
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function validateInput()
    {
        $return = true;
        
        if ($this->timeOut < $this->getConfig()->get('min-time-out')) {
            $this->errorMessages[] = '<error>Error: the time-out must be greater than ' .
                                     $this->getConfig()->get('min-time-out') . '.</error>';
            $return = false;
        }
        
        if ($this->numberOfErrors <= 0) {
            $this->errorMessages[] = '<error>Error: the number-of-errors must be greater than 0.</error>';
            $return = false;
        }
        
        $lifetimePopupMin = $this->getConfig()->get('lifetime-popup-min');
        $lifetimePopupMax = $this->getConfig()->get('lifetime-popup-max');
        
        if ($this->lifeTimePopup > $lifetimePopupMax || $this->lifeTimePopup < $lifetimePopupMin) {
            $this->errorMessages[] = '<error>Error: the lifetime-popup must be greater than '
                . $lifetimePopupMin . ' and less ' . $lifetimePopupMax . '.</error>';
            $return = false;
        }
                
        return $return;
    }

    /**
     * The function checks to see whether Curl.
     *
     * @return bool
     */
    private function checkCurl()
    {
        $return = true;
        
        if (!extension_loaded('curl')) {
            $this->errorMessages[] = '<error>Error: the PHP cURL extension must be installed!</error>';
            $return = false;
        }
        
        return $return;
    }

    /**
     * The function checks the availability of the temporary directory.
     *
     * @return bool
     */
    private function checkStorage()
    {
        $return = true;
        
        try {
            $this->getStorage();
        } catch (\InvalidArgumentException $e) {
            $this->errorMessages[] = '<error>Error: ' . $e->getMessage() . '</error>';
            $return = false;
        }
        
        return $return;
    }
    
    /**
     * The function checks whether it was possible to get the process id.
     *
     * @return bool
     */
    private function checkProcessId()
    {
        $return = true;
        
        if ($this->pid <= 0) {
            $this->errorMessages[] = '<error>Error: Failed to get the process Id.</error>';
            $return = false;
        }
        
        return $return;
    }

    /**
     * Function displays error messages and terminate the script.
     *
     * @param OutputInterface $output
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    private function outputErrorMessage(OutputInterface $output)
    {
        foreach ($this->errorMessages as $message) {
            $output->writeln($message);
        }

        die(0);
    }

    /**
     * @return bool
     */
    private function isDebug()
    {
        return $this->debugMode;
    }

    /**
     * The function returns the number of errors in the last minute.
     *
     * @return int|bool(false)
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    public function getErrorsForLastMinutes()
    {
        return $this->getNumberOfErrors('PT1M');
    }
    
    /**
     * The function returns the number of errors for the time interval.
     *
     * @param string $interval (PT1M | PT5M)
     *
     * @return int|bool(false)
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    private function getNumberOfErrors($interval)
    {
        $timeIntervalPattern = '{timeInterval}';
        
        $url = str_replace($timeIntervalPattern, $interval, $this->getApiUrl('number-of-errors'));

        $response = $this->getHttpClient()->get($url);

        $response->send();

        $res = $response->getResponse();

        $return = false;

        if ($res->isSuccessful()) {
            $return = json_decode($res->getBody(true))->count;
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
     * The function returns the number of errors in the last five minute.
     *
     * @return int|bool(false)
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    public function getErrorsForLastFiveMinutes()
    {
        return $this->getNumberOfErrors('PT5M');
    }

    /**
     * The function returns time of last error.
     *
     * @return int|bool(false)
     *
     * @throws RequestException
     * @throws \InvalidArgumentException
     */
    private function getLastErrorTime()
    {
        $url = $this->getApiUrl('get-max-time');
        
        $response = $this->getHttpClient()->get($url);

        $response->send();

        $res = $response->getResponse();
        
        $return = false;
        
        if ($res->isSuccessful()) {
            $return = json_decode($res->getBody(true))->sec;
        }
        
        return $return;
    }

    /**
     * @param Notifier $notifier
     * @param OutputInterface $errorOutput
     * @param \Exception $e
     *
     * @throws \InvalidArgumentException
     */
    protected function showServicesError($notifier, $errorOutput, $e)
    {
        $notifier->notifyServicesUnavailable();
        $errorOutput->writeln($e->getMessage());
    }
}
