function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function type(value) {
    var regex = /^\[object (\S+?)\]$/;
    var matches = Object.prototype.toString.call(value).match(regex) || [];
  
    return (matches[1] || 'undefined').toLowerCase();
  }

function getList(object) {
    let html = '<ul>';
    
    for (k in object) {
        html += '<li>'
        html += k + " [" + type(object[k]) + "]"
        if (object[k] !== null && (Array.isArray(object[k]) || object[k] instanceof Object)) {
            html += getList(object[k])
        } else {
            html += ": " + object[k]
        }
        html += '</li>'
    }

    html += '</ul>';
    return html;
}

var postButton = document.getElementById('post-btn')

postButton.onclick = function(event) {
    let jsonString = document.getElementById('json-string').value
    if (IsJsonString(jsonString)) {
        let method = document.getElementById('method').value
        let token = document.getElementById('token').value
        saveRequest(method, jsonString, token)
        updateElements();
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
            div.innerHTML += "<p>result: " + result.result + "</p>";

        } else if (xhr.status == 401) {
            alert('Unauthorized')
        }else {
            console.log("Server response: ", xhr.statusText);
        }
    };

    xhr.send(jsonString);
}

function editRequest(method, jsonString, token, id) {
    const xhr = new XMLHttpRequest();

    if (method == "POST") {
        xhr.open(method, "/api/obj/update?id=" + id);
        xhr.setRequestHeader("Content-Type", "application/json");
    } else if (method == "GET") {
        xhr.open(method, "/api/obj/update?id=" + id + "&json=" + jsonString);
    } else {
        console.log('Undefinded method')
        return null;
    }
        
    xhr.setRequestHeader("Authorization", "Bearer " + token);
    
    // обработчик получения ответа сервера
    xhr.onload = () => {
        if (xhr.status == 200) { 
            updateElements();
        } else if (xhr.status == 401) {
            alert('Unauthorized')
        }else {
            console.log("Server response: ", xhr.statusText);
        }  
    };

    xhr.send(jsonString);
}

function deleteRequest(method, token, id) {
    const xhr = new XMLHttpRequest();

    let url = "/api/obj/delete?id=" + id;

    if (method == "POST") {
        xhr.open(method, url);
        xhr.setRequestHeader("Content-Type", "application/json");
    } else if (method == "GET") {
        xhr.open(method, url);
    } else {
        console.log('Undefinded method')
        return null;
    }
        
    xhr.setRequestHeader("Authorization", "Bearer " + token);
    
    // обработчик получения ответа сервера
    xhr.onload = () => {
        if (xhr.status == 200) { 
            updateElements();
        } else if (xhr.status == 401) {
            alert('Unauthorized')
        }else {
            console.log("Server response: ", xhr.statusText);
        }  
    };

    xhr.send();
}

var currentElements = {};

function updateElements() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', "/api/obj/read");
    xhr.onload = () => {
        if (xhr.status == 200) { 
            let result = JSON.parse(xhr.responseText);
            let div = document.getElementById('elements');
            div.innerHTML = "";

            result.forEach(function(item, i, arr) {
                let data = item.data;
                currentElements[item.id] = data;
                div.innerHTML += getList(data);
                div.innerHTML += '<button class="edit-btn" value="' + item.id + '">Edit</button>';
                div.innerHTML += '<button class="delete-btn" value="' + item.id + '">Delete</button>';
            })
           
            document.body.append(div);

            // поместить все текстовые узлы в элемент <span>
            // он занимает только то место, которое необходимо для текста
            for (let li of document.querySelectorAll('li')) {
                let span = document.createElement('span');
                li.prepend(span);
                span.append(span.nextSibling); // поместить текстовый узел внутрь элемента <span>
            }

            //  ловим клики на всём дереве
            let elements = document.getElementsByClassName('tree')
            for (var i = 0; i < elements.length; i++) {
                elements[i].onclick = function(event){
                    if (event.target.tagName != 'SPAN') {
                        return;
                    }
                
                    let childrenContainer = event.target.parentNode.querySelector('ul');
                    if (!childrenContainer) return; // нет детей
                
                    childrenContainer.hidden = !childrenContainer.hidden;
                };
            }

            for (let btn of document.getElementsByClassName('edit-btn')) {
                btn.onclick = function(event) {
                    let id = btn.value
                    div.innerHTML = '<textarea id="edit-field" style="width:1200px; height:600px;">' + JSON.stringify(currentElements[id], null, 4) + '</textarea><br>';
                    div.innerHTML += '<button id="save">Save</button>'
                    div.innerHTML += '<button id="cancel">Cancel</button>'
                    document.getElementById('cancel').onclick = function(event) {
                        updateElements();
                    };
                    document.getElementById('save').onclick = function(event) {
                        let jsonString = document.getElementById('edit-field').value;
                        if (IsJsonString(jsonString)) {
                            let method = document.getElementById('method').value
                            let token = document.getElementById('token').value;
                            editRequest(method, jsonString, token, id);
                        } else {
                            alert('Invalid json string')
                        }
                    };
                }
            }

            for (let btn of document.getElementsByClassName('delete-btn')) {
                btn.onclick = function(event) {
                    let id = btn.value
                    
                    let method = document.getElementById('method').value
                    let token = document.getElementById('token').value;
                    deleteRequest(method, token, id)
                };
            }
        } else {
            console.log("Server response: ", xhr.statusText);
        }
    };
    xhr.send();
}

document.addEventListener("DOMContentLoaded", updateElements);





