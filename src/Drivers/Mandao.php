<?php

namespace Ethanzway\Sms\Drivers;

use Exception;
use GuzzleHttp\Client as HttpClient;
use ethanzway\sms\Support\Log;

class Mandao
{
    protected $parameters = [
        'url' => '',
        'port' => '',
        'sn' => '',
        'secret' => '',
    ];
    
    /**
     *
     * Send the message to the mobile.
     *
     * @param  string   $mobile
     * @param  string  $content
     * @return bool
     */
    public function send($mobile, $content)
    {
        $argv = array(
            'sn' => $this->getSn(),
            'pwd' => strtoupper(md5($this->getSn().$this->getSecret())),
            'mobile' => $mobile ?? '',
            'content' => $content ?? '',
            'ext' => '',
            'stime' => '',
            'rrid' => '',
        );
        try {
            $result = (new HttpClient())->request('POST', $this->getUrl(), ['form_params' => $argv])->getBody()->getContents();
            preg_match('/<string xmlns="http:\/\/tempuri.org\/">(.*)<\/string>/', $result, $matches);
            if ($matches[1] > 1) {
                Log::info('Success', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => iconv('gb2312', 'utf-8//IGNORE', $content)));
            } else {
                Log::warning('Failed', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => iconv('gb2312', 'utf-8//IGNORE', $content)));
            }
        } catch (Exception $e) {
            Log::error('Error', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => iconv('gb2312', 'utf-8//IGNORE', $content)));
        }
    }

    /**
     * @param  string $key
     * @return mixed
     */
    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    /**
     * @param  string $key
     * @param  mixed  $value
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }
    
    /**
     * @param $value
     *
     * @return $this
     */
    public function setUrl($value)
    {
        return $this->setParameter('url', $value);
    }
    
    /**
     *
     * @return mixed
     */
    public function getUrl()
    {
        return $this->getParameter('url');
    }
    
    /**
     * @param $value
     *
     * @return $this
     */
    public function setPort($value)
    {
        return $this->setParameter('port', $value);
    }
    
    /**
     *
     * @return mixed
     */
    public function getPort()
    {
        return $this->getParameter('port');
    }
    
    /**
     * @param $value
     *
     * @return $this
     */
    public function setSn($value)
    {
        return $this->setParameter('sn', $value);
    }
    
    /**
     *
     * @return mixed
     */
    public function getSn()
    {
        return $this->getParameter('sn');
    }
    
    /**
     * @param $value
     *
     * @return $this
     */
    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }
    
    /**
     *
     * @return mixed
     */
    public function getSecret()
    {
        return $this->getParameter('secret');
    }
}
