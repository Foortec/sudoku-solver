<?php
$input = file_get_contents("php://input");

if($input == false || $input == "")
{
    echo "bad input";
    return;
}

$sudoku = json_decode($input, true);

$solved = false;

$sudokuKeys = array_keys($sudoku);
$loopSafetyLimit = 1;
$loopIterator = 0;

echo json_encode($sudoku);
return;

do
{
    for($i=1; $i<=9; ++$i)
    {
        $keys = $sudokuKeys;

        for($j=0; $j<81; ++$j) // set to NULL every "forbidden" sudoku slot, forbidden = the one we can't put the number in for sure
        {
            if($keys[$j] == NULL) // if the slot is already NULL there is no need to do anything
                continue;
    
            if($sudoku[$j] == $i) // if we got the number, clear the whole square it is in, as well as a collumn an a row
            {
                for($k=0; $k<9; $k+=9) // we need to determine which square to wipe out
                {
                    if($j >= $k && $j <= $k+8)
                    {
                        for($l=$k; $l<9; ++$l)
                            $keys[$l] = NULL;
                    }
                }

                for($k=0; $k<9; $k+=3) // we need to determine which row to wipe out
                {
                    if(($j >= $k && $j <= $k+2) || ($j >= $k+9 && $j <= $k+11) || ($j >= $k+18 && $j <= $k+20))
                    {
                        for($l=$k; $l<3; $l+=9)
                            for($m=$l; $m<3; ++$m)
                                $keys[$m] = NULL;
                    }
                }

                for($k=0; $k<9; ++$k) // we need to determine which column to wipe out
                {
                    if(($j >= $k && $j <= $k+6) || ($j >= $k+27 && $j <= $k+33) || ($j >= $k+54 && $j <= $k+60))
                    {
                        for($l=$k; $l<3; $l+=27)
                            for($m=$l; $m<9; $m+=3)
                                $keys[$m] = NULL;
                    }
                }

                continue;
            }

            if($sudoku[$j] != "") // if anything is here, we can't use it as well
                $keys[$j] = NULL;
        }

        $oneSlot = [];
        $slotKey = [];

        for($j=0; $j<9; $j+=9) // check if there is exactly one slot per square left
        {
            for($k=$j; $k<9; ++$k)
            {
                if($keys[$k] != NULL)
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

        for($j=0; $j<9; $j+=9) // put the number into the right fields
        {
            for($k=$j; $k<9; ++$k)
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

    $loopIterator++;
}while(!$solved || $loopSafetyLimit<$loopIterator);

echo json_encode($sudoku);