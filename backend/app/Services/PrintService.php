<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class PrintService
{
    // デバイス認証
    public function authenticate($client_id, $secret, $device) {
        $auth_uri = 'https://' . 'api.epsonconnect.com' . '/api/1/printing/oauth2/auth/token?subject=printer';
        $auth = base64_encode("$client_id:$secret");

        $query_param = array(
            'grant_type' => 'password',
            'username' => $device,
            'password' => ''
        );
        $query_string = http_build_query($query_param, '', '&');

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Host: ' . 'api.epsonconnect.com' . "\r\n" .
                            'Accept: ' . 'application/json;charset=utf-8' . "\r\n" .
                            'Authorization: Basic ' . $auth . "\r\n" .
                            'Content-Length: ' . strlen($query_string) . "\r\n" .
                            'Content-Type: application/x-www-form-urlencoded; charset=utf-8' . "\r\n",
                'content' => $query_string,
                'request_fulluri' => true,
                'protocol_version' => '1.1',
                'ignore_errors' => true
            )
        );

        $http_response_header = null;
        $response = @file_get_contents($auth_uri, false, stream_context_create($options));

        $auth_result = array();
        $auth_result['Response']['Header'] = $http_response_header;
        $auth_result['Response']['Body'] = json_decode($response, true);

        $matches = null;
        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $auth_result['Response']['Header'][0], $matches);

        if ($matches[1] !== '200') {
            exit(1);
        }

        return $auth_result;
    }

    // job登録
    public function createJob($subject_id, $access_token) {
        $job_uri = 'https://api.epsonconnect.com/api/1/printing/printers/' . $subject_id . '/jobs';

        $data_param = array(
            'job_name' => 'SampleJob1',
            'print_mode' => 'document'
        );
        $data = json_encode($data_param);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Host: ' . 'api.epsonconnect.com' . "\r\n" .
                            'Accept: ' . 'application/json;charset=utf-8' . "\r\n" .
                            'Authorization: Bearer ' . $access_token . "\r\n" .
                            'Content-Length: ' . strlen($data) . "\r\n" .
                            'Content-Type: application/json;charset=utf-8' . "\r\n",
                'content' => $data,
                'request_fulluri' => true,
                'protocol_version' => '1.1',
                'ignore_errors' => true
            )
        );

        $http_response_header = null;
        $response = @file_get_contents($job_uri, false, stream_context_create($options));

        $job_result = array();
        $job_result['Response']['Header'] = $http_response_header;
        $job_result['Response']['Body'] = json_decode($response, true);

        $matches = null;
        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $job_result['Response']['Header'][0], $matches);

        if ($matches[1] !== '201') {
            exit(1);
        }

        return $job_result;
    }

    // ファイルアップロード
    public function uploadFile($upload_uri) {
        $content_type = 'application/octet-stream';
        $file_name = '1.pdf';
        $upload_uri .= '&File=' . $file_name;

        $data = Storage::get($file_name);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Host: ' . 'api.epsonconnect.com' . "\r\n" .
                            'Accept: ' . 'application/json;charset=utf-8' . "\r\n" .
                            'Content-Length: ' . strlen($data) . "\r\n" .
                            'Content-Type: ' . $content_type . "\r\n",
                'content' => $data,
                'request_fulluri' => true,
                'protocol_version' => '1.1',
                'ignore_errors' => true
            )
        );

        $http_response_header = null;
        $response = @file_get_contents($upload_uri, false, stream_context_create($options));

        $upload_result = array();
        $upload_result['Response']['Header'] = $http_response_header;
        $upload_result['Response']['Body'] = json_decode($response, true);

        $matches = null;
        preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $upload_result['Response']['Header'][0], $matches);

        if ($matches[1] !== '200') {
            exit(1);
        }

        return $upload_result;
    }

    // 印刷実行
    public function print($subject_id, $job_id, $access_token) {
        $print_uri = 'https://api.epsonconnect.com/api/1/printing/printers/' . $subject_id . '/jobs/' . $job_id . '/print';

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Host: ' . 'api.epsonconnect.com' . "\r\n" .
                            'Accept: ' . 'application/json;charset=utf-8' . "\r\n" .
                            'Authorization: Bearer ' . $access_token . "\r\n",
                'request_fulluri' => true,
                'protocol_version' => '1.1',
                'ignore_errors' => true
            )
        );

        $http_response_header = null;
        $response = @file_get_contents($print_uri, false, stream_context_create($options));

        $print_result = array();
        $print_result['Response']['Header'] = $http_response_header;
        $print_result['Response']['Body'] = json_decode($response, true);

        return $print_result;
    }
}