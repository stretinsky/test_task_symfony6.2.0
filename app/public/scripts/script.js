function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

var postButton = document.getElementById('post-btn')

postButton.onclick = function(event) {
    let jsonString = document.getElementById('json-string').value;
    if (IsJsonString(jsonString)) {
        let method = document.getElementById('method').value;
        let token = document.getElementById('token').value;
        saveRequest(method, jsonString, token);
    } else {
        alert('Invalid json string')
    }
    
}

function saveRequest(method, jsonString, token) {
    const xhr = new XMLHttpRequest();

    if (method == "POST") {
        xhr.open(method, "/api/obj/create");
        xhr.setRequestHeader("Content-Type", "application/json");
    } else if (method == "GET") {
        xhr.open(method, "/api/obj/create?json=" + jsonString);
    } else {
        console.log('Undefinded method')
        return null;
    }
        
    xhr.setRequestHeader("Authorization", "Bearer " + token);
    
    // обработчик получения ответа сервера
    xhr.onload = () => {
        if (xhr.status == 200) { 
            let result = JSON.parse(xhr.responseText);

            let div = document.getElementById('result');
            div.innerHTML = "<p>ID: " + result.id + "</p>";
            div.innerHTML += "<p>Result: " + result.result + "</p>";

        } else if (xhr.status == 401) {
            alert('Unauthorized')
        }else {
            console.log("Server response: ", xhr.statusText);
        }
    };

    xhr.send(jsonString);
}





