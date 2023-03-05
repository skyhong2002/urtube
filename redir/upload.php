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

if(isset($_GET['show'])) $show=$_GET['show'];
else $show=0;

if(isset($_GET['limit'])) $limit=$_GET['limit'];
else $limit=30;

if(isset($_GET['order'])) $order=$_GET['order'];
else $order="burst";

if(isset($_GET['makecache'])) $makecache=$_GET['makecache'];
else $makecache=0;

if(isset($_GET['type'])) $type=$_GET['type'];
else $type='news';

$myfile=$_FILES["file_csv"];

$fp=fopen($myfile['tmp_name'], "rb");
if(!$fp) die("file open error");

$file_type=$myfile["type"];
$file_name=$myfile["name"];
$file_size=$myfile["size"];

print_r($myfile);

//$line = fgetcsv($fp);
//print_r($line);

//$file_data=file_get_contents($file_name);
//echo $file_data;

//$history_json=file_get_contents($fp);
//$history_json=readfile($fp);
//echo $history_json;

$data_string = fread($fp, $file_size);

/*
$data_string="";
while (($line = fgets($fp)) !== FALSE) {
$data_string.=$line;
//echo $line, $brn;
}
*/

//echo $data_string, $n;
$data_array=json_decode($data_string, true);
//print_r($data_array);

foreach($data_array as $datat){
  $videoId=$datat['titleUrl'];
  $videoId=str_replace("https://www.youtube.com/watch?v=", "", $videoId);
  if (strlen($videoId)>5){
  $channel=$datat['subtitles'][0]['url'];;
  $channel=str_replace("https://www.youtube.com/channel/", "", $channel);
  //printf("URL: %s<br />", $datat['titleUrl']);
  //printf("VideoId: %s<br />", $videoId);
  if (!isset($view_count[$videoId])) $view_count[$videoId]=0;
  if (!isset($channel_count[$channel])) $channel_count[$channel]=0;
  $view_count[$videoId]++;
  $channel_count[$channel]++;
  $view_title[$videoId]=$datat['title'];
  $channel_title[$channel]=$datat['subtitles'][0]['name'];
  }
}

arsort($view_count);
arsort($channel_count);

printf("<ol>");
foreach ($view_count as $videoId => $count){
  if ($count>3){
    //printf("<li><img src=\"https://img.youtube.com/vi/%s/sddefault.jpg\" width=\"100\" height=\"50\">", $videoId);
    //printf("<li><img src=\"https://img.youtube.com/vi/%s/sddefault.jpg\" width=\"100\" >", $videoId);
    printf("<li><img src=\"https://img.youtube.com/vi/%s/default.jpg\" width=\"100\" >", $videoId);
    printf("<a href=\"https://www.youtube.com/watch?v=%s\">%s</a> : %d <br />", $videoId, $view_title[$videoId], $count);
    printf("</li>");
  }
}
printf("</ol>");

foreach ($channel_count as $channel => $count){
  if ($count>3){
    printf("<a href=\"https://www.youtube.com/channel/%s\">%s</a> : %d <br />", $channel, $channel_title[$channel], $count);
  }
}

include(__DIR__ . "/../../key/key_json.php");

$MYSQL_HOST=$key_array["MYSQL_HOST"];
$MYSQL_USER=$key_array["MYSQL_USER"];
$MYSQL_PASS=$key_array["MYSQL_PASS"];
$MYSQL_DB=$key_array["MYSQL_DB"];

$link = @mysqli_connect($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASS) or Die("Error");
@mysqli_select_db($link, $MYSQL_DB);
mysqli_set_charset($link, 'utf8mb4');

fclose($fp);

//printf("<a href=\"./\">再上傳其他檔案</a><br />");
//printf("<a href=\"./link_plot.php?pid=%s\">看結果</a>", $pid);

?>
