;(function ($) {
  "use strict";

  var $loading = $('#preloader').hide();
  $(document)
  .ajaxStart(function () {
    $loading.show();
  })
  .ajaxStop(function () {
    $loading.hide();
  });

  jQuery(document).ready( function($) {
  	$('body').prepend('<div class="modal fade bd-example-modal-sm" id="modalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog modal-sm"><div class="modal-content">     <div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button></div></div></div></div>');
  } );
})(jQuery);  