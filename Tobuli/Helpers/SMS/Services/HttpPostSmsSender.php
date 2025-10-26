<?php

namespace Tobuli\Helpers\SMS\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tobuli\Exceptions\ValidationException;

class HttpPostSmsSender extends AbstractHttpSmsSender
{
    public function sendThroughConsole($base_url, $query_url)
    {
        $command = 'curl -i ';

        if ($this->authentication)
            $command .= '--user ' . $this->username . ':' . $this->password . ' ';

        $command .= $this->commandLineHeaders();

        if ($this->encoding === 'query') {
            $command .= '-d "" -X POST "' . $base_url . '?' . $query_url . '" ';
        } else {
            $command .= '-d \'' . $this->getData($query_url) . '\' ' . $base_url . ' ';
        }

        $command .= '> /dev/null 2>&1 &';

        @exec($command);
    }

    public function sendThroughCurlPHP($base_url, $query_url)
    {
        try {
            $http = Http::timeout(config('sms.curl_timeout'));

            if ($this->authentication) {
                $http = $http->withBasicAuth($this->username, $this->password);
            }

            switch ($this->encoding) {
                case 'query':
                    $url = $base_url . '?' . $query_url;

                    $http = $http->withHeaders(array_merge($this->getHeaders(), [
                        'Content-Length' => 0,
                    ]));

                    $response = $http->post($url);

                    break;
                case 'json':
                    $data = $this->getData($query_url);
                    $url = $base_url;

                    $http = $http->withHeaders(array_merge($this->getHeaders(), [
                        'Content-Type' => 'application/json',
                        'Content-Length' => strlen($data),
                    ]));

                    $response = $http->withBody($data, 'application/json')->post($url);
                    break;
                default:
                    $data = $this->getData($query_url);
                    $url = $base_url;

                    $http = $http->withHeaders(array_merge($this->getHeaders(), [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Content-Length' => strlen($data),
                    ]));

                    $response = $http->withBody($data, 'application/x-www-form-urlencoded')->post($url);
            }

            $response->throw();

            return $response->body();
        } catch (RequestException $e) {
            throw new ValidationException([
                'curl_request' => trans('validation.attributes.bad_sms_gateway_url') . ': ' . $e->getMessage(),
            ]);
        }
    }

    protected function getData($query)
    {
        if ($this->encoding === 'json')
            return $this->queryToJson($query);

        return $query;
    }

    protected function queryToJson($query)
    {
        parse_str($query, $params);

        return json_encode($params);
    }
}