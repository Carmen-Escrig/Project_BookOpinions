jQuery.fn.addRemove = function (link, image_true, image_false) {

    this.each(function () {
        elem = $(this);
        $.get(link, function (data) {
            if (data) {
                elem.attr("src", image_true);
                alert
                $('#num').text(parseInt($('#num').text()) + 1);
            } else {
                elem.attr("src", image_false);
                $('#num').text(parseInt($('#num').text()) - 1);
            }
        });

    });
    return this;
};
