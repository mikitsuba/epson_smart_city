<?php

namespace App\Services;

use TCPDF;

class CreatePDFService
{
    public function createPDF($user) {
        // jsonの整形
        $address = $user["address"];
        $shelter = $user["shelter"];
        $shelterName = $user["shelterName"];
        $printerMail = $user["printerMail"];
        $name = $user["name"];

        // 地図画像取得
        $map_image = "http://maps.google.com/maps/api/staticmap?&size=400x400&markers=color:blue|" . $address . "&markers=color:green|" . $shelter . "&sensor=false&key=AIzaSyB-qHGJbsi-rr_HWMf7qmXsolcP1ISXsXg";

        // PDFの生成
        $tcpdf = new TCPDF("Portrait");
        $tcpdf->AddPage();
        $tcpdf->SetFont("kozminproregular", "", 10);

        $html = <<< EOF
  <style>
  .submitDate {
    margin: 0 20px 0 auto;
    text-align: right;
  }
  .main {
    width: 70%;
    margin: 0 auto;
  }
  .title {
    padding: 10px;
    background-color: black;
    color: white;
    font-weight: bold;
    margin: 10px 20px;
  }
  .attentionWrapper {
    width: 60%;
  }
  .disasterInfo {
    text-decoration: underline;
  }
  .addressWrapper span{
    font-weight: bold;
  }
  .mapWrapper {
    margin: 10px 0;
    width: 200px;
    float: left;
  }
  .attentionWrapper {
    padding: 10px 0px;
  }
  .attentionTitle {
    font-weight: bold;
  }
  .attentionList {
    display: flex;
  }
  .attentionItem {
    width: 33%;
    display: flex;
    align-items: center;
    padding: 5px 10px;
  }
  .chekmark{
    display: inline-block;
    width: 20px;
    height: 5px;
    border-left: 2px solid #25AF01;
    border-bottom: 2px solid #25AF01;
    transform: rotate(-45deg);
  }
  .attentionText {
    padding-left: 10px;
  }
  .footerWrapper {
    width: 70%;
    margin: 10px auto;
    display: flex;
    justify-content: space-between;
  }
  .contact {
    width: 30%;
  }
  .latestInfo {
    width: 70%;
    display: flex;
  }
  .qrWrapper {
    margin-left: 10px;;
  }
  .qrWrapper img{
    width: 80px;
    margin: 0 0 0 auto;
  }
  .contactTitle {
    font-weight: bold;
  }
  .latestInfo {
    border: solid 1px black;
    justify-content: space-between;
    margin-right: 20px;
    align-items: center;
  }
  .latestInfoWrapper {
    padding-left: 20px;
  }
  .latestInfoContent {
  }
  .latestInfoTitle {
    font-weight: bold;
  }
  </style>
  <div class="header">
          <div class="submitDate">送信日時：2021-12-10</div>
  </div>
  <div class="main">
      <div class="title">
          避難場所のお知らせ
      </div>
          <div class="disasterInfo">震度6の地震が発生しました</div>
          <div class="shelterInfo">$name さんの地域の避難場所は<br>「 $shelterName 」です</div>
          <div><span>避難場所: </span>$shelter</div>
          <div><span>登録住所: </span>$address</div>
          <div></div>
        <img class="mapWrapper" src="$map_image" />
        <div></div>
        <div class="attentionTitle">--家を出る前に３つの心がけ--</div>
        <div>外に出るときも周囲の確認を。ガラスや看板等が落ちてくる可能性があります。</div>
        <div>避難する時には、電気のブレーカーを切り、ガスの元栓を閉めましょう。</div>
        <div>我が家の安全を確認後、近所にも声をかけて安否を確認しましょう。</div>
  </div>
              <div class="contactTitle">お問い合わせ先</div>
              <div class="latestInfoTitle">自治体からの最新情報をスマホで確認</div>
              <div class="latestInfoContent"><a href="https://www.city.aizuwakamatsu.fukushima.jp/docs/2012110800034/">https://www.city.aizuwakamatsu.fukushima.jp/docs/2012110800034/</a></div>
          </div>
      </div>
  </div>
  EOF;


        $tcpdf->writeHTML($html);

        // PDF保存
        $fileSavePath = storage_path()."/app/";
        $fileName = "1.pdf";
        $tcpdf->Output($fileSavePath . $fileName, "F");
        return;
    }
}