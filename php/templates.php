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
        ';
    }

    static public function getHeader() : void
    {
        echo '
            <header>
                <h1>Sudoku solver</h1>
            </header>
        ';
    }

    static public function getSudoku() : void
    {
        $sudoku = new Sudoku();
    }

    static public function getFooter() : void
    {
        echo '
            <footer>
                <h4>https://github.com/Foortec</h4>
            </footer>
        ';
    }

    static public function test() : void
    {
        echo "test";
    }
}