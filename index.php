<?

ini_set('max_execution_time', 3600*3);
set_time_limit(0);

function cp1251_utf8( $sInput )
{
    $sOutput = "";

    for ( $i = 0; $i < strlen( $sInput ); $i++ )
    {
        $iAscii = ord( $sInput[$i] );

        if ( $iAscii >= 192 && $iAscii <= 255 )
            $sOutput .=  "&#".( 1040 + ( $iAscii - 192 ) ).";";
        else if ( $iAscii == 168 )
            $sOutput .= "&#".( 1025 ).";";
        else if ( $iAscii == 184 )
            $sOutput .= "&#".( 1105 ).";";
        else
            $sOutput .= $sInput[$i];
    }

    return $sOutput;
}


function get_page($url, $filename=null){

    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_HEADER, 0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HEADER,false);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
    curl_setopt($ch,CURLOPT_COOKIEJAR,"cookie.txt");
    curl_setopt($ch,CURLOPT_COOKIEFILE,"cookie.txt");
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36');

    $page = curl_exec($ch);

    if(!curl_errno($ch))
    {
       $info = curl_getinfo($ch);
       //echo "\n Прошло " . $info['total_time'] . " секунд, скачано {$info['size_download']}   адрес " . $info['url'];
       //var_dump($info);
    }

    // завершение сеанса и освобождение ресурсов
   curl_close($ch);

    if ($filename){
        $handle = fopen("$filename", "a+");
        fputs($handle, "$page");
        return true;
    }
    	//echo $page;
   	return $page;

}


$html = get_page("https://instagram.com/{$_GET["account"]}/");

$template = '#<script type="text/javascript">window._sharedData =\s+(.*?);</script>#is';

$matches = array();

if (preg_match_all($template, $html, $matches, PREG_SET_ORDER)){
    $json = json_decode($matches[0][1]);
    $nodes = ($json->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges);

    if (@$_GET['type'] == "json" || @$_GET['type'] == false ){
        if (isset($_GET["host"])){
            header("Access-Control-Allow-Origin: https://{$_GET["host"]}");
        }
        echo json_encode($nodes);
        exit();
}

    print_r($nodes);
?>

<table>
<?
    foreach ($nodes as $node){
        echo "<tr><td><img src=\"{$node->node->thumbnail_src}\"></td>";
        echo "<td>{$node->node->edge_media_to_caption->edges[0]->node->text}</td>";
        echo "<td>{$node->node->edge_liked_by->count}</td></tr>";
    }
}
?>
</table>


<?
/*

ini_set('max_execution_time', 3600*3);
set_time_limit(0);

function cp1251_utf8( $sInput )
{
    $sOutput = "";

    for ( $i = 0; $i < strlen( $sInput ); $i++ )
    {
        $iAscii = ord( $sInput[$i] );

        if ( $iAscii >= 192 && $iAscii <= 255 )
            $sOutput .=  "&#".( 1040 + ( $iAscii - 192 ) ).";";
        else if ( $iAscii == 168 )
            $sOutput .= "&#".( 1025 ).";";
        else if ( $iAscii == 184 )
            $sOutput .= "&#".( 1105 ).";";
        else
            $sOutput .= $sInput[$i];
    }

    return $sOutput;
}

/*
function get_page($url, $filename=null){
//https://www.instagram.com/instagram/?__a=1.
    $ch = curl_init();

    curl_setopt($ch,CURLOPT_HTTPHEADER, array(
        "Sec-Fetch-Mode: navigate",
        "Sec-Fetch-Site: none",
        "Sec-Fetch-User: ?1",
        "Upgrade-Insecure-Requests: 1"));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_HEADER,1);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,30);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION  ,1);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch,CURLOPT_COOKIEJAR,"cookie.txt");
    curl_setopt($ch,CURLOPT_COOKIEFILE,"cookie.txt");
    //curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; YandexBot/3.0)');
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.120 Safari/537.36');
    $page = curl_exec($ch);

    if(!curl_errno($ch))
    {
        $info = curl_getinfo($ch);
        //echo "\n Прошло " . $info['total_time'] . " секунд, скачано {$info['size_download']}   адрес " . $info['url'];
        var_dump($info);
    }

    // завершение сеанса и освобождение ресурсов
    curl_close($ch);

    if ($filename){
        $handle = fopen("$filename", "a+");
        fputs($handle, "$page");
        return true;
    }
    echo $url;
    echo $page;
    return $page;
}


$account = isset($_GET["account"]) ? $_GET["account"] : "raduga_veshnyaki";
$html = get_page("https://instagram.com/{$account}/");


$template = '#<script type="text/javascript">window._sharedData =\s+(.*?);</script>#is';

$matches = array();

if (preg_match_all($template, $html, $matches, PREG_SET_ORDER)){
$json = json_decode($matches[0][1]);
$nodes = ($json->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges);

if (@$_GET['type'] == "json" || @$_GET['type'] == false ){
    header("Access-Control-Allow-Origin: https://{$_GET["host"]}");
    echo json_encode($nodes);
    exit();
}
else {

?>
<table>
    <?
    foreach ($nodes as $node){
        echo "<tr><td><img src=\"{$node->node->thumbnail_src}\"></td>";
        echo "<td>{$node->node->edge_media_to_caption->edges[0]->node->text}}</td>";
        echo "<td>{$node->node->edge_liked_by->count}</td></tr>";
    }
    }
    }
    ?>
</table>

*/
?>
