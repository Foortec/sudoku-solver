<?php
class Sudoku
{
    private array $fields;
    private bool $solved = false;

    public bool $error = false;
    public string $currentHeader;

    public function __construct(string|bool $sudokuFields)
    {
        if($sudokuFields == false || $sudokuFields == "")
        {
            echo "bad input";
            $this->error = true;
            $this->currentHeader = "HTTP/1.1 400 Bad Request";
            return;
        }

        $this->fields = json_decode($sudokuFields, true);
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function isEmpty() : bool
    {
        $empty = true;
        for($i=0; $i<81; ++$i)
        {
            if($this->fields[$i] != "")
                $empty = false;
        }

        if($empty)
        {
            echo "bad input";
            $this->error = true;
            $this->currentHeader = "HTTP/1.1 400 Bad Request";
            return true;
        }
        return false;
    }

    public function isFull() : bool
    {
        $full = true;
        for($i=0; $i<81; ++$i)
        {
            if($this->fields[$i] == "")
                $full = false;
        }

        if($full)
        {
            $this->currentHeader = "HTTP/1.1 204 No Content";
            return true;
        }
        return false;
    }

    public function isSolved() : bool
    {
        return $this->solved;
    }

    private function hasWrongValue(int $fieldIndex) : bool
    {
        if($this->fields[$fieldIndex] != "" && preg_match(";[1-9];", $this->fields[$fieldIndex]) === 0)
            return true;
        return false;
    }

    private function numberRepeatsInSquare(int $fieldIndex) : bool
    {
        $theNumber = $this->fields[$fieldIndex];
        for($i=0; $i<=72; $i+=9)
        {
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
            {
                $numRepetition = 0;
                for($j=$i; $j<$i+9; ++$j)
                {
                    if($this->fields[$j] == $theNumber)
                        $numRepetition++;
                }
                if($numRepetition >= 2)
                    return true;
            }
        }
        return false;
    }

    private function numberRepeatsInRow(int $fieldIndex) : bool
    {
        $theNumber = $this->fields[$fieldIndex];
        for($i=0; $i<=54; $i+=27)
        {
            for($j=$i; $j<=$i+6; $j+=3)
            {
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                {
                    $numRepetition = 0;
                    for($k=$j; $k<=$j+18; $k+=9)
                    {
                        for($l=$k; $l<$k+3; ++$l)
                        {
                            if($this->fields[$l] == $theNumber)
                                $numRepetition++;
                        }
                    }
                    if($numRepetition >= 2)
                        return true;
                }
            }
        }
        return false;
    }

    private function numberRepeatsInColumn(int $fieldIndex) : bool
    {
        $theNumber = $this->fields[$fieldIndex];
        for($i=0; $i<=18; $i+=9)
        {
            for($j=$i; $j<=$i+2; ++$j)
            {
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                {
                    $numRepetition = 0;
                    for($k=$j; $k<=$j+54; $k+=27)
                    {
                        for($l=$k; $l<$k+7; $l+=3)
                        {
                            if($this->fields[$l] == $theNumber)
                                $numRepetition++;
                        }
                    }
                    if($numRepetition >= 2)
                        return true;
                }
            }
        }
        return false;
    }

    public function againstTheRules() : bool
    {
        $againstTheRules = false;

        for($i=1; $i<=9; ++$i)
        {
            for($j=0; $j<81; ++$j) // go thru all fields
            {
                $againstTheRules = $this->hasWrongValue($j);
                if($againstTheRules)
                    break;

                if($fields[$j] == $i) // if we got the number, check for the same num in the whole square it is in, as well as a collumn and a row
                {
                    $againstTheRules = $this->numberRepeatsInSquare($j);
                    if($againstTheRules)
                        break;

                    $againstTheRules = $this->numberRepeatsInRow($j);
                    if($againstTheRules)
                        break;

                    $againstTheRules = $this->numberRepeatsInColumn($j);
                    if($againstTheRules)
                        break;
                }
            }
            if($againstTheRules)
                break;
        }

        if($againstTheRules)
        {
            echo "bad input";
            $this->error = true;
            $this->currentHeader = "HTTP/1.1 400 Bad Request";
            return true;
        }
        return false;
    }

    private function excludeSquare(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=72; $i+=9)
        {
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
            {
                for($j=$i; $j<$i+9; ++$j)
                    $keys[$j] = NULL;
            }
        }
    }

    private function excludeRow(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=54; $i+=27)
        {
            for($j=$i; $j<=$i+6; $j+=3)
            {
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                {
                    for($k=$j; $k<=$j+18; $k+=9)
                        for($m=$k; $m<$k+3; ++$m)
                            $keys[$m] = NULL;
                }
            }
        }
    }

    private function excludeColumn(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=18; $i+=9) // we need to determine which column to wipe out
        {
            for($j=$i; $j<=$i+2; ++$j)
            {
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                {
                    for($k=$j; $k<=$j+54; $k+=27)
                        for($l=$k; $l<$k+7; $l+=3)
                            $keys[$l] = NULL;
                }
            }
        }
    }

    private function fillFields(array $keys, int $theNumber) : void
    {
        $oneSlot = $slotKey = [];

        for($i=0; $i<=72; $i+=9) // check if there is exactly one slot per square left
        {
            for($j=$i; $j<$i+9; ++$j)
            {
                if($keys[$j] !== NULL)
                {
                    if($oneSlot[$i])
                    {
                        $oneSlot[$i] = false;
                        unset($slotKey[$i]);
                        break;
                    }
                    $oneSlot[$i] = true;
                    $slotKey[$i] = $j;
                }
            }
        }

        for($i=0; $i<=72; $i+=9) // put the number into the right fields
        {
            for($j=$i; $j<$i+9; ++$j)
            {
                if($oneSlot[$i])
                {
                    $this->fields[$slotKey[$i]] = $theNumber;
                    break;
                }
            }
        }
    } 

    public function solve() : void
    {
        $sudokuKeys = array_keys($this->fields);

        do
        {
            $sudokuCopy = $this->fields;

            for($i=1; $i<=9; ++$i)
            {
                $keys = $sudokuKeys;

                for($j=0; $j<81; ++$j) // set to NULL every "forbidden" sudoku field's key, forbidden = the one we can't put the number in for sure
                {
                    if($keys[$j] == "") // if the slot is already NULL there is no need to do anything
                        continue;

                    if($this->fields[$j] == $i) // if we got the number, exclude the whole square it is in, as well as a collumn and a row
                    {
                        $this->excludeSquare($keys, $j);
                        $this->excludeRow($keys, $j);
                        $this->excludeColumn($keys, $j);
                        continue;
                    }

                    if($this->fields[$j] !== "") // if anything is here, we can't use it as well
                        $keys[$j] = NULL;
                }
                $this->fillFields($keys, $i);
            }

            if(!in_array("", $this->fields))
                $this->solved = true;

            if(count(array_diff_assoc($sudokuCopy, $this->fields)) === 0) // if nothing changed, it means the loop will go on forever (probably sudoku is too hard)
                break;

        }while(!$this->solved);
        $this->currentHeader = "HTTP/1.1 200 OK";
    }

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