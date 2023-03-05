<?php

require_once '/home/urtube/src/vendor/autoload.php';

$client = new Google_Client();
// 代入從 API console 下載下來的 client_secret
$client->setAuthConfig('/home/urtube/key/client_secret.json');

// 加入需要的權限（Google Drive API）
// 也可以使用 url，例如：https://www.googleapis.com/auth/drive.metadata.readonly
//$client->addScope(['profile', "https://www.googleapis.com/auth/webmasters"]);
$client->addScope(['profile', "https://www.googleapis.com/auth/youtube.readonly"]);

// 設定 redirect URI，登入認證完成會導回此頁
$client->setRedirectUri('https://urtube.analysis.tw/cookie.php');

// 不需要透過使用者介面就可以 refresh token
$client->setAccessType('offline');
// 支援 Incremental Authorization 漸進式擴大授權範圍
$client->setIncludeGrantedScopes(true);

// 產生登入用的 URL
$authUrl = $client->createAuthUrl();
// 導至登入認證畫面
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

?>
