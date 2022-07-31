<?php
require "classes.php";

$sudoku = new Sudoku(file_get_contents("php://input"));

if($sudoku->error || $sudoku->isEmpty() || $sudoku->isFull() || $sudoku->againstTheRules())
{
    header($sudoku->currentHeader);
    return;
}

$sudoku->solve();

echo json_encode($sudoku->getFields());
header($sudoku->currentHeader);