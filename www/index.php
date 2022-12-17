<?php
require_once __DIR__."/../server/parser.php";
require_once __DIR__."/../server/connection.php";
global $database;
$result = mysqli_query($database, "SELECT * FROM `cards` ORDER BY record_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="styles.css">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
        <title>Новости | BFN News</title>
    </head>
    
    <body> 
        <?php include_once 'header.php';?>
            <h2 class="main_title">Последние новости</h2>
            <?php
            while($news = mysqli_fetch_assoc($result)) {
                echo <<<HTML
                    
                        <div class="post">
                            <div class="post__full_info">
                                <a href ="content.php?id={$news['id']}">
                                    <h2 class="post__title">{$news["title"]}</h2>
                                </a>
                                <div class="post__info">
                                    <a href="sorted.php?rubric={$news['rubric']}">
                                        <div>{$news['rubric']}</div>
                                    </a>
                                    <div>{$news['time']}</div>
                                </div>
                                
                            </div>
                            <img class="post__image" src="{$news['image']}">
                        </div>
                    HTML;
            }
            ?>
            <button class="show_more_btn">Показать ещё</button>
        <?php include_once 'footer.php';?>
    </body>
</html>