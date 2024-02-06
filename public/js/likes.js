$(document).ready(function () {
    $("#like").on("click", function (e) {
        slug = $("#like").attr("slug");
        $("#like").addRemove("/review/" + slug + "/like", "/img/like.png", "/img/nolike.png");
    });
});