<?php

namespace Tobuli\Helpers\SMS\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tobuli\Exceptions\ValidationException;

class HttpGetSmsSender extends AbstractHttpSmsSender
{
    public function sendThroughConsole($base_url, $query_url)
    {
        $command = 'curl -i ';

        if ($this->authentication)
            $command .= '--user ' . $this->username . ':' . $this->password . ' ';

        $command .= $this->commandLineHeaders();

        $command .= '"' . $base_url . '?' . $query_url . '" > /dev/null 2>&1 &';

        @exec($command);
    }

    public function sendThroughCurlPHP($base_url, $query_url)
    {
        try {
            $http = Http::timeout(config('sms.curl_timeout'));

            $http = $http->withHeaders(array_merge($this->getHeaders(), [
                'Content-Length' => 0,
            ]));

            if ($this->authentication) {
                $http = $http->withBasicAuth($this->username, $this->password);
            }

            $url = $base_url . '?' . $query_url;

            $response = $http->get($url);

            $response->throw();
            return $response->body();
        } catch (RequestException $e) {
            throw new ValidationException([
                'curl_request' => trans('validation.attributes.bad_sms_gateway_url') . ': ' . $e->getMessage(),
            ]);
        }
    }
}