<?php
class Sudoku
{
    private array $fields;
    private bool $solved = false;

    public bool $error = false;
    public string $currentHeader;

    const HTTP_200 = "HTTP/1.1 200 OK";
    const HTTP_204 = "HTTP/1.1 204 No Content";
    const HTTP_400 = "HTTP/1.1 400 Bad Request";

    public function __construct(string|bool $sudokuFields)
    {
        if($this->fields = json_decode($sudokuFields, true))
        return;

        $this->error = true;
        $this->currentHeader = self::HTTP_400;
    }

    public function getFields() : array
    {
        return $this->fields;
    }

    public function isEmpty() : bool
    {
        $fields = $this->fields;
        for($i=0; $i<81; ++$i)
            if(!empty($fields[$i]))
                return false;
 
        $this->error = true;
        $this->currentHeader = self::HTTP_400;
        return true;
    }

    public function isFull() : bool
    {
        $fields = $this->fields;
        for($i=0; $i<81; ++$i)
            if(empty($fields[$i]))
                return false;
 
        $this->currentHeader = self::HTTP_204;
        return true;
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
        $fields = $this->fields;
        for($i=0; $i<=72; $i+=9)
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
            {
                $numRepetition = 0;
                for($j=$i; $j<$i+9; ++$j)
                    if($fields[$j] == $theNumber)
                    {
                        $numRepetition++;
                        if($numRepetition >= 2)
                            return true;
                    }
            }
        return false;
    }

    private function numberRepeatsInRow(int $fieldIndex) : bool
    {
        $theNumber = $this->fields[$fieldIndex];
        $fields = $this->fields;
        for($i=0; $i<=54; $i+=27)
            for($j=$i; $j<=$i+6; $j+=3)
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                {
                    $numRepetition = 0;
                    for($k=$j; $k<=$j+18; $k+=9)
                        for($l=$k; $l<$k+3; ++$l)
                            if($fields[$l] == $theNumber)
                            {
                                $numRepetition++;
                                if($numRepetition >= 2)
                                    return true;
                            }
                }
        return false;
    }

    private function numberRepeatsInColumn(int $fieldIndex) : bool
    {
        $theNumber = $this->fields[$fieldIndex];
        $fields = $this->fields;
        for($i=0; $i<=18; $i+=9)
            for($j=$i; $j<=$i+2; ++$j)
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                {
                    $numRepetition = 0;
                    for($k=$j; $k<=$j+54; $k+=27)
                        for($l=$k; $l<$k+7; $l+=3)
                            if($fields[$l] == $theNumber)
                            {
                                $numRepetition++;
                                if($numRepetition >= 2)
                                    return true;
                            }
                }
        return false;
    }

    public function againstTheRules() : bool
    {
        $fields = $this->fields;
        for($i=1; $i<=9; ++$i)
        {
            $breaked = false;
            for($j=0; $j<81; ++$j) // go thru all fields
            {
                if($this->hasWrongValue($j))
                {
                    $breaked = true;
                    break;
                }
 
                if($fields[$j] == $i) // if we got the number, check for the same num in the whole square it is in, as well as a collumn and a row
                {
                    if($this->numberRepeatsInSquare($j))
                    {
                        $breaked = true;
                        break;
                    }
 
                    if($this->numberRepeatsInRow($j))
                    {
                        $breaked = true;
                        break;
                    }
 
                    if($this->numberRepeatsInColumn($j))
                    {
                        $breaked = true;
                        break;
                    }
                }
            }
            if($breaked)
                break;
        }
 
        if($breaked)
        {
            $this->error = true;
            $this->currentHeader = self::HTTP_400;
            return true;
        }
        return false;
    }

    private function excludeSquare(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=72; $i+=9)
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
                for($j=$i; $j<$i+9; ++$j)
                    $keys[$j] = NULL;
    }

    private function excludeRow(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=54; $i+=27)
            for($j=$i; $j<=$i+6; $j+=3)
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                    for($k=$j; $k<=$j+18; $k+=9)
                        for($m=$k; $m<$k+3; ++$m)
                            $keys[$m] = NULL;
    }

    private function excludeColumn(array &$keys, int $fieldIndex) : void
    {
        for($i=0; $i<=18; $i+=9)
            for($j=$i; $j<=$i+2; ++$j)
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                    for($k=$j; $k<=$j+54; $k+=27)
                        for($l=$k; $l<$k+7; $l+=3)
                            $keys[$l] = NULL;
    }

    private function fillFields(array $keys, int $theNumber) : void
    {
        $oneSlot = $slotKey = [];

        for($i=0; $i<=72; $i+=9) // check if there is exactly one slot per square left
            for($j=$i; $j<$i+9; ++$j)
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

        for($i=0; $i<=72; $i+=9) // put the number into the right fields
            for($j=$i; $j<$i+9; ++$j)
                if($oneSlot[$i])
                {
                    $this->fields[$slotKey[$i]] = $theNumber;
                    break;
                }
    }

    private function numberIsInTheSquare(int $fieldIndex,int $theNumber) : bool
    {
        $fields = $this->fields;
        for($i=0; $i<=72; $i+=9)
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
                for($j=$i; $j<$i+9; ++$j)
                    if($fields[$j] == $theNumber)
                        return true;
        return false;
    }

    private function numberIsInTheRow(int $fieldIndex,int $theNumber) : bool
    {
        $fields = $this->fields;
        for($i=0; $i<=54; $i+=27)
            for($j=$i; $j<=$i+6; $j+=3)
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                    for($k=$j; $k<=$j+18; $k+=9)
                        for($l=$k; $l<$k+3; ++$l)
                            if($fields[$l] == $theNumber)
                                return true;
        return false;
    }

    private function numberIsInTheColumn(int $fieldIndex,int $theNumber) : bool
    {
        $fields = $this->fields;
        for($i=0; $i<=18; $i+=9)
            for($j=$i; $j<=$i+2; ++$j)
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                    for($k=$j; $k<=$j+54; $k+=27)
                        for($l=$k; $l<$k+7; $l+=3)
                            if($fields[$l] == $theNumber)
                                return true;
        return false;
    }

    private function erasePossibleNums(int $fieldIndex, array &$fieldsPossible) : void
    {
        $theNumber = $this->fields[$fieldIndex];

        for($i=0; $i<=72; $i+=9) // erase possible nums from the square
            if($fieldIndex >= $i && $fieldIndex <= $i+8)
                for($j=$i; $j<$i+9; ++$j)
                    $fieldsPossible[$j][$theNumber] = false;
        
        for($i=0; $i<=54; $i+=27) // erase possible nums from the row
            for($j=$i; $j<=$i+6; $j+=3)
                if(($fieldIndex >= $j && $fieldIndex <= $j+2) || ($fieldIndex >= $j+9 && $fieldIndex <= $j+11) || ($fieldIndex >= $j+18 && $fieldIndex <= $j+20))
                    for($k=$j; $k<=$j+18; $k+=9)
                        for($l=$k; $l<$k+3; ++$l)
                            $fieldsPossible[$l][$theNumber] = false;
        
        for($i=0; $i<=18; $i+=9) // erase possible nums from the column
            for($j=$i; $j<=$i+2; ++$j)
                if(($fieldIndex == $j || $fieldIndex == $j+3 || $fieldIndex == $j+6) || ($fieldIndex == $j+27 || $fieldIndex == $j+30 || $fieldIndex == $j+33) || ($fieldIndex == $j+54 || $fieldIndex == $j+57 || $fieldIndex == $j+60))
                    for($k=$j; $k<=$j+54; $k+=27)
                        for($l=$k; $l<$k+7; $l+=3)
                            $fieldsPossible[$l][$theNumber] = false;
    }

    private function exclusionAlgorithm() : void // algorithm 1.0
    {
        $solved = $this->solved;
        $fields = $this->fields;
        $sudokuKeys = array_keys($this->fields);
        do
        {
            $sudokuCopy = $fields;
 
            for($i=1; $i<=9; ++$i)
            {
                $keys = $sudokuKeys;
 
                for($j=0; $j<81; ++$j) // set to NULL every "forbidden" sudoku field's key, forbidden = the one we can't put the number in for sure
                {
                    if($keys[$j] == "") // if the slot is already NULL there is no need to do anything
                        continue;
 
                    if($fields[$j] == $i) // if we got the number, exclude the whole square it is in, as well as a collumn and a row
                    {
                        $this->excludeSquare($keys, $j);
                        $this->excludeRow($keys, $j);
                        $this->excludeColumn($keys, $j);
                        continue;
                    }
 
                    if($fields[$j] !== "") // if anything is here, we can't use it as well
                        $keys[$j] = NULL;
                }
                $this->fillFields($keys, $i);
            }
 
            if(!in_array("", $fields))
                $solved = true;
 
            if(count(array_diff_assoc($sudokuCopy, $fields)) === 0) // if nothing changed, it means the loop will go on forever (probably sudoku is too hard)
                break;
 
        }while(!$solved);
        $this->fields = $fields;
        $this->solved = $solved;
    }

    private function possibilityAlgorithm() : void // algorithm 2.0
    {
        $fields = $this->fields;
        do
        {
            $fieldsPossible = array_map(function($field) { if($field != "") return false; else return array(1 => false, 2 => false, 3 => false, 4 => false, 5 => false, 6 => false, 7 => false, 8 => false, 9 => false); }, $fields);
 
            for($i=0; $i<81; ++$i) // note possible numbers
                for($j=1; $j<=9; ++$j) // check for all numbers 1-9
                {
                    if($this->numberIsInTheSquare($i, $j) || $this->numberIsInTheRow($i, $j) || $this->numberIsInTheColumn($i, $j)) // if number is in the square, row or column, can not be not possible
                        continue;
                    $fieldsPossible[$i][$j] = true;
                }
 
            do
            {
                $sudokuCopy = $fields;
 
                for($i=1; $i<=9; ++$i)
                    for($j=0; $j<81; ++$j)// search for field where only possible number is $i, then insert $i in the field and erase possible $i numbers from the square, row and the column
                    {
                        if($fields[$j] != "" || $fieldsPossible[$j][$i] != true)
                            continue;
 
                        $possibleCounter = 0;
                        for($k=1; $k<=9; ++$k)
                        {
                            if($fieldsPossible[$j][$k] == true)
                                $possibleCounter++;
                        }
 
                        if($possibleCounter != 1)
                            continue;
 
                        $fields[$j] = $i;
                        $this->erasePossibleNums($j, $fieldsPossible);
                    }
 
                if($this->solved)
                {
                    $this->currentHeader = self::HTTP_200;
                    return;
                }
 
            }while($sudokuCopy != $this->fields);
 
            $this->fields = $fields; // save changes made in fields
            $sudokuCopy = $fields;
            $this->exclusionAlgorithm(); // algorithm 1.0, last chance
 
        }while($sudokuCopy != $this->fields); // we need to check this->fields because exlusionAlgorithm() makes a change in it
    }

    public function solve() : void
    {
        $this->exclusionAlgorithm(); // algorithm 1.0
 
        if($this->solved)
        {
            $this->currentHeader = self::HTTP_200;
            return;
        }
 
        $this->possibilityAlgorithm(); // algorithm 2.0
 
        $this->currentHeader = self::HTTP_200;
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