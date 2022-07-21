<?php
class Templates
{
    static public function getMetadata() : void
    {
        echo '
            <meta charset="UTF-8">
            <meta name="description" content="Classic 9x9 sudoku solver">
            <meta name="keywords" content="sudoku, solver, 9x9">
            <meta name="author" content="Piotr Czajka">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="theme-color" content="#808080">
        ';
    }

    static public function getFonts() : void
    {
        echo '
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap" rel="stylesheet">
            <link href="https://fonts.googleapis.com/css2?family=Mali:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet"> 
        ';
    }

    static public function getHeader() : void
    {
        echo '
            <header id="site-header">
                <h1 id="site-title">Sudoku solver</h1>
                <p id="site-version">0.2.2 test version</p>
            </header>
        ';
    }

    static public function getSudoku() : void
    {
        Sudoku::display();
    }

    static public function getButtons() : void
    {
        echo '
            <section id="buttons">
                <input id="solve" type="button" value="Solve" onclick="solveSudoku();"/>
                <input id="erase" type="button" value="Erase" onclick="eraseSudoku();"/>
            </section>
        ';
    }

    static public function getNotificationWindow() : void
    {
        echo '
            <div id="notification">
                <span style="float: right; padding: 15px; cursor: pointer;" onclick="document.getElementById(\'notification\').style.display = \'none\';">Close</span>
                <h3>Notification</h3>
                <hr/>
                <p id="notification-message"></p>
            </div>
        ';
    }

    static public function getLoadingAnimation() : void
    {
        echo '
            <div id="loading">
                <img style="width: 100%;" src="imgs/loading.png" alt="loading..."/>
            </div>
        ';
    }

    static public function getFooter() : void
    {
        echo '
            <footer id="site-footer">
                <span>
                    &copy; 2022
                    <a href="https://github.com/Foortec/sudoku-solver" target="_blank">github.com/Foortec/sudoku-solver</a>
                </span>
            </footer>
        ';
    }
}