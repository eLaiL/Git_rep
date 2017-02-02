<?php


function DownloadImageOnUrl($url)
{
    $ar = explode('http://images.asos-media.com', $url);
    $name = array_pop($ar);

    $name = str_replace('/', '-', $name);

    $ch = curl_init($url . '?$XXL$&wid=513&fit=constrain');
    $fp = fopen($name . ".jpeg", 'w+');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
    return $name;
}

function randSize()
{
    $sizes = array(
        'XXXS - Chest 76-81 cm',
        'XXS - Chest 81-86 cm',
        'XS - Chest 86-91 cm',
        'S - Chest 91-96 cm',
        'M - Chest 96-101 cm',
        'L - Chest 101-106 cm',
        'XL - Chest 106-111 cm',
        'XXL - Chest 111-116 cm',
        'XXXL - Chest 116-121 cm'
    );
    $available = rand(4, 9);
    $str = '';
    for ($i = 0; $i < $available; $i++) {
        $str .= "," . $sizes[$i];
    }
    return $str;
}

function Colors()
{
    static $col = 'label-success';
    if ($col == 'label-primary') {
        $col = 'label-success';
    } elseif ($col == 'label-info') {
        $col = 'label-primary';
    } elseif ($col == 'label-default') {
        $col = 'label-info';
    } elseif ($col == 'label-success') {
        $col = 'label-default';
    }
    return $col;
}

include 'simple_html_dom.php';
require_once "DB.php";


$id_category = 13;

//-------- global page--------//
$url = 'http://us.asos.com/men/a-to-z-of-brands/ellesse/cat/?cid=19763&refine=attribute_900:2030&currentpricerange=10-155&pgesize=36';
$data = file_get_html($url);


$products = $data->find('li[class=product-container interactions]');// this is all procuts in page
$productsDiscount = $data->find('li[class=markedDown]');// this is all procuts in page


$products = array_merge($products, $productsDiscount);

foreach ($products as $k => $v) {
    $href = $products[$k]->children()[0]->href; //jump to product detail page
//    $title_image_src = $products[$k]->children()[0]->children()[0]->children()[0]->src; //src title image
    $title = $products[$k]->children()[0]->children()[1];//title
    $title = $title->find('span')[0]->innertext;//title
    $price = $products[$k]->children()[1];//price
    $price = $price->children()[1]->children()[1]->innertext;//price
    $price = str_replace('$', '', $price);//price

//-------- detali page--------//
    $data = file_get_html($href);

    $descriptionObj = $data->find('div[class=col]');
    //-------- START edit description string (delete hrefs)--------//
    $whiles = array(0, 1, 2);
    $continues = array(2, 0, 1, 0, 0, 0);
    $descriptions = array();
    $swicher = 0;
    $descriptionFinal = '';
    for ($q = 0; $q <= 2; $q++) {
        for ($q1 = 0; $q1 <= $whiles[$q]; $q1++) {
            $description = $descriptionObj[$q]->children()[$q1]->innertext;

            for ($i = 0; $i < $continues[$swicher]; $i++) {
                $offset = strpos($description, '<a');
                $offset2 = strpos($description, '"><');
                $unsetStr = substr($description, $offset, $offset2 - $offset + 2);
                $description = str_replace("$unsetStr", '', $description);
            }
            if ($continues[$swicher] != 0) $descriptions[] = str_replace('</a>', '', $description);
            else $descriptions[] = $description;
            $swicher++;
        }
    }
//-------- END edit description string (delete hrefs)--------//

    $imagesDetali = array();
    $imgF = $data->find('div[class=window]');//its first image in page detail
    $imgF = $imgF[0]->children()[0]->children()[0]->children()[0]->src;

    $page = file_get_contents($href);
    $search = 'http://images.asos-media.com/products/';

    $offset = strpos($page, $search);

    for ($i = 0; $i < 4; $i++) {
        $offset = strpos($page, $search, $offset + 10);
        $strposEnd = strpos($page, '",', $offset);
        $sub = substr($page, $offset, $strposEnd - $offset);
        $imagesDetali[] = $sub;
    }
    $namesImages = array();

    foreach ($imagesDetali as $imgsrc) {
        $namesImages[] = DownloadImageOnUrl($imgsrc);
    }
    $titleImgNam = $namesImages[0];
    $namesImages = implode(',', $namesImages);

    $unicId = (int)(microtime(1));
    $sizes = randSize();
//    $colorSale = Colors();
    $collection_color = Colors();

    $d = "INSERT INTO `****` (`****`, `*****`, `*****`,
 `*****`, `*****`, `*****`,`*****`,`*****`,`*****`,`*****`,
 `*****`,`*****`,`discount_price`,`*****`,`*****`,`*****`,`*****`,`*****`,`*****`,`*****`,
 `*****`,`*****`,`*****`,`*****`,`*****`,`*****`,`*****`)
  VALUES ('" . $noname . "', '" . $noname . "', '" . $noname . "', '" . $noname . "', '" . 1 . "',
    '" . 1 . "', '" . 0 . "', '" . 1 . "', '" . 0 . "',  '" . 0 . "','" . $noname . "', '" . 0 . "', '" . 0 . "', '0', '" . $namesImages . "',
     '" . $noname . "', '" . 0 . "', '" . $noname . "', '" . 1 . "', '" . 0 . "','" . htmlspecialchars($noname[0], ENT_QUOTES) . "','" . htmlspecialchars($noname[1], ENT_QUOTES) . "',
     '" . htmlspecialchars($noname[2], ENT_QUOTES) . "','" . htmlspecialchars($noname[3], ENT_QUOTES) . "','" . htmlspecialchars($noname[4], ENT_QUOTES) . "','" . htmlspecialchars($descriptions[5], ENT_QUOTES) . "','Ellesse')";

    $result = mysql_query($d);
    if ($result) echo 'ok' . "\r\n";
    else echo 'error' . "\r\n";
    usleep(500);
}





