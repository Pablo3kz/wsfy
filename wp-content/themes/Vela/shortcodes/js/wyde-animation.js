(function ($) {

    $(".wyde-animation").each(function () {
        var el = this;
        $(".animation-preview").hide();
        $("select", el).change(function () {
            $(".animation-preview").show();
            $(".animation-preview span", el).removeClass().addClass($(this).val()).addClass("animated ");
        });
    });

})(jQuery);

