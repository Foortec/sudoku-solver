function eraseSudoku()
{
    const inputsList = document.getElementsByClassName("sudoku-input");
    const inputs = htmlCollectionToArray(inputsList);

    inputs.forEach(element => {
        element.value = null;
        element.classList.remove("solved");
    });
}

function notify(message)
{
    document.getElementById("notification-message").innerHTML = message;
    document.getElementById("notification").style.display = "block";
}

function loadingAnimation(start)
{
    if(start)
    {
        document.getElementById("loading").style.display = "block";
    }
    else
    {
        document.getElementById("loading").style.display = "none";
    }
}

function htmlCollectionToArray(htmlcollection)
{
    return Array.prototype.slice.call(htmlcollection); // reference: https://stackoverflow.com/questions/222841/most-efficient-way-to-convert-an-htmlcollection-to-an-array
}

function solveSudoku()
{
    loadingAnimation(true);

    let xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function()
    {
        if(this.status == 204)
        {
            loadingAnimation(false);
            notify("The sudoku is already full.");
            return;
        }

        if((this.status == 400 && this.responseText == "bad input") || this.responseText == "bad input")
        {
            loadingAnimation(false);
            notify("We can't solve that sudoku. Please check if everything is correct, then try again.");
            return;
        }

        if(this.status != 200 && this.status != 0)
        {
            loadingAnimation(false);
            notify("Something went wrong. Please try again soon.");
            return;
        }

        if(this.readyState == 4 && this.status == 200)
        {
            loadingAnimation(false);
            let solution = JSON.parse(this.responseText);

            console.log(solution);
            
            for(let index = 0; index < inputs.length; index++) {
                inputs[index].value = solution[index];
                if(typeof solution[index] != "string")
                {
                    inputs[index].classList.add("solved");
                }
            }
        }
    };

    const inputsList = document.getElementsByClassName("sudoku-input");
    const inputs = htmlCollectionToArray(inputsList);

    let inputsValues = [];

    for(let index = 0; index < inputs.length; index++) {
        inputsValues[index] = inputs[index].value;
    }

    xmlhttp.open("POST", "php/solve.php", true);
    xmlhttp.setRequestHeader("Content-Type", "application/json");
    xmlhttp.send(JSON.stringify(inputsValues));
}