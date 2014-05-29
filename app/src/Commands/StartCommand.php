<?php
namespace Commands;

use Lib\Config;
use Lib\Notifier;
use Lib\TempStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class StartCommand extends Command 
{
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
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('log-tool:start')
            ->setDescription('The command to start the client.')
            ->setDefinition(array(
                new InputOption(
                    'time-out', null, InputOption::VALUE_OPTIONAL,
                    'The time interval (in sec) through which checks errors. (minimum values: ' . $this->getConfig()->get('min-time-out') . ')', 
                    $defaultTimeOut = $this->getConfig()->get('time-out')
                ),
                new InputOption(
                    'number-of-errors', null, InputOption::VALUE_OPTIONAL,
                    'The number of errors that call alert.',
                    $defaultNumberOfErrors = $this->getConfig()->get('number-of-errors')
                ),
            ))
            ->setHelp(<<<EOT
The command to start the client.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->timeOut = (int)$input->getOption('time-out');

        $this->numberOfErrors = (int)$input->getOption('number-of-errors');

        if (!$this->validateInput() || !$this->checkCurl()) {
            $this->outputErrorMessage($output);
        }

        if (!TempStorage::getInstance()->isWritable()) {
            //TODO Добавь вывод ошибки в консоль + перенести выше в if
            die(0);
        }

        TempStorage::getInstance()->savePid();
       
        while (true) {
            $numberOfErrors = $this->getNumberOfErrors();
            
            if ($numberOfErrors >= $this->numberOfErrors) {
                Notifier::getInstance()->notify($numberOfErrors,  $this->getLastErrorTime());
            }
            
            sleep($this->timeOut);
        }
    }

    /**
     * Функция проверяет входящие данные: 
     *     time-out > self::MIN_TIME_OUT
     *     number-of-errors > 0
     * 
     * @return bool
     */
    private function validateInput()
    {       
        $return = true;
        
        if ($this->timeOut < $this->getConfig()->get('min-time-out')) {
            $this->errorMessages[] = '<error>Error: the time-out must be greater than ' . $this->getConfig()->get('min-time-out') . '.</error>';
            $return = false;
        }
        
        if ($this->numberOfErrors <= 0) {
            $this->errorMessages[] = '<error>Error: the number-of-errors must be greater than 0.</error>';
            $return = false;
        }
                
        return $return;
    }

    /**
     * Функция проверяет наличие расширения Curl.
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
     * Функция выводит сообщения об ошибках и завершает сценарий.
     * 
     * @param OutputInterface $output
     * 
     * @return bool
     */
    private function outputErrorMessage(OutputInterface $output)
    {
        foreach ($this->errorMessages as $message) {
            $output->writeln($message);
        }

        die(0);
    }

    /**
     * Функция возвращает время возникновения последней ошибки.
     * 
     * @return bool
     */
    private function getLastErrorTime()
    {
        $url = $this->getApiUrl('get-max-time');
        
        $response = $this->getHttpClient()->get($url);

        $response->send();

        $res = $response->getResponse();
        
        $return = false;
        
        if ($res->getStatusCode() == 200) {
            $return = json_decode($res->getBody(true))->sec;
        }
        
        return $return;
    }

    /**
     * Функци возвращае количество ошибок за последнию минуту.
     * 
     * @return bool
     */
    public function getNumberOfErrors()
    {
        $timeIntervalPattern = '{timeInterval}';
        
        $interval = 'PT1M';
        
        $url = str_replace($timeIntervalPattern, $interval, $this->getApiUrl('number-of-errors'));        

        $response = $this->getHttpClient()->get($url);

        $response->send();

        $res = $response->getResponse();

        $return = false;

        if ($res->getStatusCode() == 200) {
            $return = json_decode($res->getBody(true))->count;
        }
        
        return $return;
    }

    /**
     * Функция возвращает url по api url name.
     * 
     * @param string $urlName
     * 
     * @return string
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
     */
    private function getHttpClient()
    {
        return $this->getHelper('httpClient')->getHttpClient();
    }

    /**
     * @return Config
     */
    private function getConfig()
    {
        return Config::getInstance();
    }
}