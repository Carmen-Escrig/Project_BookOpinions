$(document).ready(function () {
    $('.your-class').slick();
    $("#board-popular").createBoard("/book/getMostPopular");


    $('.slick-next').click(function() {
        invoqueBoard();
    });

    $('.slick-prev').click(function() {
        invoqueBoard();
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