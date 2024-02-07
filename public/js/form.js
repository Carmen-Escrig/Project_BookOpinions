$(document).ready(function () {
    $('.searchbar').keyup(function(event) {
        event.preventDefault();
        event.stopPropagation();
        filter(event.which);
    });

});

