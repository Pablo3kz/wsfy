(function () {
    tinymce.create('tinymce.plugins.WydeEditor', {
        init: function (ed, url) {
            var baseUrl = url.substring(0, url.lastIndexOf("/"));
            
            ed.addButton('dropcap', {
                title: 'DropCap',
                cmd: 'dropcap',
                image: baseUrl + '/images/dropcap.png'
            });

            ed.addCommand('dropcap', function () {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<span class="dropcap">' + selected_text + '</span>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

             ed.addButton('highlight', {
                title: 'Highlight',
                cmd: 'highlight',
                image: baseUrl + '/images/highlight.png'
            });

            ed.addCommand('highlight', function () {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<span class="highlight">' + selected_text + '</span>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

        }
    });
    // Register plugin
    tinymce.PluginManager.add('wydeEditor', tinymce.plugins.WydeEditor);
})();