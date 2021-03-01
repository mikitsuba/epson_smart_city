<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrintService;
use App\Services\CreatePDFService;

class PrintController extends Controller
{
    protected $printservice;
    protected $createpdfservice;
    private $users;

    public function __construct(PrintService $printservice, CreatePDFService $createpdfservice)
    {
        $this->printservice = $printservice;
        $this->createpdfservice = $createpdfservice;
    }

    private function get_data() {
        $url = "https://script.google.com/macros/s/AKfycbwUOW_NU-sY_aiTQoKdoCCBkMCj8qFoiLRv7g1zBR4ILSmnVcH0/exec";
        $json = file_get_contents($url);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $arr = json_decode($json, true);
        return $arr["data"];
    }

    private function create_pdf($user) {
        $this->createpdfservice->createPDF($user);
    }

    private function execute_print($client_id, $secret, $device) {
        $auth_result = $this->printservice->authenticate($client_id, $secret, $device);

        $subject_id = $auth_result['Response']['Body']['subject_id'];
        $access_token = $auth_result['Response']['Body']['access_token'];
        $job_result = $this->printservice->createJob($subject_id, $access_token);

        $upload_uri = $job_result['Response']['Body']['upload_uri'];
        $this->printservice->uploadFile($upload_uri);

        $job_id = $job_result['Response']['Body']['id'];
        $print_result = $this->printservice->print($subject_id, $job_id, $access_token);
    }

    public function print() {
        $this->users = $this->get_data();
        foreach ($this->users as $user) {
            $this->create_pdf($user);
            // dd($user);
            $client_id = $user["clientId"];
            $secret = $user["clientSecret"];
            $device = $user["printerMail"];
            $this->execute_print($client_id, $secret, $device);
        }
    }
}