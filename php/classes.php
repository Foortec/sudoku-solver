<?php
class Sudoku
{
    static public function display() : void
    {
        echo '<section id="main"><section id="sudoku">';
        for($i=0; $i<9; ++$i)
        {
            echo '<section class="sudoku-chunk">';
            for($j=0; $j<9; ++$j)
                echo '<input id="coords' . $i, $j . '" class="sudoku-input" type="number" oninput="if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="1"/>';
            echo '</section>';
        }
        echo '</section></section>';
    }
}