var books = [];

jQuery.fn.createBoard = function () {
   this.each(function () {
      elem = $(this);
      createImages(elem);
   });
   return this;
};

$.post("example.php", function () {
   alert("success");
})
   .done(function () {
      alert("second success");
   })
   .fail(function () {
      alert("error");
   })
   .always(function () {
      alert("finished");
   });
   
//Función que crea las imagenes y las añade al tablón
function createImages(tablon) {

   var positions = [];

   //Crear las imagenes con sus propiedades y añadirlas
   for (let i = 0; i < books.length; i++) {
      var img = document.createElement("img");
      img.src = "img/" + books[i]["cover"];
      img.alt = "Imagen";
      img.style.height = "150px";
      img.style.width = "100px";
      img.className = "img";

      img.style.position = "relative";

      //Se genera aleatoriamente la posición y a partir de la segunda imagen se comprueba que no colisionen
      do {
         img.style.left = Math.random() * 500 + 30 + "px";
         img.style.top = Math.random() * 300 + 30 + "px";
         document.getElementById("board").appendChild(img);
         if (i == 0) {
            break;
         }
      } while (comprobarPosicion(positions, img));
      positions.push(img);
   }

}

//Función que compara si se chocan dos imagenes
function isCollide(a, b) {
   return !(
      ((a.y + 150) < (b.y)) ||
      (a.y > (b.y + 150)) ||
      ((a.x + 100) < b.x) ||
      (a.x > (b.x + 100))
   );
}

//Función que va comprobando si se choca la posición generada con todas las ya creadas
function comprobarPosicion(positions, position) {
   for (let j = 0; j < positions.length; j++) {
      if (isCollide(position, positions[j]) || isCollide(positions[j], position)) {
         return true;
      }
   }

   return false;
}

// Coger los 6 libros que se muestran al principio

/* var book1 = {
    id: 1,
    title: "Éxtasis",
    author: "Tracy Wolff",
    cover: "cover-1.jpeg",
    review: "Literalmente no tienen coherencia muchas de las cosas que suceden. Nos ha parecido un final de saga muuuuy descafeinado para todo lo que ha ido pasando a lo largo de 6 libros. Sinceramente creemos que deberían haberse quedado en el anterior. Hubiese sido mejor finalizar la saga con la trama de Cyrus y la historia de los tres meses de Grace y Hudson como complemento. Este libro no le hace justicia a toda la saga. Sabíamos a lo que veníamos, éramos conscientes de que es una saga súper juvenil, que a la escritora le gusta meter más relleno que a un cojín y que hay protagonistas que se merecen sensatez en vena, pero aun así… Sabiendo lo que sabemos ahora, seguramente no lo habríamos leído para quedarnos con el buen sabor de boca que nos habían dejado los anteriores."
}

var book2 = {
    id: 2,
    title: "Alas de Sangre",
    author: "Rebecca Yarros",
    cover: "cover-2.jpeg",
    review: "Este libro es excelente. Entretenido, enseguida te cautiva. No te deja soltarlo ni un minuto y te mantiene pensando en él todo el día. Sus personajes son de lo mejor y el mundo mágico nunca antes leído. Espero que la serie que van a hacer sea fiel, de serlo será éxito rotundo."
}

var book3 = {
    id: 3,
    title: "La Magnificiencia del 3, 6 y 9",
    author: "Lily del Pilar",
    cover: "cover-3.jpeg",
    review: "Hay cosas que yo quería que se desarrollara de otra manera y siento que no me terminaron de contar la profundidad de ciertas cosas, pero el camino que tomo me sorprendió y me hizo llorar mucho, es una historia que no es un romance completamente feliz pero te promete tratar temas delicados por eso si eres sensible re aconsejo leerlo bajo tu propia precaución, no les diré como termina pero en otra vida, espero que todo sea diferente."
}

var book4 = {
    id: 4,
    title: "Mentiras encubiertas",
    author: "Kate Carlisle",
    cover: "cover-4.jpeg",
    review: "Un libro maravillosos que te mantiene intrigado hasta la última página. Muy recomendable, como otros libros de esta escritora. La ambientación está muy bien hecha. Lo único que fallan son algunos personajes que parecen muy planos. Pero el protagonista y lso más importantes están bien y tiene nun buen desarrollo"
}

var book5 = {
    id: 5,
    title: "Rivales Divinos",
    author: "Rebecca Ross",
    cover: "cover-5.jpeg",
    review: "Esta novela tiene una ambientación muy similar a novelas situadas en las guerras mundiales, o sea es sobre Iris, cuyo hermano se va a la guerra y ella se queda atrás en la ciudad. Ella trabaja en un periódico, donde gran parte de su motivación gira alrededor del hermano. Pero, es un mundo donde la magia existe, donde Iris se comunica con alguien por medio de cartas enviadas por debajo de la puerta, iniciando casi una relación estilo amigos por correspondencia. La manera como se maneja la magia está interesante porque no se siente tanto como algo que te tienen que introducir, si no es algo que simplemente existe y lo introducen de una manera muy sutil."
}

var book6 = {
    id: 6,
    title: "One Punch Man",
    author: "One",
    cover: "cover-6.jpeg",
    review: "Una de las principales razones por las que One-Punch Man funciona tan bien es que nunca se toma a sí mismo demasiado en serio . A diferencia del anime, donde la comedia parece completamente artificial o fuera de lugar, One-Punch Man la hace funcionar porque la premisa en sí misma parece tan ridículamente inconcebible que parece más una parodia."
}


var books = [book1, book2, book3, book4, book5, book6]; 
 */



