/*
* AJAX Page Loader 1.7.0
* ------------------*/

(function ($) {
    "use strict";
    $.extend(wyde.page, {
        isLoad: false,
        started: false,
        ajaxPage: function (options) {

            this.settings = $.extend({
                search: ".ajax-search-form",
                scope: this.scope.page,
                excludeURLs: [],
                excludeSelectors: [],
                transition: "fade"
            }, options);

            if (typeof ajax_page_settings != "undefined") this.settings = $.extend(this.settings, ajax_page_settings);


            this.searchPath = "";
            this.ignoreURLs = [];

            this.ignoreURLs.push("wp-login/");
            this.ignoreURLs.push("wp-admin/");
            this.ignoreURLs.push("wp-content/");

            if ($("body").hasClass("woocommerce") || $("body").hasClass("woocommerce-page")) return;

            var self = this;

            $(this.settings.excludeURLs).each(function (i, v) {
                if (v) self.ignoreURLs.push(v);
            });

            $(this.settings.excludeSelectors).each(function () {
                if (this.tagName.toLowerCase() == "a") {
                    self.ignoreURLs.push(this.href);
                } else {
                    $(this).find("a").each(function () {
                        self.ignoreURLs.push(this.href);
                    });
                }

            });


            if (window.history.pushState) {
                $(window).off("popstate").on("popstate", function () {
                    if (self.started === true && !self.isIgnore(document.location)) {
                        self.loadContent(document.location.toString());
                    }
                });
            }

            setTimeout(function () {
                self.updateLink();
            }, 100);

            if (self.settings.search) {

                $(self.settings.search).each(function (index) {

                    if ($(this).attr("action")) {
                        //Get the current action so we know where to submit to
                        self.searchPath = $(this).attr("action");

                        //bind our code to search submit, now we can load everything through ajax :)
                        //$("#searchform").name = "searchform";
                        $(this).submit(function () {
                            console.log(self.searchPath);
                            self.submitSearch($(this).serialize());
                            return false;
                        });
                    }
                });
            }

        },
        updateLink: function (newElements) {
            var self = this;
            if (self.isIgnore(document.URL)) return;
            $("a:not(.no-ajax)", newElements ? newElements : document.body).each(function () {

                if (this.href.indexOf(self.siteURL) > -1 && (!this.target || this.target == "_self") && !self.isIgnore(this)) {

                    $(this).off("click").on("click", function (event) {
                        event.preventDefault();

                        var $el = $(this);
                        var url = $el.attr("href");
                        var title = $el.attr("title") || null;

                        if (event.which == 2 || event.metaKey) {
                            return true;
                        }
                        // set scroll target position
                        self.targetPos = 0;
                        var hash = self.getHash(url);
                        if (hash) {
                            url = url.replace(hash, "");
                            self.targetPos = hash;                            
                        }
                        
                        self.loadContent(this.href);
                    });

                }
            });
        },
        loadContent: function (url, getData) {

            if (!this.isLoad) {

                this.isLoad = true;
                this.started = true;

                var path = url.replace(/^.*\/\/[^\/]+/, '');

                if (typeof window.history.pushState == "function") {
                    history.pushState({ foo: 1000 + Math.random() * 1001 }, "Loading...", path + (getData ? "?" + getData : ""));
                } else document.location.href = "#" + path;

                this.currentPage = path;

                var self = this;

                $(window).unbind("resize");

                this.hideContent(function () {

                    self.removeSlider();

                    $.ajax({
                        type: "GET",
                        url: url,
                        data: getData,
                        cache: false,
                        dataType: "html",
                        success: function (response) {                           

                            self.isLoad = false;

                            self.updateContent(response);

                            self.showContent(function () {

                                self.refreshSlider();

                                if (self.settings.scope == self.scope.page && typeof self.load == "function") self.load();
                                else if (self.settings.scope == self.scope.content && typeof self.contentLoad == "function") self.contentLoad();

                                self.initVCElements();

                                if (self.targetPos) {
                                setTimeout(function () {
                                    history.pushState(null, "", self.targetPos);                                     
                                }, 800);
                            }

                            });


                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            self.isLoad = false;
                            window.location.href = url;
                        },
                        statusCode: {
                            404: function () {
                                console.log("Page not found!");
                            }
                        }
                    });
                });
            }
        },
        hideContent: function (callback) {

            var self = this;

            var selectors = $(self.settings.scope);

            var windowWidth = $(window).width() + 100;
            var windowHeight = $(window).height() + 100;

            var duration = 1000;

            switch (self.settings.transition) {
                case "fade":
                    duration = 800;
                    selectors.animate({ opacity: 0 }, duration, function () {
                    });
                    break;
                case "slideToggle":
                    selectors.css({ position: "relative" }).animate({ left: -(windowWidth) }, duration, "easeInOutExpo", function () {
                        $("body").css({ overflow: "hidden" });
                    });
                    break;
                case "slideLeft":
                    selectors.css({ position: "relative" }).animate({ left: -(windowWidth) }, duration, "easeInOutExpo", function () {
                        $("body").css({ overflow: "hidden" });
                    });
                    break;
                case "slideRight":
                    selectors.css({ position: "relative" }).animate({ left: windowWidth }, duration, "easeInOutExpo", function () {
                        $("body").css({ overflow: "hidden" });
                    });
                    break;
                case "slideUp":
                    selectors.css({ position: "relative" }).animate({ top: -(windowHeight), opacity: 0 }, duration, "easeInOutExpo", function () {
                        $("body").css({ overflow: "hidden" });
                    });
                    break;
                case "slideDown":
                    selectors.css({ position: "relative" }).animate({ top: windowHeight, opacity: 0 }, duration, "easeInOutExpo", function () {
                        $("body").css({ overflow: "hidden" });
                    });
                    break;
            }

            setTimeout(function () {
                self.showLoader();
                self.scrollTo(0, {
                    duration: 100
                });

                if (typeof callback == "function") {
                    callback();
                }

            }, duration);


        },
        showContent: function (callback) {
            var self = this;
            var duration = 1000;

            var selectors = $(self.settings.scope);

            var windowWidth = $(window).width() + 100;
            var windowHeight = $(window).height() + 100;

            this.preloadImages(function () {

                switch (self.settings.transition) {
                    case "fade":
                        duration = 800;
                        selectors.animate({ opacity: 1 }, duration, function () {
                        });
                        break;
                    case "slideToggle":
                        selectors.animate({ left: 0 }, duration, "easeInOutExpo", function () {
                            $("body").css({ overflow: "" });
                            $(this).css("position", "");
                        });
                        break;
                    case "slideLeft":
                        selectors.css({ left: windowWidth }).animate({ left: 0 }, duration, "easeInOutExpo", function () {
                            $("body").css({ overflow: "" });
                            $(this).css("position", "");
                        });
                        break;
                    case "slideRight":
                        selectors.css({ left: -(windowWidth) }).animate({ left: 0 }, duration, "easeInOutExpo", function () {
                            $("body").css({ overflow: "" });
                            $(this).css("position", "");
                        });
                        break;
                    case "slideUp":
                        selectors.css({ top: windowHeight }).animate({ top: 0, opacity: 1 }, duration, "easeInOutExpo", function () {
                            $("body").css({ overflow: "" });
                            $(this).css("position", "");
                        });
                        break;
                    case "slideDown":
                        selectors.css({ top: -(windowHeight) }).animate({ top: 0, opacity: 1 }, duration, "easeInOutExpo", function () {
                            $("body").css({ overflow: "" });
                            $(this).css("position", "");
                        });
                        break;
                }

                if (typeof callback == "function") {
                    setTimeout(function () {
                        callback();
                    }, duration);
                }

            });
        },
        getNodeAttribute: function (text, tag, attr) {

            var regex = new RegExp(String.format("<{0} {1}=[\"'](.*)[\"'].*>", tag, attr));

            var m = regex.exec(text);
            if (m && m[1]) {
                return m[1];
            }
            return false;
        },
        updateToolBar: function (content) {

            if ($("#wpadminbar").length > 0) {
                var adminBar = $(content).find("#wpadminbar");
                if (adminBar) $("#wpadminbar").html(adminBar.html());

            }

        },
        updateHead: function (content) {
            window.$ = jQuery;

            $("head").find("style[data-type='vc_custom-css']").remove();
            $("head").find("style[data-type='vc_shortcodes-custom-css']").remove();

            var vc_page_style = $(content).filter("style[data-type='vc_custom-css']");
            if (vc_page_style) $("head").append(vc_page_style);

            var vc_shortcode_style = $(content).filter("style[data-type='vc_shortcodes-custom-css']");
            if (vc_shortcode_style) $("head").append(vc_shortcode_style);

            $("head").find("title").replaceWith($(content).filter("title"));

        },
        googleTracking: function () {
            var self = this;
            if (typeof ga == "function") {
                ga(function () {
                    var trackers = ga.getAll();
                    $.each(trackers, function (i, v) {
                        v.send("pageview", {
                            "page": self.currentPage,
                            "title": document.title
                        });
                        //v.send("pageview");
                    });
                });
            }
        },
        getDocumentHtml: function (html) {

            var result = String(html)
				.replace(/<\!DOCTYPE[^>]*>/i, '')
				.replace(/<(html|head|body|title|meta)([\s\>])/gi, '<div class="document-$1"$2')
				.replace(/<\/(html|head|body|title|meta)\>/gi, '</div>');

            return $.trim(result);
        },
        updateContent: function (content) {

            window.$ = jQuery;

            this.clearVCClass();


            if (this.settings.scope == this.scope.content) {
                //get new slider
                var slider = $(content).find("#slider");
                if (slider.length) {

                    if ($("#slider").length) {
                        //update slider
                        $("#slider").html(slider.html());
                    } else {
                        //insert new slider
                        if ($("#header").hasClass("below-slider"))
                            $("#header").before(slider);
                        else
                            $("#header").after(slider);
                    }

                } else $("#slider").remove();

            }


            //get content
            var output = $(content).find(this.settings.scope);
            //set content
            if (output.length) $(this.settings.scope).html(output.html());


            var $doc = null;
            var $body = null;
            if (window.DOMParser) { // all browsers, except IE before version 9
                try {
                    var parser = new DOMParser();
                    $doc = $(parser.parseFromString(content, "text/html"));
                    $body = $doc.find("body");
                    parser = $doc = null;
                } catch (e) {
                    $doc = $(this.getDocumentHtml(content));
                    $body = $doc.find(".document-body:first");
                }
            } else {
                $doc = $(this.getDocumentHtml(content));
                $body = $doc.find(".document-body:first");
            }

            // Update Body Classes
            var oldClasses = $("body").attr("class");

            var newClasses = $body.attr("class");
            if (newClasses) $("body").removeClass().addClass(newClasses);


            // Update Stylesheet
            var self = this;
            var $cssLinks = $body.find("link[rel='stylesheet']");
            $cssLinks.each(function () {
                if ($("body link[id='" + $(this).attr("id") + "']").length == 0) {
                    $(self.settings.scope).append(this);
                }
            });

            // Update Scripts
            var $scripts = $body.find("script");
            $scripts.each(function () {
                if ($(this).attr("src") && $("body script[src='" + $(this).attr("src") + "']").length == 0) {
                    $(self.settings.scope).append(this);
                }
            });


            this.updateHead(content);

            this.updateToolBar(content);

            this.googleTracking();


        },
        removeSlider: function () {
            // Revolution Slider
            if ($(".rev_slider").length && typeof $.fn.revolution == "function") {
                try {
                    $(".rev_slider").revkill();
                } catch (e) {
                    //console.log(e);
                }
            }
        },
        refreshSlider: function () {
            // Revolution Slider
            if ($(".rev_slider").length && typeof $.fn.revolution == "function") {                
                $(window).trigger("resize");
            }
        },
        clearVCClass: function () {
            //if exists visual composer to prevent duplicate class
            //if (typeof vc_js == "function") $("html").removeClass("js_active ontouchstart vc_mobile vc_desktop vc_transform");

        },
        initVCElements: function () {
            if (typeof vc_js == "function") vc_js();
            if (typeof $.fn.vcGrid == "function") {
                $('[data-vc-grid-settings]').each(function () {
                    $(this).vcGrid();
                })
            }

        },
        submitSearch: function (param) {
            if (!this.isLoad) {
                this.loadContent(this.searchPath, param);
            }
        },
        isIgnore: function (link) {

           if (!link) return true;

            var url = link.href ? link.href : link.toString();

            if (!url) return true;

            var samePage = false;
            var queryVars = getUrlVars(url);
            if(queryVars["page_id"]){              
                samePage = (this.id == queryVars["page_id"]);                
            }else{
                samePage = (link.pathname == window.location.pathname);
            }

            if(this.onepage){
                if($("body").hasClass("search")){
                    samePage = false;
                } 
            }

            var hash = this.getHash(url);

            if (hash == "#" || (samePage && hash)) return true;                
            
            for (var i in this.ignoreURLs) {
                if (url.indexOf(this.ignoreURLs[i]) > -1) {
                    return true;
                }
            }

            return false;
        }
    });

})(jQuery);