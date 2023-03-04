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


?>
