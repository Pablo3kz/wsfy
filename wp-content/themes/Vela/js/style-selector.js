(function ($) {
    "use strict";

    var selectedColor, selectedPattern, selectedImage = "";
    var isOverlay, isFixed = false;
    var cssVersion = new Date().toFormat("yyyyddmm");

    function getSkin() {
        var value = "";
        $.each($("body").attr("class").split(' '), function (i, v) {
            if (v.indexOf("skin-") > -1) {
                value = v.replace("skin-", "");
                return;
            }
        });
        return value;
    }

    function getPattern() {
        var value = "";
        $.each($("body").attr("class").split(' '), function (i, v) {
            if (v.indexOf("pattern-") > -1) {
                value = v;
                return;
            }
        });
        return value;
    }

    function toggleMapColor(color) {
        $(".gmaps").gMaps({ color: color });
    }

    function toggleLayout(name) {
        if (name)
            $("body").removeClass(name == "boxed" ? "wide" : "boxed").addClass(name);
        else
            $("body").toggleClass("boxed wide");

        $(window).trigger("resize");

    }

    function togglePattern(o) {
        if (o) {
            $("#layout-mode").val("boxed");
            toggleLayout("boxed");
            $("body").css("background-image", "").removeClass("background-overlay").toggleClass((selectedPattern ? selectedPattern : "") + " " + $(o).data("pattern"));
            $("li", $(o).parents("ul")).removeClass("active");
            $(o).parent("li").addClass("active");
            selectedPattern = $(o).data("pattern");
        }
    }
    function toggleImage(o) {
        if (o) {
            $("#layout-mode").val("boxed");
            toggleLayout("boxed");
            $("body").removeClass(selectedPattern).css({ "background-image": String.format("url('{0}')", $(o).data("image")) });
            $("li", $(o).parents("ul")).removeClass("active");
            $(o).parent("li").addClass("active");
            selectedImage = $(o).data("image");

            //force chrome to render background image.
            /*$("body").hide();
            $("body").get(0).offsetHeight;
            $("body").show();*/
        }
    }


    function setCssColorScheme() {
        if (selectedColor) {

            var customColor = $("#customColor");
            if (!customColor.length) {
                customColor = $("<style />").attr({ type: "text/css", id: "customColor" });
                $("head").append(customColor);
            }

            customColor.text("");
            customColor.append("a:hover,blockquote:before,.highlight,.top-nav>ul>li>a:hover,.social-icons a:hover,.nav>ul>li:hover>a,.nav>ul>li.active>a,.menu-cart:hover>a,#search:hover,#header #search.active:hover,#header.transparent #nav>ul>li:hover>a:before,#header.transparent .menu-cart:hover>a:before,#header.transparent #search:hover:before,#header.fixed #nav>ul>li:hover>a,#header.fixed #nav>ul>li.active>a,#header.fixed .menu-cart:hover>a,#header.fixed #search:hover,.post-title a:hover,.prev-post a:hover,.next-post a:hover,.post-meta a:hover,.social-link a,#toTop .border,.background-striped .flex-direction-nav a:before,.wpb_content_element .wpb_tabs_nav li.ui-tabs-active a:after,.required,.call-to-action:after,.counter-box i,.heading.title-4 h2:after,.heading.title-7 h2:after{color:" + selectedColor + ";}");
            customColor.append("#header.mobile #nav .menu,.wpb_toggle_title_active,#content h4.wpb_toggle_title_active,.wpb_accordion .wpb_accordion_wrapper .wpb_accordion_header.ui-state-active,.heading.title-5 h2:after,.heading.title-8 h2:after,.heading.title-10 h2:after{border-top-color:" + selectedColor + ";}");
            customColor.append("::-moz-selection{background:" + selectedColor + ";} ::selection{background:" + selectedColor + "}");
            customColor.append("#nav>ul>li:hover>a,#nav>ul>li.active>a,#header.fixed #nav>ul>li:hover>a,.header .menu-cart:hover>a,.menu-cart .dropdown-menu,.header #search:hover,.top-nav li:hover .sub-menu,#nav li:hover .sub-menu,#header.mobile #nav li.open>ul,blockquote,.heading.title-9 h2:before{border-color:" + selectedColor + ";}");
            customColor.append(".link-button span,#toTop .border:before,#header.mobile .nav li.open>a,#header.mobile .nav li.open:hover>a,.menu-cart .cart-items,#ajax-loader:before,#ajax-loader:after,.loading:before,.loading:after,.autocomplete .dropdown-menu ul>li>a:hover,.autocomplete .search-more a:hover,.autocomplete .search-more.selected a,.share-icons .dropdown-menu li a:hover,.flex-control-paging li a.flex-active,.testimonials-slider .flex-direction-nav a,.counter-box span:before,.counter-box span:after,.dropcap,.wpb_tour .wpb_tabs_nav li.ui-tabs-active,.wpb_content_element .wpb_tabs_nav li.ui-tabs-active,.wpb_content_element .wpb_tabs_nav li:hover,.vc_single_bar:hover .vc_bar,.call-to-action:before,.effect-goliath figure p,.effect-bubba figure{background-color:" + selectedColor + ";}");
            customColor.append(".icon-block,.icon-block .border,input[type=submit],input[type=button],a.button,a.button,a.tp-caption.rev-btn,button{border-color:" + selectedColor + ";background-color:" + selectedColor + ";}");
            customColor.append("#toTop:hover .border,input[type=submit]:hover,input[type=button]:hover,a.link-button,a.ghost-button,a.ghost-button:hover,.heading.title-3 h2:after{border-color:" + selectedColor + ";color:" + selectedColor + ";}");
            customColor.append(".woocommerce ul.products li.product .price,.woocommerce-page ul.products li.product .price,.woocommerce #content div.product p.price,.woocommerce #content div.product span.price,.woocommerce div.product p.price,.woocommerce div.product span.price,.woocommerce-page #content div.product p.price,.woocommerce-page #content div.product span.price,.woocommerce-page div.product p.price,.woocommerce-page div.product span.price{color:" + selectedColor + ";}");
            customColor.append(".woocommerce.widget ul li:hover a img,.woocommerce ul.products li.product:hover a img,.woocommerce div.product div.images a:hover img,.woocommerce a.button,.woocommerce a.button.alt,.woocommerce-page a.button,.woocommerce-page a.button.alt,.woocommerce button.button,.woocommerce button.button.alt,.woocommerce-page button.button,.woocommerce-page button.button.alt,.woocommerce #content input.button.alt,.woocommerce #respond input#submit.alt,.woocommerce input.button.alt,.woocommerce-page #content input.button.alt,.woocommerce-page #respond input#submit.alt,.woocommerce-page input.button.alt{border-color:" + selectedColor + ";background-color:" + selectedColor + ";}");
            customColor.append(".woocommerce a.button:hover,.woocommerce a.button.alt:hover,.woocommerce-page a.button:hover,.woocommerce-page a.button.alt:hover,.woocommerce button.button:hover,.woocommerce button.button.alt:hover,.woocommerce-page button.button:hover,.woocommerce-page button.button.alt:hover,.woocommerce #content input.button.alt:hover,.woocommerce #respond input#submit.alt:hover,.woocommerce input.button.alt:hover,.woocommerce-page #content input.button.alt:hover,.woocommerce-page #respond input#submit.alt:hover,.woocommerce-page input.button.alt:hover{border-color:" + selectedColor + ";color:" + selectedColor + ";}");
            customColor.append(".woocommerce span.onsale,.woocommerce-page span.onsale,.woocommerce .woocommerce-error,.woocommerce .woocommerce-info,.woocommerce .woocommerce-message,.woocommerce-page .woocommerce-error,.woocommerce-page .woocommerce-info,.woocommerce-page .woocommerce-message,.woocommerce .widget_price_filter .ui-slider .ui-slider-range,.woocommerce-page .widget_price_filter .ui-slider .ui-slider-range,.chosen-container .chosen-results li.highlighted{background-color:" + selectedColor + ";}");
            customColor.append(".woocommerce .woocommerce-breadcrumb a,.woocommerce-page .woocommerce-breadcrumb a,.woocommerce-page .woocommerce-message .button:hover{color:" + selectedColor + ";}");

        }
    }



    function toggleColor(o) {
        if (o) {

            if (selectedColor == $(o).data("color")) return;

            var oldColor = selectedColor;
            selectedColor = $(o).data("color");

            setCssColorScheme();

            $("li", $(o).parents("ul")).removeClass("active");
            $(o).parent("li").addClass("active");
            toggleMapColor(selectedColor);
        }
    }

    $.fn.styleSelector = function () {

        var $el = $(this);
        var elWidth = $el.outerWidth();

        selectedPattern = getPattern();
        if (!selectedPattern) selectedPattern = "1";
        selectedImage = $("body").css("background-image");
        if (selectedImage) selectedImage = selectedImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
        isOverlay = $("body").hasClass("background-overlay");
        isFixed = $("body").hasClass("background-fixed");



        $el.css({ left: -(elWidth + 5) });



        $(".selector-toggle", $el).on("click", function (event) {
            event.preventDefault();

            if ($el.hasClass("open")) {
                $el.animate({ left: -(elWidth + 5) }, 200, function () {
                    $el.removeClass("open");
                });
            } else {
                $el.addClass("open");
                $el.animate({ left: 0 }, 200);
            }
            return false;

        });


        $("#color-list a").on("click", function (event) {
            event.preventDefault();
            toggleColor(this);
            return false;
        });


        $("#layout-mode").change(function () {
            toggleLayout();
        });

        $("#background-mode").change(function () {

            if ($(this).val() == "pattern") {
                togglePattern($("#bg-pattern-list li[class='active'] a"));
                $("#pattern-background").toggle(true);
                $("#image-background").toggle(false);
            }
            else {
                toggleImage($("#bg-image-list li[class='active'] a"));
                $("#pattern-background").toggle(false);
                $("#image-background").toggle(true);
            }



        });

        $("#bg-pattern-list a").on("click", function (event) {
            event.preventDefault();
            togglePattern(this);
            return false;
        });
        $("#bg-image-list a").on("click", function (event) {
            event.preventDefault();
            toggleImage(this);
            return false;
        });


        $("#btn-pattern-fixed label").on("click", function () {
            if ($(this).hasClass("active")) return;


            if ($("input", this).val() == "on") {
                $("body").addClass("background-fixed");
                $(this).removeClass("btn-default").addClass("btn-primary");
                isFixed = true;
            } else {
                $("body").removeClass("background-fixed");
                $(".btn-primary", $(this).parents(".btn-group")).removeClass("btn-primary").addClass("btn-default");
                isFixed = false;
            }

            $(".btn", $(this).parents(".btn-group")).removeClass("active");
            $(this).addClass("active");

        });

        $("#btn-pattern-overlay label").on("click", function () {
            if ($(this).hasClass("active")) return;

            if ($("input", this).val() == "on") {
                $("body").addClass("background-overlay");
                $(this).removeClass("btn-default").addClass("btn-primary");
                isOverlay = true;
            } else {
                $("body").removeClass("background-overlay");
                $(".btn-primary", $(this).parents(".btn-group")).removeClass("btn-primary").addClass("btn-default");
                isOverlay = false;
            }
            $(".btn", $(this).parents(".btn-group")).removeClass("active");
            $(this).addClass("active");

        });

        $(".reset-style").on("click", function (event) {
            event.preventDefault();
            window.location.reload();
            return false;
        });



        if ($("body").hasClass("boxed")) $("#layout-mode").val("boxed");
        else $("#layout-mode").val("wide");

        if (selectedImage) {
            $("#background-mode").val("image");
            $("#pattern-background").hide();
            $("#image-background").show();

        } else {
            $("#background-mode").val("pattern");
            $("#pattern-background").show();
            $("#image-background").hide();
        }

        if (selectedPattern) $("#bg-pattern-list a[data-pattern='" + selectedPattern + "']").parent("li").addClass("active");
        if (selectedImage) {
            $("#bg-image-list li a[data-image='" + selectedImage + "']").parent().addClass("active");
        }


        if (isFixed) {
            $("#btn-pattern-fixed .off").parent(".btn").removeClass("active");
            $("#btn-pattern-fixed .on").parent(".btn").addClass("active btn-primary");
        } else {
            $("#btn-pattern-fixed .off").parent(".btn").addClass("active");
            $("#btn-pattern-fixed .on").parent(".btn").removeClass("active btn-primary");
        }

        if (isOverlay) {
            $("#btn-pattern-overlay .off").parent(".btn").removeClass("active");
            $("#btn-pattern-overlay .on").parent(".btn").addClass("active btn-primary");
        } else {
            $("#btn-pattern-overlay .off").parent(".btn").addClass("active");
            $("#btn-pattern-overlay .on").parent(".btn").removeClass("active btn-primary");
        }


    };

})(jQuery);