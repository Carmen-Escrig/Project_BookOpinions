$(document).ready(function () {
    $('.your-class').slick();
    $("#board-popular").createBoard("/book/getMostPopular");


    $('.slick-next').click(function () {
        invoqueBoard();
    });

    $('.slick-prev').click(function () {
        invoqueBoard();
    });

    $('#searchbar').keypress(function (event) {
        if (event.which == 13) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    $('#searchbar').keyup(function (event) {
        event.preventDefault();
        event.stopPropagation();
        filter(event.which);
    });

    $("#seeBook").click();

    $(".lightbox").dialog({
        height: 600,
        width: 450,
        modal: true,
        resizable: false,
        autoOpen: false,
        buttons: [
            {
                text: "See Book",
                icon: "ui-icon-zoomin",
                click: function () {
                    window.location.href = "/book/" + results[selectedElement];
                }
            }
        ]
    });
});

function invoqueBoard() {
    var board = $('.slick-current>.board');
    var link;
    if (board.attr("id") == "board-popular") {
        link = "/book/getMostPopular";
    } else if (board.attr("id") == "board-discover") {
        link = "/book/getMostPopular";
    } else {
        link = "/book/getMonthlyPopular";
    }

    board.createBoard({ link: link });
}