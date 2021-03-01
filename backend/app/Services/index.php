<?php
include "./tcpdf.php";

// endpointからjsonを取得
$url = "https://script.google.com/macros/s/AKfycbwUOW_NU-sY_aiTQoKdoCCBkMCj8qFoiLRv7g1zBR4ILSmnVcH0/exec";
$json = file_get_contents($url);
$json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
$arr = json_decode($json, true);
$users = $arr["data"];

foreach ($users as $user) {
  createPDF($user);
}

function createPDF($user)
{
  // jsonの整形
  $address = $user["address"];
  $shelter = $user["shelter"];
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
    text-align: center;
    padding: 10px;
    background-color: black;
    color: white;
    font-weight: bold;
    margin: 10px 20px;
  }

  .disasterInfo {
    text-align: center;
    text-decoration: underline;
  }

  .shelterInfo {
    text-align: center;
  }

  .addressWrapper span{
    font-weight: bold;
  }

  .mapWrapper {
    margin: 10px 0;
    display: flex;
    justify-content: center;
  }

  .attentionWrapper {
    padding: 10px 0px;
  }

  .attentionTitle {
    font-weight: bold;
    text-align: center;
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
      <div headerWrapper>
          <div class="submitDate">送信日時：2021-12-10</div>
      </div>
  </div>
  <div class="main">
      <div class="title">
          hogehogeのお知らせ
      </div>
      <div class="description">
          <div class="disasterInfo">震度５とかだぞ</div>
          <div class="shelterInfo">$name さんの地域の避難場所は<br>「 $shelter 」です</div>
          <div class="addressWrapper">
              <div><span>避難場所: </span>$shelter</div>
              <div><span>登録住所: </span>$address</div>
          </div>
      </div>
      <div class="mapWrapper">
        <img src="$map_image" />
      </div>
      <div class="attentionWrapper">
          <div class="attentionTitle">--家を出る前に３つの心がけ--</div>
          <div class="attentionList">
              <div class="attentionItem"><span class="chekmark"></span><span class="attentionText">外に出るときも周囲の確認を。ガラスや看板等が落ちてくる可能性があります。</span>
              </div>
              <div class="attentionItem"><span class="chekmark"></span><span class="attentionText">避難する時には、電気のブレーカーを切り、ガスの元栓を閉めましょう。</span></div>
              <div class="attentionItem"><span class="chekmark"></span><span class="attentionText">我が家の安全を確認後、近所にも声をかけて安否を確認しましょう。</span></div>
          </div>
      </div>
  </div>
  <div class="footer">
      <div class="footerWrapper">
          <div class="contact">
              <div class="contactTitle">お問い合わせ先</div>
              <div class="contactContent">hogehoge</div>
          </div>
          <div class="latestInfo">
              <div class="latestInfoWrapper">
                  <div class="latestInfoTitle">自治体からの最新情報をスマホで確認</div>
                  <div class="latestInfoContent"><a href="https://www.city.aizuwakamatsu.fukushima.jp/docs/2012110800034/">https://www.city.aizuwakamatsu.fukushima.jp/docs/2012110800034/</a></div>
              </div>
              <div class="qrWrapper"><img src="./qr.png"></div>
          </div>
      </div>
  </div>
  EOF;


  $tcpdf->writeHTML($html);

  // PDF保存
  $fileSavePath = "/Applications/MAMP/htdocs/TCPDF/";
  $fileName = $printerMail . ".pdf";
  $tcpdf->Output($fileSavePath . $fileName, "F");
  return;
}
