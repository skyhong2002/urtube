<?php

if (isset($argv))
    for ($i=1;$i<count($argv);$i++)
    {
        $it = explode("=",$argv[$i]);
        $_GET[$it[0]] = $it[1];
    }

$n="\n";
$brn="<br />\n";

date_default_timezone_set("Asia/Taipei");

if(isset($_GET['debug'])) $debug=$_GET['debug'];
else $debug=1;

if(isset($_GET['limit'])) $limit=$_GET['limit'];
else $limit=500;

$api_userinfo="https://www.googleapis.com/oauth2/v2/userinfo?access_token=";

require_once '/home/urtube/src/vendor/autoload.php';

$client = new Google_Client();
// 代入從 API console 下載下來的 client_secret
$client->setAuthConfig('client_secret.json');
//$client->setAccessType("offline");

// Authorization code
$code = $_GET['code'];
$client->authenticate($code);

// 取得 Access Token
$accessToken = $client->getAccessToken();
//$accessToken = $client->getRefreshToken();

/*
if ($client->isAccessTokenExpired()) {
    $refresh_token = json_decode($accessToken)->refresh_token;
    $client->refreshToken($refresh_token);
}
*/

$accessToken_array=json_decode(json_encode ( $accessToken) , true);
//print_r($accessToken_array);

$token=$accessToken_array['access_token'];
//echo $token, $brn;
$expires_in=date("Y-m-d H:i:s", time()+$accessToken_array['expires_in']);
$refresh_token=$accessToken_array['refresh_token'];

$userinfo_return=file_get_contents($api_userinfo.$token);
if ($debug>3) echo $userinfo_return;
$userinfo_array=json_decode($userinfo_return, true);
$id=$userinfo_array['id'];
//echo "ID : ", $id, $brn;

//mysql login

include(__DIR__ . "/../key/key_json.php");

$MYSQL_HOST=$key_array["MYSQL_HOST"];
$MYSQL_USER=$key_array["MYSQL_USER"];
$MYSQL_PASS=$key_array["MYSQL_PASS"];
$MYSQL_DB=$key_array["MYSQL_DB"];

$link = @mysqli_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS) or Die("Error");
@mysqli_select_db($link, $MYSQL_DB);
mysqli_set_charset($link, 'utf8mb4');

$sql="select pk from urt_user where id = '$id' ";
$res=@mysqli_query($link, $sql);
list($pk)=@mysqli_fetch_row($res);

if ($pk<1) {
  $nowtime=date("Y-m-d H:i:s");
  $given_name=$userinfo_array['given_name'];
  $family_name=$userinfo_array['family_name'];
  $picture=$userinfo_array['picture'];
  $locale=$userinfo_array['locale'];
  $code_return=addslashes(json_encode($accessToken));
  $sql="insert into urt_user (id, ctime, given_name, family_name, picture, locale, access_token, expires_in, refresh_token, code_return) value ";
  $sql.=" ('$id', '$nowtime', '$given_name', '$family_name', '$picture', '$locale', '$token', '$expires_in', '$refresh_token', '$code_return')";
  if ($debug>4) echo $sql, $n;
  @mysqli_query($link, $sql);
} else {
    
  $sql="update urt_user set urt_user set mtime='$nowtime', access_token='$token', expires_in='$expires_in' where pk='$pk' ";
  @mysqli_query($sql);

}

// $result_return=`/usr/bin/php /home/gsc/api/get_sites.php user_id=$user_id`;

setcookie("urtid", $id, time()+86400*365);

$referer="https://urtube.analysis.tw/";
//$referer="https://urtube.analysis.tw/api/get_sites.php?user_id=$id";
header("Location: $referer");

?>
