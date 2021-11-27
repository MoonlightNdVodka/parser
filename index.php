<?php
require_once 'func.php';

//ФУНКЦИЯ ОТЛАДКИ,  отображает аргумент функции моноширинным шрифтом и со всеми пробелами между словами
function pr($s)
{
    echo '<pre>';
    print_r($s);
    echo '</pre>';
}

// ПАРСЕР 1
// Парсер выводит: Ссылку на сайт и имя сайта, время выполнения скрипта

//function getUrl()
//{
//    $start = microtime(true);
//
//    $getStartTime = date('d.m.Y, l, H:i:s', time()) . '<br>';
//    $url = 'https://mail.ru/';
//
//    $resource = file_get_contents($url);
//    print_r($resource);
//
//    $curl = curl_init();
//    curl_setopt($curl, CURLOPT_URL, $url);
//    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    $resource = curl_exec($curl);
//
//    preg_match('#<title>(.*?)</title>#su', $resource, $matches);
//    $title = $matches[1];
//
//    $file = $resource;
//
//    file_put_contents(
//        'mailRu.html',
//        $file
//    );
//    echo '<br>' . 'URL сайта: ' . $url . '<br>';
//    echo 'Название сайта: ' . $title . '<br>' . '<br>';
//    $getEndTime = date('d.m.Y, l, H:i:s', time());
//    echo "Начало выполнения скрипта: " . $getStartTime . '<br>';
//    echo "Время завершения выполнения скрипта: " . $getEndTime . '<br>';
//    $time = microtime(true) - $start;
//    $formatTime = number_format($time, 3);
//    echo "Время выполнения скрипта " . $formatTime;
//}
//
//echo getUrl();


// ПАРСЕР 2. Несколько сайтов.
//ПАРСЕР САЙТОВ, КОТОРЫЙ БЕРЕТ ИЗ МАССИВА СТРОКИ С САЙТАМИ, И ЗАПИСЫВАЕТ ИХ В СЛЕД. ПОРЯДКЕ:
// 1) УРЛ САЙТА, 2) НАЗВАНИЕ, 3) Заголовок. Так же мы записываем в файл гл. страницу сайта

//Тут мы используем файл func.php с найстройками cURL-a
$arr_sites = array(
    'https://brainforce.by',
    'https://brainforce.pro',
    'https://onliner.by',
    'https://php.net',
);

pr($arr_sites);

$cookies = "cookie-".microtime(true).".txt";

$arr_sites_stat = array();

foreach ($arr_sites as  $key => $site) {


    $page = get_page('get', $site, array(), $cookies, $site);
    $content = $page['content'];
    $encoding = $page ['encoding'];

    $title = getDataByOrder($content, '<title>','</title>', 1);
    $site_name = str_replace(array('https://', 'http://'), array("",""), $site);
    file_put_contents($site_name.".html", $content);

    $arr_sites_stat[] = array(
        'site' => $site,
        'site_name' => $site_name,
        'title' => $title,
    );

    $arr_sites_stat['site'][$key] = $site;
    $arr_sites_stat['site_name'][$key] = $site_name;
    $arr_sites_stat['title'][$key] = $title;
    echo "<br>";
    echo " Ссылка на сайт: ".$site."<br>" ."Имя сайта: ".$site_name."<br>" ."Заголовок сайта: ".$title."<br>";
}

//РАБОТА С БД MYSQL
// МЫ СОЗДАЛИ НА СЕРВЕРЕ ТАБЛИЦУ С СЫЛКАМИ НА САЙТЫ
// И МОЖЕМ ЗАБИРАТЬ ИХ ДЛЯ РАБОТЫ ПАРСЕРА
$conn = new mysqli("localhost", "borland", "1123", "mytestdb");
if ($conn->connect_error) {
    die("ОшибO4ка: " . $conn->connect_error);
}
$arr_sites = [];
////ПОЛУЧАЕМ ПОЛЯ ИЗ БД И ВЫВОДИМ ИХ
$sql = "SELECT * FROM mysite ";
if($result = $conn->query($sql)){
    $rowsCount = $result->num_rows; // количество полученных строк
    echo "<p>Получено объектов: $rowsCount</p>";
    echo "<table><tr><th>Id</th><th>URL</th></tr>";
    foreach($result as $row){
        $arr_sites[] = $row["url"];
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["url"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    $result->free();
} else{
    echo "Ошибка: " . $conn->error;
}
echo '<br>';
//pr($arr_sites);
$conn->close();

