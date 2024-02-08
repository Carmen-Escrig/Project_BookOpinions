$(document).ready(function () {
    $("#like").on("click", function (e) {
        slug = $("#like").attr("slug");
        $("#like").addRemove("/review/" + slug + "/like", "/img/like.png", "/img/nolike.png");
    });
    $("#follow").on("click", function (e) {
        slug = $("#follow").attr("slug");
        $("#follow").addRemove("/profile/" + slug + "/follow", "/img/following.png", "/img/no_following.png");
    });
});