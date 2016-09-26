    (function ($) {

        if (window.VcColumnView) {

            window.WydeTabView = window.VcColumnView.extend({
                events: {
                    'click > .vc_controls .vc_control-btn-delete': 'deleteShortcode',
                    'click > .vc_controls .vc_control-btn-prepend': 'addElement',
                    'click > .vc_controls .vc_control-btn-edit': 'editElement',
                    'click > .vc_controls .vc_control-btn-clone': 'clone',
                    'click > .wpb_element_wrapper > .vc_empty-container': 'addToEmpty'
                },
                render: function () {
                    var params = this.model.get('params');
                    window.WydeTabView.__super__.render.call(this);
                    if (!params.tab_id || params.tab_id.indexOf('def') != -1) {
                        params.tab_id = (+new Date() + '-' + Math.floor(Math.random() * 11));
                        this.model.save('params', params);
                    }
                    this.id = 'tab-' + params.tab_id;
                    this.$el.attr('id', this.id);
                    return this;
                },
                ready: function (e) {
                    window.WydeTabView.__super__.ready.call(this, e);
                    this.$tabs = this.$el.closest('.wpb_tabs_holder');
                    var params = this.model.get('params');
                    return this;
                },
                changeShortcodeParams: function (model) {
                    var params = model.get('params');
                    window.WydeTabView.__super__.changeShortcodeParams.call(this, model);
                    if (_.isObject(params)) {
                        var icon = "";
                        if (params.mode == "icon") {
                            switch (params.icon_set) {
                                case "openiconic":
                                    icon = params.icon_openiconic;
                                    break;
                                case "typicons":
                                    icon = params.icon_typicons;
                                    break;
                                case "entypo":
                                    icon = params.icon_entypo;
                                    break;
                                case "linecons":
                                    icon = params.icon_linecons;
                                    break;
                                default:
                                    icon = params.icon;
                                    break;
                            }
                        }

                        $('.ui-tabs-nav a[href="#tab-' + params.tab_id + '"]').html((params.mode == "icon" && icon != "") ? "<i class=\"" + icon + "\"></i>" : params.title);
                    }
                },
                deleteShortcode: function (e) {
                    _.isObject(e) && e.preventDefault();
                    var answer = confirm(window.i18nLocale.press_ok_to_delete_section),
                    parent_id = this.model.get('parent_id');
                    if (answer !== true) return false;
                    this.model.destroy();
                    if (!vc.shortcodes.where({ parent_id: parent_id }).length) {
                        vc.shortcodes.get(parent_id).destroy();
                        return false;
                    }
                    var params = this.model.get('params'),
                    current_tab_index = $('a[href="#tab-' + params.tab_id + '"]', this.$tabs).parent().index();
                    $('a[href="#tab-' + params.tab_id + '"]').parent().remove();
                    var tab_length = this.$tabs.find('.ui-tabs-nav li:not(.add_tab_block)').length;
                    if (tab_length > 0) {
                        this.$tabs.tabs('refresh');
                    }
                    if (current_tab_index < tab_length) {
                        this.$tabs.tabs("option", "active", current_tab_index);
                    } else if (tab_length > 0) {
                        this.$tabs.tabs("option", "active", tab_length - 1);
                    }

                },
                cloneModel: function (model, parent_id, save_order) {
                    var shortcodes_to_resort = [],
                    new_order = _.isBoolean(save_order) && save_order === true ? model.get('order') : parseFloat(model.get('order')) + vc.clone_index,
                    new_params = _.extend({}, model.get('params'));
                    if (model.get('shortcode') === 'vc_tab') _.extend(new_params, { tab_id: +new Date() + '-' + this.$tabs.find('[data-element_type=vc_tab]').length + '-' + Math.floor(Math.random() * 11) });
                    var model_clone = Shortcodes.create({ shortcode: model.get('shortcode'), parent_id: parent_id, order: new_order, cloned: true, cloned_from: model.toJSON(), params: new_params });
                    _.each(Shortcodes.where({ parent_id: model.id }), function (shortcode) {
                        this.cloneModel(shortcode, model_clone.id, true);
                    }, this);
                    return model_clone;
                }
            });
        }


    })(jQuery);