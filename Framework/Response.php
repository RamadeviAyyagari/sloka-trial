<?php

namespace Framework;

class Response
{

    /** @var array */
    public $errorMessages;

    public function __construct()
    {
        $this->errorMessages = [];
    }

    /*
     * REF: https://www.restapitutorial.com/httpstatuscodes.html
     */

    public function setHttpStatusCode($httpStatusCode = 200, $httpStatusMessage = 'OK')
    {
        $this->httpStatusCode    = $httpStatusCode;
        $this->httpStatusMessage = $httpStatusMessage;
    }

    public function setErrorMessage($errorCode, $errorMessage)
    {
        $this->errorMessages[] = [
            'code'    => $errorCode,
            'message' => $errorMessage,
        ];
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function dispatchJson()
    {
        if (empty($this->contentType) === true) {
            $this->setContentType('application/json');
        }

        if (empty($this->httpStatusCode) === true) {
            $this->setHttpStatusCode();
        }

        $finalContent = [
            'status' => $this->httpStatusCode,
        ];

        if (is_array($this->errorMessages) === true && count($this->errorMessages) > 0) {
            $finalContent['errors'] = $this->errorMessages;
        } else {
            $finalContent['data'] = $this->data;
        }

        header('HTTP/1.1 ' . $this->httpStatusCode . ' ' . $this->httpStatusMessage . '');
        header('Content-Type: ' . $this->contentType);
        echo json_encode($finalContent);
        exit;
    }

    /**
     * Redirect to chosen url.
     *
     * @param string $url
     *            the url to redirect to
     * @param boolean $fullpath
     *            if true use only url in redirect instead of using DIR
     */
    public static function redirect($url = null, $fullpath = false)
    {
        if ($fullpath == false) {
            $url = Box::$application['url'] . '/' . $url;
        }

        header('Location: ' . $url);
        exit();
    }

}
