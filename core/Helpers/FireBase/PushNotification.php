<?php

namespace Core\Helpers\FireBase;

use GuzzleHttp\Client;

class PushNotification
{
    protected array $headers = [];

    public function __construct()
    {
        $this->headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'Authorization' => 'key=' . getConfig('fire_base.server_key')
        ];
    }

    /**
     * get info by token
     *
     * @param $token
     * @return false|mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInfo($token)
    {
        if (empty($token)) {
            return false;
        }

        $url = getConfig('fire_base.url_get_info') . $token . '?details=true';

        return $this->_callApi($url);
    }

    /**
     * @param $token
     * @param $topic
     * @return false|mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribeTopic($token, $topic)
    {
        if (empty($token) || empty($topic)) {
            return false;
        }

        $params = [
            'to' => '/topics/' . $topic,
            'registration_tokens' => (array)$token,
        ];

        // log here ...

        return $this->_callApi(getConfig('fire_base.url_add_topic'), $params);
    }

    /**
     * @param $token
     * @param $topic
     * @return false|mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unsubscribeTopic($token, $topic)
    {
        if (empty($token) || empty($topic)) {
            return false;
        }

        $params = [
            'to' => '/topics/' . $topic,
            'registration_tokens' => (array)$token,
        ];

        // log here ...

        return $this->_callApi(getConfig('fire_base.url_remove_topic'), $params);
    }

    /**
     * @param $title
     * @param $message
     * @param $tokens
     * @return array|false
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendNotificationToDevices($title, $message, $tokens)
    {
        if (empty($tokens)) {
            return false;
        }

        $tokens = array_chunk((array)$tokens, getConfig('fire_base.limit_tokens'));
        $sound = getConfig('fire_base.sound');
        $url = getConfig('fire_base.url_send');
        $results = [];

        foreach ($tokens as $token) {
            $params = [
                'registration_ids' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $message,
                ],
            ];

            if (!empty($sound)) {
                $params['sound'] = $sound;
            }

            $logs = json_encode(['url' => $url, 'params' => $params, 'method' => 'POST', 'headers' => $this->headers], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $response = $this->_callApi($url, $params);

            // fails
            if (!$response) {
                $results[] = ['status' => false, 'message_id' => '', 'token' => $token];
                continue;
            }

            // fails
            if (!isset($response['result'])) {
                $results[] = ['status' => false, 'message_id' => '', 'token' => $token];
                $msg = str(__('messages.push_messages_error'))->append(
                    PHP_EOL,
                    'Request: ' . $logs,
                    PHP_EOL,
                    'Response: ' . json_encode((array)$response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                );
                logError($msg);
                continue;
            }

            // errors
            if (!empty($response['results'][0]['error'])) {
                $results[] = ['status' => false, 'message_id' => '', 'token' => $token];
                $msg = str(__('messages.push_messages_error'))->append(
                    ' (msg: ' . $response['results'][0]['error'] . ')',
                    PHP_EOL,
                    'Request: ' . $logs,
                    PHP_EOL,
                    'Response: ' . json_encode((array)$response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                );
                logError($msg);
                continue;
            }

            // success
            if (!empty($response['results'][0]['message_id'])) {
                $results[] = ['status' => true, 'message_id' => $response['results'][0]['message_id'], 'token' => $token];
                $msg = str(__('messages.push_messages_success'))->append(
                    PHP_EOL,
                    'Request: ' . $logs,
                    PHP_EOL,
                    'Response: ' . json_encode((array)$response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                );
                logInfo($msg);
            }
        }

        return $results;
    }

    /**
     * @param $title
     * @param $message
     * @param $topics
     * @return array|false
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendNotificationToTopic($title, $message, $topics)
    {
        if (empty($topics)) {
            return false;
        }

        $arrTopics = [];
        foreach ((array)$topics as $item) {
            $arrTopics[] = "'" . $item . "' in topics";
        }
        $strTopic = implode(' || ', $arrTopics);

        $sound = getConfig('fire_base.sound');
        $url = getConfig('fire_base.url_send');
        $result = ['status' => true, 'message_id' => '', 'topics' => (array)$topics];

        $params = [
            'notification' => [
                'title' => $title,
                'body' => $message,
            ],
            'condition' => $strTopic,
        ];
        if (!empty($sound)) {
            $params['notification']['sound'] = $sound;
        }

        $logs = json_encode(['url' => $url, 'params' => $params, 'method' => 'POST', 'headers' => $this->headers], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $response = $this->_callApi($url, $params);

        // fails
        if (!$response) {
            $result['status'] = false;
            return $result;
        }

        // errors
        if (!empty($response['error'])) {
            $msg = str(__('messages.push_messages_error'))->append(
                ' (msg: ' . $response['error'] . ')',
                PHP_EOL,
                'Request: ' . $logs,
                PHP_EOL,
                'Response: ' . json_encode((array)$response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            );
            logError($msg);
            $result['status'] = false;
            return $result;
        }

        // success
        if (!empty($response['message_id'])) {
            $msg = str(__('messages.push_messages_success'))->append(
                PHP_EOL,
                'Request: ' . $logs,
                PHP_EOL,
                'Response: ' . json_encode((array)$response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            );
            logInfo($msg);
            $result['message_id'] = $response['message_id'];
            return $result;
        }

        return $result;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @param array $headers
     * @return false|mixed|\Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function _callApi($url, array $params = [], string $method = 'POST')
    {
        $logs = json_encode(['url' => $url, 'params' => $params, 'method' => $method, 'headers' => $this->headers], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        try {

            $client = new Client();
            $call = $client->request($method, $url, [
                'headers' => $this->headers,
                'json' => $params,
                'timeout' => 300,
            ]);

            if ($call->getStatusCode() != 200) {
                logError(__('messages.curl_api_error') . PHP_EOL . $logs);
                return false;
            }

            $content = $call->getBody()->getContents();
            return is_json($content) ? json_decode($content, true) : $content;
        } catch (\Exception $exception) {
            logError($exception->getMessage() . PHP_EOL . $logs . PHP_EOL . $exception->getTraceAsString());
            return false;
        }
    }
}
