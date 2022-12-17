<?php
    require_once __DIR__."/../server/connection.php";
    require_once __DIR__."/../server/parser.php";
    $link = $_GET['id'];
    global $database;
    $result = mysqli_query($database, "SELECT * FROM `cards` WHERE id = '$link'");
    $news = mysqli_fetch_assoc($result);
    $rubric = $news['rubric'];
    $title = $news['title'];
    $result2 = mysqli_query($database, "SELECT * FROM `cards` WHERE rubric = '$rubric' ORDER BY record_date DESC LIMIT 11");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
        <title><?php echo $news["title"] . " | BFN News";?></title>
    </head>

    <body>
        <?php include_once 'header.php';?>
        <section class="content">
            <?php

            if ($news['image'] == "") {
                echo <<<HTML
                <div class="content_left">
                    <div class="content__info">
                        <a href="sorted.php?rubric={$news['rubric']}">
                            <div>{$news['rubric']}</div>
                        </a>
                        <div>{$news['time']}</div>
                    </div>
                    <h2 class="content__title">{$news['title']}</h2>
                    <div class="content__description">
                        {$news['description']}
                    </div>
                </div>
            HTML;
            } else {
                echo <<<HTML
                <div class="content_left">
                    <div class="content__info">
                        <a href="sorted.php?rubric={$news['rubric']}">
                            <div>{$news['rubric']}</div>
                        </a>
                        <div>{$news['time']}</div>
                    </div>
                    <h2 class="content__title">{$news['title']}</h2>
                    <img class="content__image" src="{$news['image']}">
                    <div class="content__description">
                        
                        {$news['description']}
                    </div>
                    <div class="timeline">
                    </div>
                </div>
            HTML;
            }
            ?>

            <div class="content__right">
                <h3 class="content__right__title">Смотрите также</h3>
            <?php
            while($news = mysqli_fetch_assoc($result2)) {
                if ($news['title'] != $title) {
                    echo <<<HTML
                    
                        <div class="content__post">
                            <div class="content__post__full_info">
                                <a href ="content.php?id={$news['id']}">
                                    <h2 class="content__post__title">{$news["title"]}</h2>
                                </a>
                                <div class="content__post__info">
                                    <div>{$news['time']}</div>
                                </div>
                                
                            </div>
                            <img class="content__post__image" src="{$news['image']}">
                        </div>
                    
                    HTML;
                }
                
            }
            
            ?>
            </div>
        </section>
        <?php include_once 'footer.php';?>
    </body>
</html>