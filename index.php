<?php // requires
    try
    {
        require_once "php/templatesX.php";
        require_once "php/classes.php";
    }
    catch(Throwable $t)
    {
        echo '<section style="padding: 5vh 5vw; background-color: #800000aa; color: white; border: 1px solid red; border-radius: 25px; box-shadow: 0 0 10px black;">';
        echo '<h1>Error!</h1>';
        echo '<p>A very important file is missing so the page can\'t load properly!</p>';
        echo '</section>';
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Sudoku Solver</title>
        <?php
            templates::getMetadata();
            templates::getFonts();
        ?>
        <link rel="stylesheet" href="css/style.css">
        <script src="js/scripts.js" type="text/javascript"></script>
    </head>
    <body>
        <?php 
            templates::getHeader();
            templates::getSudoku();
            templates::getButtons();
            templates::getFooter();
            templates::getNotificationWindow();
            templates::getLoadingAnimation();
        ?>
    </body>
</html>