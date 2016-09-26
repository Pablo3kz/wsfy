(function ($) {
    "use strict";
    function loadResults(ajaxURL, el) {

        var autocomplete = $(".autocomplete", el);
        if (!autocomplete.length) {
            createAutoComplete(el);
        }

        var data = { action: "ajax_search", search_keyword: $("input", el).val() };

        if (data.search_keyword.length == 0) {
            autocomplete.hide().removeClass("open");
            $(".dropdown-menu, .search-more", autocomplete).html("");
            return;
        }

        autocomplete.show().addClass("open");
        $(".search-more", autocomplete).removeClass("selected").html("<p class=\"search-loading\"><span class=\"post-loader\"></span></p>");

        $.post(ajaxURL, data, function (response) {

            $(".dropdown-menu", autocomplete).html("");

            var results = $.parseJSON(response);

            if (results && results.length > 0) {
                results.sort(function (a, b) {
                    var x = a['title'];
                    var y = b['title'];
                    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
                });
                $.each(results, function () {
                    var list = $(String.format("<li><h5>{0}</h5></li>", this.title));
                    list.append(getListItems(this.name, this.items));
                    $(".dropdown-menu", autocomplete).append(list);
                });


            }
            if (results.length == 0) $(".search-more", autocomplete).addClass("selected");
            $(".search-more", autocomplete).html(String.format("<a href=\"{0}?s={1}\">See more results for \"{1}\"</a>", wyde.page.siteURL, $("input", el).val()));


            if (typeof wyde.page.updateLink == "function") wyde.page.updateLink($("li", autocomplete.not("#product-list")));


            if ($(el).hasClass("active")) {
                autocomplete.show().addClass("open");
                $(".dropdown-menu", autocomplete).focus();
            }
            var h = $(window).height() - $(".search-more", autocomplete).outerHeight() - $(".dropdown-menu", autocomplete).offset().top;
            $(".dropdown-menu", autocomplete).css("max-height", h);
        });
    }

    function createAutoComplete(el) {
        $("<div class=\"autocomplete\"><ul class=\"dropdown-menu\"></ul><div class=\"search-more\"></div></div>").appendTo(".search-wrapper", el);
    }

    function getListItems(name, items) {
        var list = $("<ul></ul>").attr("id", String.format("{0}-list", name.toLowerCase()));
        $.each(items, function () {
            var image = "";
            if (this.post_image) image = String.format("<span class=\"thumb\"><img src=\"{1}\" alt=\"{2}\"></span>", this.post_link, this.post_image, this.post_title);
            var author = "";
            if (this.post_author) author = String.format("<span><i class=\"fa fa-pencil\"></i>{0}</span>", this.post_author);
            list.append($(String.format("<li><a href=\"{1}\">{0}<span><strong>{2}</strong><span class=\"post-meta\">{3}<span><i class=\"fa fa-clock-o\"></i>{4}</span></span></span></a></li>", image, this.post_link, this.post_title, author, this.post_date)));
        });
        return list;
    }

    $.extend(wyde.page, {

        ajaxSearch: function (options) {

            var settings = {
                delay: 500,
                element: "#search"
            };

            if (typeof ajax_search_settings != "undefined") $.extend(settings, ajax_search_settings);

            settings = $.extend(true, settings, options);

            if (!settings.ajaxURL) return;

            return $(settings.element).each(function () {

                var ajaxTimer = null;
                var el = this;

                createAutoComplete(el);
                $("input", el).attr("autocomplete", "off");
                $("input", el).keyup(function (event) {
                    if (event.keyCode != "38" && event.keyCode != "40" && event.keyCode != "13" && event.keyCode != "27" && event.keyCode != "39" && event.keyCode != "37") {

                        if (ajaxTimer != null) {
                            clearTimeout(ajaxTimer);
                        }
                        ajaxTimer = setTimeout(function () { loadResults(settings.ajaxURL, el); }, settings.delay);
                    }
                });
            });


        }
    });
})(jQuery);