function eraseSudoku()
{
    const inputsList = document.getElementsByClassName("sudoku-input");
    const inputs = Array.prototype.slice.call(inputsList); // reference: https://stackoverflow.com/questions/222841/most-efficient-way-to-convert-an-htmlcollection-to-an-array

    inputs.forEach(element => {
        element.value = null;
    });
}

function notify(message)
{
    document.getElementById("notification-message").innerHTML = message;
    document.getElementById("notification").style.display = "block";
}

function solveSudoku()
{
    // maybe some loading animation
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function()
    {
        if(this.status != 200 && this.status != 0)
        {
            notify("Something went wrong. Please try again soon.");
            return;
        }

        if(this.responseText == "bad input")
        {
            notify("We can't solve that sudoku. Please check if everything is correct, then try again.");
            return;
        }

        if(this.readyState == 4 && this.status == 200)
        {
            // let solution = JSON.parse(this.responseText);
            let solution = this.responseText;
            // console.log(solution);
        }
    };

    const inputsList = document.getElementsByClassName("sudoku-input");
    const inputs = Array.prototype.slice.call(inputsList);

    let inputsValues = [];

    for(let index = 0; index < inputs.length; index++) {
        inputsValues[index] = inputs[index].value;
    }

    const json = JSON.stringify(inputsValues);

    xmlhttp.open("POST", "php/solve.php", true);
    xmlhttp.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xmlhttp.send(json);
}