jQuery.fn.addRemove = function (link, image_true, image_false) {

    this.each(function (event) {
        event.preventDefault();
        elem = $(this);
        $.get(link, function (data) {
            if (data) {
                elem.attr("src", image_true);
            } else {
                elem.attr("src", image_false);
            }
        });

    });
    return this;
};
