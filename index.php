<?php // requires
    try
    {
        require_once "php/templates.php";
        require_once "php/classes.php";
    }
    catch(Throwable $t)
    {
        echo '<h1>Error!</h1>';
        echo '<p>A very important file is missing so the page can\'t load properly!</p>';
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
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
        ?>
    </body>
</html>