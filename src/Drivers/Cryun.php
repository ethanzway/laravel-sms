<?php

namespace Ethanzway\Sms\Drivers;

use Exception;
use GuzzleHttp\Client as HttpClient;
use ethanzway\sms\Support\Log;

class Cryun
{
    protected $parameters = [
        'accesskey' => '',
        'secret' => '',
        'templates' => [],
    ];
    
    public function send($mobile, $body)
    {
        $mobiles = $mobile;
        if (is_string($mobiles)) {
            $mobiles = [$mobiles];
        }
        $argv = $this->parse($body);
        foreach($mobiles as $mobile) {
            try {
                $result = (new HttpClient())
                  ->request(
                    'POST',
                    'http://api.1cloudsp.com/api/v2/single_send',
                    [
                      'form_params' => [
                        'accesskey' => $this->getAccesskey(),
                        'secret' => $this->getSecret(),
                        'sign' => $argv['sign'],
                        'templateId' => $argv['templateId'],
                        'mobile' => $mobile,
                        'content' => $argv['content'],
                      ]
                    ]
                  )
                  ->getBody()
                  ->getContents();
                $data = json_decode($result, true);
                if ($data['code'] == 0) {
                    Log::info('Success', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => $body));
                } else {
                    Log::warning('Failed', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => $body));
                }
            } catch (Exception $e) {
                Log::error('Error', array('mobile' => substr($mobile, 0, 3) . '****' . substr($mobile, -4), 'content' => $body));
            }
        }
    }

    protected function parse($body)
    {
        if (preg_match('/【.*】/', $body, $match)) {
            $sign = $match[0];
            $body = str_replace($sign, '', $body);
            foreach ($this->getTemplates() as $template) {
                if (preg_match_all('/\{.*\}/U', $template['content'], $matches)) {
                    $regex = $template['content'];
                    foreach ($matches[0] as $match) {
                        $regex = str_replace($match, '(.*)', $regex);
                    }
                    if (preg_match_all('/' . $regex . '/', $body, $result)) {
                        $templateId = $template['id'];
                        $content = [];
                        for ($i = 1; $i < count($result); $i++) {
                            $content[] = $result[$i][0];
                        }
                        return ['sign' => $sign, 'templateId' => $templateId, 'content' => implode('##', $content)];
                    }
                }
            }
        }
        throw new \UnexpectedValueException('Driver [$name] is not defined.');
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

    public function setAccesskey($value)
    {
        return $this->setParameter('accesskey', $value);
    }

    public function getAccesskey()
    {
        return $this->getParameter('accesskey');
    }

    public function setSecret($value)
    {
        return $this->setParameter('secret', $value);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setTemplates($value)
    {
        return $this->setParameter('templates', $value);
    }

    public function getTemplates()
    {
        return $this->getParameter('templates');
    }
}
