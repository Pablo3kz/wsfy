(function ($) {
    $(document).ready(function () {

        //Post/Portfolio Options.
        //media URL
        function togglePostMediaURL() {
            $("#embed_options").toggle($("#post-format-audio").is(":checked") || $("#post-format-video").is(":checked"));
        }

        //images gallery
        function toggleGallery() {
            $("#gallery_options").toggle($("#post-format-gallery").is(":checked"));
        }

        //Post Options.
        //post link
        function togglePostURL() {
            $("#link_options").toggle($("#post-format-link").is(":checked"));
        }
        //post quote
        function togglePostQuote() {
            $("#quote_options").toggle($("#post-format-quote").is(":checked"));
        }

        if ($("#post-formats-select").length) {
            toggleGallery();
            togglePostMediaURL();
            togglePostURL();
            togglePostQuote();

            $("#post-formats-select input").click(function () {
                toggleGallery();
                togglePostMediaURL();
                togglePostURL();
                togglePostQuote();
            });
        }

        //post sidebar
        function togglePostSidebar() {
            $(".cmb_id__meta_post_sidebar_position").toggle($("#_meta_post_custom_sidebar").is(":checked"));
        }
        togglePostSidebar();
        $("#_meta_post_custom_sidebar").click(function () {
            togglePostSidebar();
        });

        //Page Options.
        //page header
        function toggleHeader() {
            $("#header_options .form-table tr:not(.cmb_id__meta_page_header)").toggle($("#_meta_page_header").val() != 'hide');
        }

        toggleHeader();
        $("#_meta_page_header").change(function () {
            toggleHeader();
        });
        //sidebar
        function toggleSidebar() {
            $("#sidebar_options").toggle($("#page_template").val() != "full-width.php");
        }

        if ($("#page_template").length) {
            toggleSidebar();

            $("#page_template").change(function () {
                toggleSidebar();
            });
        }


        //slider
        function toggleSlider() {
            $("#slider_options .form-table tr:not(.cmb_id__meta_slider_show)").toggle($("#_meta_slider_show").is(":checked"));
        }
        toggleSlider();
        $("#_meta_slider_show").click(function () {
            toggleSlider();
        });

        //title options
        function toggleTitleOptions() {
            if ($("#_meta_post_custom_title").length) $("#title_options").toggle($("#_meta_post_custom_title").is(":checked"));
        }
        toggleTitleOptions();
        $("#_meta_post_custom_title").change(function () {
            toggleTitleOptions();
        });

        //title
        function toggleTitle() {
            $("#title_options .form-table tr:not(.cmb_id__meta_title)").toggle($("#_meta_title").val() != "hide");
            $(".cmb_id__meta_title_align").toggle($("#_meta_title").val() != "hide");
        }
        toggleTitle();
        $("#_meta_title").change(function () {
            toggleTitle();
            toggleTitleBackground();
        });
        function toggleTitleBackground() {
            if ($("#_meta_title").val() == "hide") return;
            $(".cmb_id__meta_title_background_image").toggle($("#_meta_title_background").val() == "image" || $("#_meta_title_background").val() == "video");
            $(".cmb_id__meta_title_background_video").toggle($("#_meta_title_background").val() == "video");
            $(".cmb_id__meta_title_background_color").toggle($("#_meta_title_background").val() != "" && $("#_meta_title_background").val() != "none");
            $(".cmb_id__meta_title_background_size").toggle($("#_meta_title_background").val() == "image");
            $(".cmb_id__meta_title_background_parallax").toggle($("#_meta_title_background").val() == "image");
            $(".cmb_id__meta_title_overlay").toggle($("#_meta_title_background").val() == "image" || $("#_meta_title_background").val() == "video");
            $(".cmb_id__meta_title_mask").toggle($("#_meta_title").val() != "hide");
            toggleTitleOverlay();
            toggleTitleMask();
        }
        toggleTitleBackground();
        $("#_meta_title_background").change(function () {
            toggleTitleBackground();
        });

        function toggleTitleOverlay() {
            $(".cmb_id__meta_title_overlay_color").toggle(($("#_meta_title_background").val() == "image" || $("#_meta_title_background").val() == "video") && $("#_meta_title_overlay").val() == "color");
        }
        toggleTitleOverlay();
        $("#_meta_title_overlay").change(function () {
            toggleTitleOverlay();
        });

        function toggleTitleMask() {
            $(".cmb_id__meta_title_mask_color").toggle($("#_meta_title").val() != "hide" && ($("#_meta_title_mask").val() != "" && $("#_meta_title_mask").val() != "none"));
        }
        toggleTitleMask();
        $("#_meta_title_mask").change(function () {
            toggleTitleMask();
        });


        //background
        function toggleBackground() {
            $("#background_options .form-table tr:not(.cmb_id__meta_background)").toggle($("#_meta_background").val() != "none");
            $(".cmb_id__meta_background_image").toggle($("#_meta_background").val() == "image" || $("#_meta_background").val() == "video");
            $(".cmb_id__meta_background_video").toggle($("#_meta_background").val() == "video");
            $(".cmb_id__meta_background_color").toggle($("#_meta_background").val() != "" && $("#_meta_background").val() != "none");
            $(".cmb_id__meta_background_size").toggle($("#_meta_background").val() == "image");
            $(".cmb_id__meta_background_parallax").toggle($("#_meta_background").val() == "image");
            $(".cmb_id__meta_background_pattern").toggle($("#_meta_background").val() != "" && $("#_meta_background").val() != "none");
            $(".cmb_id__meta_background_overlay").toggle($("#_meta_background").val() == "image" || $("#_meta_background").val() == "video");
            toggleBackgroundOverlay();
        }
        toggleBackground();
        $("#_meta_background").change(function () {
            toggleBackground();
        });

        function toggleBackgroundOverlay() {
            $(".cmb_id__meta_background_overlay_color").toggle(($("#_meta_background").val() == "image" || $("#_meta_background").val() == "video") && $("#_meta_background_overlay").val() == "color");
        }

        toggleBackgroundOverlay();
        $("#_meta_background_overlay").change(function () {
            toggleBackgroundOverlay();
        });

    });


})(jQuery);