$(document).ready(function () {
    $('.your-class').slick();
    $("#board-popular").createBoard("/book/getMostPopular");


    $('.slick-next').click(function () {
        var index = parseInt($('.slick-current').attr("data-slick-index"));
        index = index == 0 ? 2 : index - 1; 
        $('.slick-slide').each(function() {
            if($(this).attr("data-slick-index") == index) {
                $(this).find(".board").destroyBoard();
            }
        });
        
        invoqueBoard();
    });

    $('.slick-prev').click(function () {
        var index = parseInt($('.slick-current').attr("data-slick-index"));
        index = index == 2 ? 0 : index + 1;
        $('.slick-slide').each(function() {
            if($(this).attr("data-slick-index") == index) {
                $(this).find(".board").destroyBoard();
            }
        });
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

    $(".lightbox").dialog({
        height: 600,
        width: 450,
        modal: true,
        resizable: false,
        autoOpen: false,
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

    board.createBoard(link);
}
