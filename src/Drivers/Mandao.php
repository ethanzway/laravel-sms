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

    public function getParameter($key)
    {
        return $this->parameters[$key];
    }

    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function setUrl($value)
    {
        return $this->setParameter('url', $value);
    }

    public function getUrl()
    {
        return $this->getParameter('url');
    }

    public function setPort($value)
    {
        return $this->setParameter('port', $value);
    }

    public function getPort()
    {
        return $this->getParameter('port');
    }

    public function setSn($value)
    {
        return $this->setParameter('sn', $value);
    }

    public function getSn()
    {
        return $this->getParameter('sn');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }
}
