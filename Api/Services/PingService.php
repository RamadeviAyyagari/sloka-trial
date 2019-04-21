<?php
namespace\Api\Services;

class PingService extends \Framework\Service
{

    public function __construct ()
    {
        parent::__construct();
    }

    public function getIndexTask ()
    {
        $data = [
            'ping' => 'ok',
            'time' => date('Y-m-d H:i:s'),
        ];

        $response = new \Framework\Response();
        $response->setHttpStatusCode('200', 'OK');
        $response->setData($data);
        $response->dispatchJson();
    }
}
