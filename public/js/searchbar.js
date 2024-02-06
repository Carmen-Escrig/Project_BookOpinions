//Variables
var selectedElement = -1;
var results = null;
var cacheResults = {};

//Ajax 
var XMLHttpRequestObject = false;

if (window.XMLHttpRequest) {
    XMLHttpRequestObject = new XMLHttpRequest();
}
if (!XMLHttpRequestObject) {
    alert("No ha sido posible crear una instancia de XMLHttpRequest");
}

//Formatear la lista para marcar el elemento seleccionado
Array.prototype.formatList = function () {
    var ul = $('<ul></ul>');

    for (var i = 0; i < this.length; i++) {
        if (i == selectedElement) {
            var li = $("<li class=\"selected\"><a href='/book/title/" + this[i] + "'>" + this[i] + "</a></li>");
            ul.append(li);
        }
        else {
            var li = $("<li><a href='/book/title/" + this[i] + "'>" + this[i] + "</a></li>");
            ul.append(li);
        }

    }

    return $("<div id='results'></div>").append(ul);
};

//Función que busca los titulos de los libros que se corresponden con la búsqueda

function filter(key) {

    if (key == 40) { // Flecha Abajo
        if (selectedElement + 1 < results.length) {
            selectedElement++;
        }
        showResults();
    }
    else if (key == 38) { // Flecha Arriba
        if (selectedElement > 0) {
            selectedElement--;
        }
        showResults();
    }
    else if (key == 13) { // ENTER o Intro
        selectElement();
    }
    else {
        var filter = $('#searchbar').val();

        // Si es la key de borrado y el texto es vac�o, ocultar la lista
        if (key == 8 && filter == "") {
            deleteList();
            return;
        }

        if (filter.length > 3) {
            filter = filter.replace(" ", "+");
            if (cacheResults[filter] == null) {
                if (XMLHttpRequestObject) {

                    XMLHttpRequestObject.onreadystatechange = function () {
                        XMLHttpRequestObject.onreadystatechange = function () {
                            if (XMLHttpRequestObject.readyState == 4 &&
                                XMLHttpRequestObject.status == 200) {
                                var search = JSON.parse(XMLHttpRequestObject.responseText);
                                results = [];
                                search.items.forEach(volum => {
                                    results.push(volum.volumeInfo.title);
                                });
                                if (results.length == 0) {
                                    noResults();
                                }
                                else {
                                    cacheResults[filter] = results;
                                    updateResults();
                                }
                            }
                        }
                    };
                }

                XMLHttpRequestObject.open("GET", "https://www.googleapis.com/books/v1/volumes?q=" + filter + "&projection=lite&orderBy=relevance&fields=items(volumeInfo(title))", true);
                XMLHttpRequestObject.send(null);
            } else {
                results = cacheResults[filter];
                updateResults();
            }
        }


    }

}

//Si no se encuentran resultados se muestra un mensaje avisando
function noResults() {
    $('#results').html = "No results found";
    $('#results').css("display", "block");
}

//Cuando se va escribiendo se van actualizando los resultados
function updateResults() {
    selectedElement = -1;
    showResults();
}

//Cuando se selecciona un elemento se va a su libro
function selectElement() {
    if (results[selectedElement]) {
        window.location.href = "/book/title/" + results[selectedElement];
        deleteList();
    }
}

function showResults() {
    $('#results').replaceWith(results.formatList());
    $('#results').css("display", "block");
}

function deleteList() {
    $('#results').html = "";
    $('#results').css("display", "none");
}

