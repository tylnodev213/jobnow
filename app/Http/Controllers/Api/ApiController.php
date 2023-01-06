<?php

namespace App\Http\Controllers\Api;

use Core\Http\Controllers\BaseController;

class ApiController extends BaseController
{
    protected $code = 200;
    protected $status = true;
    protected $message;
    protected $data = [];
    protected $header = [];
    protected $options = 0;
    protected $response = [];
    protected $validator;
    protected $params = [];

    public function __construct()
    {
        parent::__construct();
        disableDebugBar();
    }

    /**
     * @param $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    protected function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $message
     */
    protected function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array|mixed
     */
    protected function getMessage()
    {
        if (!empty($this->message)) {
            return $this->message;
        }

        $msg = __('messages.http_code');
        return data_get($msg, $this->code);
    }

    /**
     * @param $data
     */
    protected function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * @param $code
     */
    protected function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    protected function getCode()
    {
        return $this->code;
    }

    /**
     * @param $response
     */
    protected function appendResponse($response)
    {
        $this->response = array_merge($this->response, $response);
    }

    /**
     * @param $params
     */
    protected function appendParams($params)
    {
        $this->params = array_merge($this->params, (array)$params);
    }

    /**
     * @param string $key
     * @return array|mixed
     */
    protected function getParams(string $key = '')
    {
        $data = array_merge(request()->all(), (array)$this->params);

        if (empty($key)) {
            return $data;
        }

        return data_get($data, $key, null);
    }

    /**
     * @param $data
     * @return false|string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function response()
    {
        $response = [
            'status' => $this->getStatus(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
        ];

        $response = array_merge($response, $this->response);
        return $this->renderJson($response, $this->getCode());
    }
}
