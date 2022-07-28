<?php
$input = file_get_contents("php://input");

if($input == false || $input == "")
{
    echo "bad input";
    header("HTTP/1.1 400 Bad Request");
    return;
}

$sudoku = json_decode($input, true);
unset($input);

$sudokuEmpty = true;
$sudokuFull = true;

for($i=0; $i<81; ++$i)
{
    if($sudoku[$i] != "")
        $sudokuEmpty = false;
    else
        $sudokuFull = false;
}

if($sudokuEmpty)
{
    echo "bad input";
    header("HTTP/1.1 400 Bad Request");
    return;
}

if($sudokuFull)
{
    header("HTTP/1.1 204 No Content");
    return;
}
unset($sudokuFull, $sudokuEmpty);

$solved = false;

$sudokuKeys = array_keys($sudoku);

do
{
    $sudokuCopy = $sudoku;

    for($i=1; $i<=9; ++$i)
    {
        $keys = $sudokuKeys;

        for($j=0; $j<81; ++$j) // set to NULL every "forbidden" sudoku slot, forbidden = the one we can't put the number in for sure
        {
            if($keys[$j] == "") // if the slot is already NULL there is no need to do anything
            {
                continue;
            }

            if($sudoku[$j] == $i) // if we got the number, clear the whole square it is in, as well as a collumn an a row
            {
                for($k=0; $k<=72; $k+=9) // we need to determine which square to wipe out
                {
                    if($j >= $k && $j <= $k+8)
                    {
                        for($l=$k; $l<$k+9; ++$l)
                            $keys[$l] = NULL;
                    }
                }

                for($k=0; $k<=54; $k+=27) // we need to determine which row to wipe out
                {
                    for($l=$k; $l<=$k+6; $l+=3)
                    {
                        if(($j >= $l && $j <= $l+2) || ($j >= $l+9 && $j <= $l+11) || ($j >= $l+18 && $j <= $l+20))
                        {
                            for($m=$l; $m<=$l+18; $m+=9)
                                for($n=$m; $n<$m+3; ++$n)
                                    $keys[$n] = NULL;
                        }
                    }
                }

                for($k=0; $k<=18; $k+=9) // we need to determine which column to wipe out
                {
                    for($l=$k; $l<=$k+2; ++$l)
                    {
                        if(($j == $l || $j == $l+3 || $j == $l+6) || ($j == $l+27 || $j == $l+30 || $j == $l+33) || ($j == $l+54 || $j == $l+57 || $j == $l+60))
                        {
                            for($m=$l; $m<=$l+54; $m+=27)
                                for($n=$m; $n<$m+7; $n+=3)
                                    $keys[$n] = NULL;
                        }
                    }
                }
                continue;
            }

            if($sudoku[$j] !== "") // if anything is here, we can't use it as well
            {
                $keys[$j] = NULL;
            }
        }

        $oneSlot = [];
        $slotKey = [];

        for($j=0; $j<=72; $j+=9) // check if there is exactly one slot per square left
        {
            for($k=$j; $k<$j+9; ++$k)
            {
                if($keys[$k] !== NULL)
                {
                    if($oneSlot[$j])
                    {
                        $oneSlot[$j] = false;
                        unset($slotKey[$j]);
                        break;
                    }
                    $oneSlot[$j] = true;
                    $slotKey[$j] = $k;
                }
            }
        }

        for($j=0; $j<=72; $j+=9) // put the number into the right fields
        {
            for($k=$j; $k<$j+9; ++$k)
            {
                if($oneSlot[$j])
                {
                    $sudoku[$slotKey[$j]] = $i;
                    break;
                }
            }
        }
    }

    if(!in_array("", $sudoku))
        $solved = true;

    if(count(array_diff_assoc($sudokuCopy, $sudoku)) === 0)
        break;

}while(!$solved);

echo json_encode($sudoku);
header("HTTP/1.1 200 OK");