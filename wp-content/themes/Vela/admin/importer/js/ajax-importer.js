(function ($) {
    "use strict";
    function wydeImporter() {

        this.import_url = "";
        this.data_dir = "";
        this.demoId = "";
        this.tasks = [];
        this.steps = [];

        if (typeof ajax_importer_settings != "undefined") $.extend(this, ajax_importer_settings);

        if (this.import_tasks) {
            for (var i = 0; i < this.import_tasks.length; i++) {
                this.tasks.push(this.import_tasks[i]);
            }
        }

        var self = this;

        this.initImporter = function () {

            $(".import-wrapper a").click(function (event) {
                event.preventDefault();
                if (typeof ajax_importer_settings == "object") {
                    if (confirm("Are you sure you want to import this demo?")) {

                        var $el = $(this);

                        self.demoId = $el.attr("id").replace(/\D/g, '');

                        var $panel = $el.parents(".import-wrapper");

                        $el.parent().find("a").slideUp();

                        $panel.append("<p><strong>Importing...</strong></p>");

                        self.progress = $("<ul class=\"import-progress\"></ul>");
                        $panel.append(self.progress);

                        for (var i = 0; i < self.tasks.length - 1; i++) {
                            self.addTask(self.tasks[i]);
                            self.importContent(i);
                        }
                        self.addTask(self.tasks[self.tasks.length - 1]);

                        $panel.append("<p><a href=\"" + window.location.href + "&action=cancel\" class=\"button-cancel\"><i class=\"el el-remove-sign\"></i>Cancel</a></p>");

                    }
                } else {
                    alert("Cannot import now!");
                }
                return false;
            });
        };

        this.addTask = function (task) {
            self.progress.append("<li><span class=\"status\"></span> " + task + "</li>");
        };

        this.importContent = function (index) {

            setTimeout(function () {
                var task = self.tasks[index];
                self.progress.find("li").eq(index).find(".status").html("<span class=\"w-loader\"></span>");
                self.beginImport(task, function () {
                    self.success(index);
                }, function () {
                    self.fail(index);
                });
            }, 1000 * index);

        };

        this.beginImport = function (task, success, fail) {

            var data = { action: "wyde_importer", demo: self.demoId, type: task };

            $.ajax({
                url: self.import_url,
                data: data
            }).done(function (response) {

                var responseObj = jQuery.parseJSON(response);
                if (responseObj.code == "1") {
                    if (typeof success == "function") {
                        success();
                    }
                } else {
                    if (typeof fail == "function") {
                        fail();
                    }
                }

                self.steps.push(responseObj.code);

                if (self.steps.length == self.tasks.length - 1) {
                    self.importThemeOptions();
                }

            }).fail(function () {
                console.log("Import fail.");
            });

        };

        this.importThemeOptions = function () {
            self.progress.find("li").last().find(".status").html("<span class=\"w-loader\"></span>");
            self.beginImport("settings", function () {
                setTimeout(function () {
                    var optionsUrl = self.data_dir + "/" + self.demoId + "/theme_options.txt";
                    if (optionsUrl) {
                        $("#import-link-value").val(optionsUrl);
                        var formOptions = $("#redux-form-wrapper");
                        var hiddenAction = $("<input type=\"hidden\" id=\"import-hidden\" />");
                        hiddenAction.attr("name", $("#redux-import").attr("name")).val("true");
                        formOptions.append(hiddenAction);
                        formOptions.submit();
                    }
                }, 1000);
            });

        };

        this.success = function (index) {
            self.progress.find("li").eq(index).find(".status").html("<i class=\"el el-ok-sign\"></i>");
        }

        this.fail = function (index) {
            self.progress.find("li").eq(index).find(".status").html("<i class=\"el el-remove-sign\"></i>");
        };

        $(document).ready(function () {

            self.initImporter();

        });

    }

    new wydeImporter();


})(jQuery);
