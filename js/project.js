
function createValidator(name, updateState) {
    var input = document.getElementById(name);
    if (input == null) {
        alert("Element #" + name + " not found!"); 
    }

    var error = document.getElementById(name + "_error");
    if (error == null) {
        alert("Element #" + name + "_name not found!"); 
    }           

    var x = {
        input: input,
        error: error,
        valid: false
    };
    
    function handleEvent(event) {
        event.preventDefault();
        x.valid = event.target.validity.valid;
        updateState();
    }

    input.addEventListener('invalid', handleEvent);
    input.addEventListener('change', handleEvent);
    input.addEventListener('keyup', handleEvent);

    return x;
}

function createRadioValidator(name, updateState) {
    var input = $("[name="+name+"]");
    if (input.length == 0) {
        alert("Element [name=" + name + "] not found!"); 
    }            
    var error = document.getElementById(name + "_error");
    if (error == null) {
        alert("Element #" + name + "_name not found!"); 
    }

    var x = {
        input: input,
        error: error,
        valid: false
    };
    
    function handleEvent(event) {
        x.valid = input.val() != "";
        updateState();
    }

    input.on("change", handleEvent);
    return x;
}