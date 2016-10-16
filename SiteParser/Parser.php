<?php

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

function explodeSrcImage($src)
{
    $exlp = explode("/", $src);
    $nameImg = array_pop($exlp);
    return $nameImg;
}

function DownloadImageOnUrl($path, $name, $url)
{
    $ch = curl_init($url);
    $fp = fopen($name, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}


require_once "DB.php";
include 'simple_html_dom.php';

$result = mysql_query("SELECT title FROM **** order by id DESC limit 8");
if ($result) {
    echo 'connect!';
} else {
    echo 'not connect';
    die();
}

$titlesMysql = array();
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
    $titlesMysql[] = mb_convert_encoding($row[0], 'utf-8', mb_detect_encoding($row[0]));
}
$url = 'https://www.******.com';
$data = file_get_html($url);


$requestParser = array(//it's request parser
    'all_title' => $data->find('div.post-text h3 a'),// all title
    'all_images_small_src' => $data->find('div.media-left a img'),// all src
    'all_pages_href' => $data->find('div.post-text h3 a'),// all href
    'all_news_divs' => $data->find('div.list-view')[0],//concretely all divs news
    'sponsorDogo' => $data->find('div.review-container')//advertising
);


$adv = $requestParser['sponsorDogo'];
$views = $requestParser['all_news_divs'];
$views = $views->children;
// let's go find where adv
foreach ($adv as $ad) { //if advertising null we continue
    foreach ($views as $k => $tmp) {
        if ($tmp->attr['class'] == $ad->attr['class']) {
            $adv = $k;
        }
    }
}


$AlltitlPage = $requestParser['all_title'];
$AllimgPage = $requestParser['all_images_small_src'];
$AllPagesHref = $requestParser['all_pages_href'];

if (count($adv) > 0) {
    unset($AlltitlPage[$adv], $AllPagesHref[$adv]); //Remove adv between news
    foreach ($AlltitlPage as $k => $v) {//After removal of keys an arrayshifted we align them
        $n[] = $AlltitlPage[$k];
        $p[] = $AllPagesHref[$k];
    }
    $AlltitlPage = $n;
    $AllPagesHref = $p;
}

$switcher = false;
$lacks_news = array(); //lacks news

if (count($titlesMysql) > 0) {
    foreach ($AlltitlPage as $k => $titleDOM) { //We recognize that the lack of news
        foreach ($titlesMysql as $value) {
//            $ds = $titleDOM->innertext;// test value
            if ($titleDOM->innertext == $value) {
                $switcher = true;
            }
        }
        if (!$switcher) {
            $lacks_news[$k] = $AllPagesHref[$k];//Add lacks news
        }
        $switcher = false;
    }
} else {
    $lacks_news = $AllPagesHref;
}


$news = array();
foreach ($lacks_news as $k => $dom) { // foreach lacks news
    $news[$k]['title'] = $dom->innertext;
    $imgNameSmall = explodeSrcImage($AllimgPage[$k]->src);
    $news[$k]['images_small'] = $imgNameSmall;
    DownloadImageOnUrl('', $imgNameSmall, $AllimgPage[$k]->src);

    $pos = strpos($url, "page");//check if is not the main page
    if ($pos) {
        $expl = explode("/", $url);
        array_pop($expl);
        array_pop($expl);
        $url = implode("/", $expl);
    }

    echo $dom->href . "\r\n";
    $page = file_get_html($url . $dom->href);

    $div = $page->find('div.responsive-body', 0);

    $images = '';
    $discription = '';
    $i = 0;
    while ($item = $div->children($i++)) {

        if ($item->tag == "figure") {
            $e = $item->children(0);//img
            $e2 = $item->children(1);//figuration

            $imgName = explodeSrcImage($e->src);
            DownloadImageOnUrl("", $imgName, $e->src);
            $discription .= "<img alt='' src=/images/news/$imgName class=img-responsive>";
            $discription .= " $e2->outertext";
        }

        if (isset($item->children(0)->tag)) {

            if ($item->tag == "p" and $item->children(0)->tag != 'iframe') {
                $text = $item->innertext;

                $str_pos = strpos($text, "<span style=");
                if ($str_pos === 0) {
                    $str_pos = strpos($text, '>', 23);
                    $text = substr($text, $str_pos + 1, strlen($text));
                }

                $text = str_replace(array("<span class=", 'dictionary', "'", '"', '>', '<', '/span'), "", $text);
                $discription .= '<p>' . $text . '</p>';

            }
            if ($item->children(0)->tag == 'iframe') {
                $discription .= $item->children(0)->outertext;

            }
        }
    }
    $news[$k]['description'] = $discription;
    $page->clear();
    unset($page);
}
if (count($lacks_news) > 0) { //add lacks news

    date_default_timezone_set('Europe/Kiev');
    $news = array_reverse($news);
    foreach ($news as $newOne) {

        $time = date("Y-m-d H:i:s");
        $new = htmlspecialchars($newOne['description'], ENT_QUOTES);
        $title = $newOne['title'];

        $result = mysql_query($d = "INSERT INTO `****` (`title`, `description`, `images_small`,
 `date`, `autor`, `waches`, `commentsCount`) VALUES ('" . $title . "', '" . $new . "', '" . $newOne['images_small'] . "', '" . $time . "', 'eLaiL', '0', '0')");
    }
}


?>



