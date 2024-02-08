jQuery.fn.addRemove = function (link, image_true, image_false) {

    this.each(function () {
        elem = $(this);
        $.get(link, function (data) {
            if (data) {
                elem.fadeOut(200);
                elem.attr("src", image_true);
                elem.fadeIn(100);
                alert
                $('#num').text(parseInt($('#num').text()) + 1);
            } else {
                elem.fadeOut(200);
                elem.attr("src", image_false);
                elem.fadeIn(100);
                $('#num').text(parseInt($('#num').text()) - 1);
            }
        });

    });
    return this;
};
