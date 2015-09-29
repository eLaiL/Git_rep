<?php

class HTTP_hosting {

    var $files = 'C:\Users\Я\Desktop\server_eLaiL\htdocs\\';
    var $con_i = 0;
    var $point = 0;
    var $buff = 1024;
    var $file_name;
    var $dirr;
    var $main_sock;

    //Пользоватильские функцыи
    function formats ($type){
        $formats = array(
            ".pdf"          =>      "application/pdf",
            ".sig"          =>      "application/pgp-signature",
            ".spl"          =>      "application/futuresplash",
            ".class"        =>      "application/octet-stream",
            ".ps"           =>      "application/postscript",
            ".torrent"      =>      "application/x-bittorrent",
            ".dvi"          =>      "application/x-dvi",
            ".gz"           =>      "application/x-gzip",
            ".pac"          =>      "application/x-ns-proxy-autoconfig",
            ".swf"          =>      "application/x-shockwave-flash",
            ".tar.gz"       =>      "application/x-tgz",
            ".tgz"          =>      "application/x-tgz",
            ".tar"          =>      "application/x-tar",
            ".zip"          =>      "application/zip",
            ".mp3"          =>      "audio/mpeg",
            ".m3u"          =>      "audio/x-mpegurl",
            ".wma"          =>      "audio/x-ms-wma",
            ".wax"          =>      "audio/x-ms-wax",
            ".ogg"          =>      "application/ogg",
            ".wav"          =>      "audio/x-wav",
            ".gif"          =>      "image/gif",
            ".jpg"          =>      "image/jpeg",
            ".jpeg"         =>      "image/jpeg",
            ".png"          =>      "image/png",
            ".xbm"          =>      "image/x-xbitmap",
            ".xpm"          =>      "image/x-xpixmap",
            ".xwd"          =>      "image/x-xwindowdump",
            ".css"          =>      "text/css",
            ".html"         =>      "text/html",
            ".htm"          =>      "text/html",
            ".js"           =>      "text/javascript",
            ".asc"          =>      "text/plain",
            ".c"            =>      "text/plain",
            ".cpp"          =>      "text/plain",
            ".log"          =>      "text/plain",
            ".conf"         =>      "text/plain",
            ".text"         =>      "text/plain",
            ".txt"          =>      "text/plain",
            ".spec"         =>      "text/plain",
            ".dtd"          =>      "text/xml",
            ".xml"          =>      "text/xml",
            ".mpeg"         =>      "video/mpeg",
            ".mpg"          =>      "video/mpeg",
            ".mov"          =>      "video/quicktime",
            ".qt"           =>      "video/quicktime",
            ".avi"          =>      "video/x-msvideo",
            ".asf"          =>      "video/x-ms-asf",
            ".asx"          =>      "video/x-ms-asf",
            ".wmv"          =>      "video/x-ms-wmv",
            ".bz2"          =>      "application/x-bzip",
            ".tbz"          =>      "application/x-bzip-compressed-tar",
            ".tar.bz2"      =>      "application/x-bzip-compressed-tar",
            ".odt"          =>      "application/vnd.oasis.opendocument.text",
            ".ods"          =>      "application/vnd.oasis.opendocument.spreadsheet",
            ".odp"          =>      "application/vnd.oasis.opendocument.presentation",
            ".odg"          =>      "application/vnd.oasis.opendocument.graphics",
            ".odc"          =>      "application/vnd.oasis.opendocument.chart",
            ".odf"          =>      "application/vnd.oasis.opendocument.formula",
            ".odi"          =>      "application/vnd.oasis.opendocument.image",
            ".odm"          =>      "application/vnd.oasis.opendocument.text-master",
            ".ott"          =>      "application/vnd.oasis.opendocument.text-template",
            ".ots"          =>      "application/vnd.oasis.opendocument.spreadsheet-template",
            ".otp"          =>      "application/vnd.oasis.opendocument.presentation-template",
            ".otg"          =>      "application/vnd.oasis.opendocument.graphics-template",
            ".otc"          =>      "application/vnd.oasis.opendocument.chart-template",
            ".otf"          =>      "application/vnd.oasis.opendocument.formula-template",
            ".oti"          =>      "application/vnd.oasis.opendocument.image-template",
            ".oth"          =>      "application/vnd.oasis.opendocument.text-web",
            # make the default mime type application/octet-stream.
            "."              =>      "text/html",
            ".php"          =>      "php"
        );
        foreach ($formats as $k => $v){
            if ($k === $type) return $v;
        }
    }
    function sub_input ($head,$ot,$do){
        $strlen = strlen($ot);
        $pos_ot = strpos($head,$ot)+$strlen;
        $pos_do = strpos($head,$do,$pos_ot);
        $lenght = $pos_do-$pos_ot;
        $sub = substr($head,$pos_ot,$lenght);
        return $sub;
    }
    function Replacing_key_names($head){
        $mass = explode("\n",$head);
        $http = array_shift($mass);
        foreach ($mass as $k => $v){
            $pos = strpos($v,":");
            $sub1 = substr($v,0,$pos);
            $sub2 = substr($v,$pos+1,strlen($v));
            $mass[$sub1] = $sub2;
            unset($mass[$k]);
        }
        $mass["http"] = $http;
        return $mass;
    }

    //Функцыонал
    function reception(){
        $connect = @socket_accept($this->main_sock);//Проверяем новое подключение
        if ($connect !== false) {
            $this->connects[] = $connect;
        }
    }
    function checkSocket(){
        foreach ($this->connects as $k => $connect) {
            $input_data = @socket_read($connect, 1024);
            $err = socket_last_error($connect);
            if ($err === 10054 || $err === 10053 || $err === false) { // Если соединение прервано
                unset($this->connects[$k]);
            }
            if ($input_data !== false && strlen($input_data) > 2) {
                $present = null;
                $this->file_name = $this->sub_input($input_data, "GET /", " ");
                $ip_xosta = $this->sub_input($input_data, "Referer: ", "\n");
                $data_paketa = $ip_xosta . "," . $this->file_name;
                $this->dirr = $this->files . $this->file_name;
                $exist = file_exists($this->dirr);
                if ($exist !== false) {
                    $kays_datu = array_keys($this->dataConnects);
                    foreach ($kays_datu as $kay => $value) {
                        $mass_paketov = $this->dataConnects[$value];
                        if ($data_paketa === $mass_paketov) {
                            $this->read_connects[$value][] = $connect;
                            $present = true;
                            break;
                        }
                    }

                    if ($present === false or $present === null) {//Если такого нету
                        $this->read_connects[$this->con_i][] = $connect;
                        $this->dataConnects[$this->con_i] = $data_paketa;
                        $this->in[$this->con_i] = 0;
                        if (isset($this->DownloadSpeed[$this->file_name])) $dw = $this->DownloadSpeed[$this->file_name];
                        else $dw = 1024;
                        $rezult = $dw / $this->buff;//Этим мы узнаем сколько раз нуждно срабатывать на секунду
                        $rezult = 1000 / $rezult;//Тут мы узнаем сколько раз на 1000 Мсек
                        $rezult = $rezult / 1000;
                        $this->timeToAdd[$this->con_i] = $rezult;
                        $this->Time_response[$this->con_i] = microtime(true) + $rezult;
                    }

                    $filesize = filesize($this->dirr);
                    $pos = strpos($this->file_name, '.');
                    $sub = substr($this->file_name, $pos, strlen($this->file_name));
                    $type = $this->formats($sub);

                    if ($present !== null) {
                        $this->fopens[$value][] = fopen($this->dirr, 'r');
                        $this->types[$value][] = $type;
                        $mass_replaced_kays = $this->Replacing_key_names($input_data);

                        if (isset($mass_replaced_kays['Range'])) {
                            $pos = strpos($mass_replaced_kays['Range'], "=");
                            $pos2 = strpos($mass_replaced_kays['Range'], "-") - 1;
                            $len = $pos2 - $pos;
                            $range_bytes = substr($mass_replaced_kays['Range'], $pos + 1, $len);
                            $filesize = $filesize - $range_bytes;
                            $count = count($this->fopens[$value]) - 1;
                            $this->size_lefts[$value][] = $filesize;
                            fseek($this->fopens[$value][$count], $range_bytes);
                            echo 'connection ' . $this->con_i . " seek $range_bytes.\r\n";
                        }
                    } else {
                        $this->fopens[$this->con_i][] = fopen($this->dirr, 'r');
                        $this->types[$this->con_i][] = $type;
                    }
                } else {
                    $error_file = $this->file_name . 'error_404.html';
                    $error_size = filesize($error_file);
                    date_default_timezone_set('Europe/Kiev');
                    $time = date('r');
                    $scrypt = 'HTTP/1.1 404 Not Found' . "\r\n";
                    $scrypt .= 'Date: ' . $time . "\r\n";
                    $scrypt .= 'Server: eLaiL' . "\r\n";
                    $scrypt .= 'Content-Length:' . $error_size . "\r\n";
                    $scrypt .= 'Keep-Alive: timeout=5, max=100' . "\r\n";
                    $scrypt .= 'Connection: close' . "\r\n";
                    $scrypt .= 'Content-Type: text/html' . "\r\n";
                    $scrypt .= "\r\n";
                    $scrypt .= file_get_contents($error_file);
                    socket_write($connect, $scrypt);
                    socket_close($connect);
                }
                //inpute data file browser
                date_default_timezone_set('Europe/Kiev');
                $time = date('r');
                $scrypt = 'HTTP/1.1 200 OK' . "\r\n";
                $scrypt .= 'Date: ' . $time . "\r\n";
                $scrypt .= 'Server: eLaiL' . "\r\n";
                $scrypt .= 'Accept-Ranges: bytes' . "\r\n";
                $scrypt .= 'Content-Length: ' . $filesize . "\r\n";
                $scrypt .= 'Keep-Alive: timeout=5, max=100' . "\r\n";
                $scrypt .= 'Connection: Keep-Alive' . "\r\n";
                $scrypt .= 'Content-Type: ' . $type . "\r\n";
                $scrypt .= "\r\n";
                socket_write($connect, $scrypt);
                echo 'connection ' . $this->con_i . " start download $this->dirr.\r\n";
                $this->con_i++;
            }
        }
    }
    function ClearSocket(){
        if (isset($this->read_connects[$this->point]))if (count($this->read_connects[$this->point]) === 0){//Удаление Корня сокетов
            unset($this->size_lefts[$this->point],$this->fopens[$this->point],$this->types[$this->point],$this->Time_response[$this->point],$this->timeToAdd[$this->point],$this->dataConnects[$this->point],$this->read_connects[$this->point],$this->in[$this->point]);
        }
        if (isset($this->read_connects[$this->point])){
            $input_data = @socket_read($this->read_connects[$this->point][$this->in[$this->point]], 1024);
            $err = socket_last_error();
            if ($err === 10054 || $err === 10053) { // Если соединение прервано
                if (isset($this->read_connects[$this->point][$this->in[$this->point]])) {
                    socket_close($this->read_connects[$this->point][$this->in[$this->point]]);
                    unset($this->size_lefts[$this->point][$this->in[$this->point]],$this->read_connects[$this->point][$this->in[$this->point]],$this->fopens[$this->point][$this->in[$this->point]],$this->types[$this->point][$this->in[$this->point]]);
                    echo 'connection ' . " closed.\r\n";
                }
            }
        }
    }
    function Downloading(){
        if (isset($this->types[$this->point]) and isset($this->in[$this->point]) and isset($this->types[$this->point][$this->in[$this->point]])){
            if ($this->types[$this->point][$this->in[$this->point]] === 'php') {
                ob_start();
                include("$this->dirr");
                $out1 = ob_get_contents();
                ob_clean();
                @socket_write($this->read_connects[$this->point][$this->in[$this->point]], $out1);
            }
        }
        if (isset($this->types[$this->point]) and isset($this->in[$this->point]) and isset($this->types[$this->point][$this->in[$this->point]])){
            if ($this->types[$this->point][$this->in[$this->point]] === 'text/plain' or $this->types[$this->point][$this->in[$this->point]] === 'audio/mpeg') {
                $timeEnd = microtime(true);
                if ($this->Time_response[$this->point] < $timeEnd)
                {
                    $fred = fread($this->fopens[$this->point][$this->in[$this->point]], $this->buff);
                    $bytes = @socket_write($this->read_connects[$this->point][$this->in[$this->point]], $fred, $this->buff);
                    if ($bytes === 0 or $bytes === false) {
                        $f = ftell($this->fopens[$this->point][$this->in[$this->point]]);
                        fseek($this->fopens[$this->point][$this->in[$this->point]], $f - $this->buff);
                    }else {
                        $this->Time_response[$this->point] = $this->Time_response[$this->point] + $this->timeToAdd[$this->point];
                        if (isset($this->size_lefts[$this->point][$this->in[$this->point]])) $this->size_lefts[$this->point][$this->in[$this->point]] = $this->size_lefts[$this->point][$this->in[$this->point]] - $this->buff;
                    }
                }
            }
        }
    }
    function GlobalCounter(){
        //newPointer
        if (count($this->read_connects) > 0) {
            $keys = array_keys($this->read_connects);
            $kMax = max($keys);
            if ($this->point >= $kMax) {
                $this->point = min($keys);
            } else {
                $ks = array_search($this->point,$keys);
                $ks++;
                $this->point = $keys[$ks];
            }
            if (isset($this->read_connects[$this->point]) and count(array_keys($this->read_connects[$this->point])) > 0){
                $keys = array_keys($this->read_connects[$this->point]);
                $kMax = max($keys);
                if ($this->in[$this->point] >= $kMax) {
                    $this->in[$this->point] = min($keys);
                } else {
                    $ks = array_search($this->in[$this->point],$keys);
                    $ks++;
                    $this->in[$this->point] = $keys[$ks];
                }
            }
        }
    }
    function __construct(){
        $this->main_sock = socket_create(AF_INET,SOCK_STREAM,0);
        socket_bind($this->main_sock,'127.0.0.1',80);
        socket_listen($this->main_sock,10);
        socket_set_nonblock($this->main_sock);

        $this->fopens = array();
        $this->connects = array ();
        $this->read_connects = array ();
        $this->types = array();
        $this->size_lefts = array();
        $this->dataConnects = array();
        $this->in = array(0);
        $this->DownloadSpeed = array("2014.mp3"=> 1024*1024,"1.mp3" => 1024*1024*2);
        $this->Time_response = array();
        $this->timeToAdd = array();
    }
}

$Obj = new HTTP_hosting();

while (true){
    $Obj->reception();
    $Obj->checkSocket();
    $Obj->ClearSocket();
    $Obj->Downloading();
    $Obj->GlobalCounter();
    if (!isset($Obj->fopens[$Obj->point])){
        sleep(1);
    }
}
?>