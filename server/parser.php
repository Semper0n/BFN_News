<?php
    header('refresh: 300');

    require_once __DIR__."/phpquery-onefile.php";

    function parser($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_USERAGENT, "spider");
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
    
    /*=================================    Lenta parser      =================================== */

    $linkLenta = parser("https://lenta.ru/parts/news/");
    //var_dump($linkLenta);
    $pqLenta = phpquery::newDocument($linkLenta);
    
    $arrLinksCardsLenta = array();
    $listLinks = $pqLenta->find('a.card-full-news');
    foreach ($listLinks as $link) {
        if (substr(pq($link)->attr('href'), 0, 4) != "http")
        $arrLinksCardsLenta[] = pq($link)->attr('href');
    }

    $arrListCardsLenta = array();

    $months = [
        "января" => "01",
        "февраля" => "02",
        "марта" => "03",
        "апреля" => "04",
        "мая" => "05",
        "июня" => "06",
        "июля" => "07",
        "августа" => "08",
        "сентября" => "09",
        "октября" => "10",
        "ноября" => "11",
        "декабря" => "12",
    ];

    foreach ($arrLinksCardsLenta as $card) {

        $linkPage = "https://lenta.ru".$card;
        
        $resultCard = parser($linkPage);


        $pqLenta = phpquery::newDocument($resultCard);
        //var_dump($pqLenta);
        $arrDate = explode(" ", $pqLenta->find('.topic-header__time')->text());
        //var_dump($arrDate);
        if (strlen($arrDate[1]) == 1){
            $arrDate[1] = "0" . $arrDate[1];
        }
        $date = $arrDate[3] . $months["$arrDate[2]"] . $arrDate[1] . mb_eregi_replace("[^a-zа-яё0-9 ]", '', $arrDate[0]) . "00";
    
        //var_dump($date);
        //var_dump($pqLenta->find('.topic-body__content-text')->text());
        //var_dump($pqLenta->find('.topic-body__title')->text());
        //var_dump($pqLenta->find('.picture__image')->attr('src'));

        $arrListCardsLenta[] = [
            "title" => $pqLenta->find('.topic-body__title')->text(),
            "time" => $pqLenta->find('.topic-header__time')->text(),
            "link" => $linkPage,
            "rubric" => $pqLenta->find('.topic-header__rubric')->text(),
            "record_date" => $date,
            "description" => $pqLenta->find('.topic-body__content-text')->html(),
            "image" => $pqLenta->find('.picture__image')->attr('src'),
        ];
    }

    require __DIR__."/../server/vendor/autoload.php";
    use Krugozor\Database\Mysql;

    $db = Mysql::create("localhost", "root", "")->setErrorMessagesLang('ru')->setDatabaseName("newsdb")->setCharset("utf8");

    foreach ($arrListCardsLenta as $card) {
        $card_title = $card['title'];
        $mysqli = mysqli_connect("localhost", "root", "", "newsdb");
        $result = mysqli_query ($mysqli, "SELECT id FROM cards WHERE title = '$card_title'");
        //var_dump(mysqli_num_rows($result));
        if (!mysqli_num_rows($result)) {
            $db->query('INSERT INTO `cards` SET ?As', $card);
        }
    
    }

    /*============================      Izvestiya parser      ==============================*/
    
    $linkIz = trim(parser("https://iz.ru/feed/"));
    //var_dump($linkIz);
    $pqIz = phpquery::newDocument($linkIz);
    
    $arrLinksCardsIz = array();
    $listLinks = $pqIz->find('a.lenta_news__day__list__item');
    
    foreach ($listLinks as $link) {
        $arrLinksCardsIz[] = pq($link)->attr('href');
        
    }

    $arrListCardsIz = array();

    foreach ($arrLinksCardsIz as $card) {

        $linkPage = "https://iz.ru".$card;
        
        $resultCard = parser($linkPage);

        $pqIz = phpquery::newDocument($resultCard);
        $arrDate = explode(" ", $pqIz->find('.article_page__left__top__time__label div time')->text());
        if (strlen($arrDate[0]) == 1){
            $arrDate[0] = "0" . $arrDate[0];
        }
        //var_dump($arrDate);
        $date = mb_eregi_replace("[^a-zа-яё0-9 ]", '', $arrDate[2]) . $months["$arrDate[1]"] . $arrDate[0] . mb_eregi_replace("[^a-zа-яё0-9 ]", '', $arrDate[3]) . "00";
        //var_dump($date);

        $arrRubrics = [
            "Страна" => "Россия",
            "Армия" => "Силовые структуры",
            "Политика" => "Мир",
            "Общество" => "Среда обитания",
            "Происшествия" => "Россия",
            "Стиль" => "Ценности",
            "Авто" => "Наука и техника",
            "Наука" => "Наука и техника",
            "Интернет" => "Интернет и СМИ",
            "Туризм" => "Путешествия",
            "Недвижимость" => "Среда обитания",
            "Экономика" => "Экономика",
            "Мир" => "Мир",
            "Культура" => "Культура",
            "Спорт" => "Спорт",
            "Здоровье" => "Забота о себе",
            "Пресс-релизы" => "Среда обитания",
        ];

        $rubric = $arrRubrics[$pqIz->find('.rubrics_btn div a')->text()];
        //var_dump('https:' . $pqIz->find('.big_photo__img link')->attr('href'));
        $time = explode(",", $pqIz->find('.article_page__left__top__time__label div time')->text());
        
        $time = trim($time[1]) . ', ' . $time[0];
        //var_dump($time);
        $arrListCardsIz[] = [
            "title" => $pqIz->find('.m-t-10 span')->text(),
            "time" => $time,
            "link" => $linkPage,
            "rubric" => $rubric,
            "record_date" => $date,
            "description" => $pqIz->find('.text-article__inside div div p')->html(),
            "image" => 'https:' . $pqIz->find('.big_photo__img link')->attr('href'),
        ];
    }
    //var_dump($arrListCardsIz);
    foreach ($arrListCardsIz as $card) {
        $card_title = $card['title'];
        $mysqli = mysqli_connect("localhost", "root", "", "newsdb");
        $result = mysqli_query ($mysqli, "SELECT id FROM cards WHERE title = '$card_title'");
        //var_dump(mysqli_num_rows($result));
        if (!mysqli_num_rows($result)) {
            $db->query('INSERT INTO `cards` SET ?As', $card);
        }
        
    }
    

?>