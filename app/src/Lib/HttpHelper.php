<?php
namespace Lib;

use Guzzle\Http\Client;
use Symfony\Component\Console\Helper\Helper;

class HttpHelper extends Helper 
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new Client();
        }
        
        return $this->httpClient; 
    }
    
    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'httpClient';
    }
} 