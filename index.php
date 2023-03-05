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

if(isset($_GET['min'])) $min=$_GET['min'];
else $min=1;

if(isset($_GET['limit'])) $limit=$_GET['limit'];
else $limit=300;

if (isset($_COOKIE['urtid'])) $urtid=$_COOKIE['urtid'];

$phpname=$_SERVER["SCRIPT_FILENAME"];
$logfile=__DIR__."/../log/dup.log";
$res=`/bin/ps ax`;
$thedate=date("Y-m-d H:i:s");
$psnum=substr_count($res, $phpname);
if (isset($_GET['force'])) $force=$_GET['force'];
else $force=2;
if ($psnum>$force) {
  $res0=`echo "$thedate $phpname $psnum " >> $logfile`;
  die("Duplicated ".$psnum." times! ".$n);
}

include(__DIR__ . "/../key/key_json.php");

$MYSQL_HOST=$key_array["MYSQL_HOST"];
$MYSQL_USER=$key_array["MYSQL_USER"];
$MYSQL_PASS=$key_array["MYSQL_PASS"];
$MYSQL_DB=$key_array["MYSQL_DB"];

$link = @mysqli_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS) or Die("Error");
@mysqli_select_db($link, $MYSQL_DB);
mysqli_set_charset($link, 'utf8mb4');


if (!isset($urtid) and $urtid<1) {
  printf("<h2><a href=\"/login.php\">請按此登入</a></h2>");
  printf("<h3>請用可以看到 Search Console 的帳號，記得在彈出視窗的 checkbox 授權看到 Search Console</h3>");

}

if (isset($urtid)) {
  printf("<h2>URT ID: %s</h2>", $urtid);

  $sql="select given_name, family_name, picture  from urt_user where id = $urtid;";
  $res=@mysqli_query($link, $sql);

  list($given_name, $family_name, $picture)=@mysqli_fetch_row($res);

  printf("Your name: %s %s <br>", $given_name, $family_name);
  printf("<img src=\"%s\" with=\"600\" heigh=\"400\">", $picture);
  //printf("%s", $picture);
 
  // printf("<h2><a href=\"/api/get_sites.php?user_id=%d\">更新列表</a>(若上面是空的)</h2>", $user_id);
  // printf("<h2><a href=\"https://myaccount.google.com/permissions\">若還不行請按此取消 GeegNe 的授權再登入一次</a>");

  printf("<h2><a href=\"/redir/logout.php\">請按此登出</a></h2>");

}


// upload watch history .json file
if (isset($urtid)) {

echo <<< HTML

    <form method="post" enctype="multipart/form-data" action="/redir/upload.php">
      <input type="file" name="file_csv" id="file_csv">
      <input name="action" type="hidden" value="add">
      <input type="submit" name="button" id="button" value="新增資料">
    </form>

HTML;
  
}


?>
