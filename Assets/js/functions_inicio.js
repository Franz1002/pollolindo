function imprimir() {
    // Realiza una solicitud AJAX al backend de PHP
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "http://localhost/pollo/inicio/imprimir", true); // Asegúrate de que la URL esté correcta

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Aquí puedes manejar la respuesta después de la impresión
            alert("¡Impresión realizada con éxito!");
        } else if (xhr.readyState == 4) {
            alert("Error en la impresión");
        }
    };

    xhr.send();
}
