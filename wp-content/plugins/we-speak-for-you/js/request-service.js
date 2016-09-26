;(function ($) {
  "use strict";

  jQuery(document).ready( function($) {
  	$('body').prepend('<div class="modal fade bd-example-modal-sm" id="modalMsg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog modal-sm"><div class="modal-content">     <div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button></div></div></div></div>');
    
    $('#wsfy_duration').change();
    if($('#wsfy_attached_file').val()) {
      $(".afu-process-file label.remove").removeAttr('disabled');
    }
    
  	$('#dtpwsfy_date').datetimepicker({
      weekStart: 1,
      todayBtn:  1,
  		autoclose: 1,
  		todayHighlight: 1,
  		startView: 2,
  		minView: 2,
  		forceParse: 0,
      format: 'mm/dd/yyyy' 
    });        
      
  	$('#dtpwsfy_start_time').datetimepicker({
      weekStart: 1,
      todayBtn:  1,
  		autoclose: 1,
  		todayHighlight: 1,
  		startView: 1,
  		minView: 0,
  		maxView: 1,
  		forceParse: 0
      });                
  } );
  
  function showCostByDuration(){
    var app_type = $('#wsfy_appointment_type').val();
    if(!app_type){
      return;
    }
    var price = wsfy_data.cost_by_duration[app_type][$('#wsfy_duration').val()];
    if(price){
      $('#wsfy_cost_by_duration').val(price);
    }     
  }
  
  $('#wsfy_duration').change(function(){
    showCostByDuration();
  });
  
  $('#wsfy_appointment_type').change(function(){
    showCostByDuration();  
  });

  $('#wsfy_cancel_service').click(function(){
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajax_search_settings.ajaxURL,
        data: {
            'action': 'wsfy_request_change_status',
            'form_data': {'post_id': $('#post_id').val()}
        },
        success: function(data) {
          if(data.success)
          {
            $('#modalMsg .modal-body').html(data.msg);
            $('#modalMsg').modal('show');
            if(data.redirect_to){ 
              $('#modalMsg').data('redirect_to', data.redirect_to);
              $('#modalMsg').on('hidden.bs.modal', function (e) {
                window.location.assign($('#modalMsg').data('redirect_to'));
              });
            }            
          } else {
          }
        },
        error: function(errorThrown) {
        }
    });     
  });
  
  $('#wsfy_request_translator').click(function(){
    var inputs = $('#request_form :input');
        
    var values = {};
    inputs.each(function() {
        values[this.name] = $(this).val();
    });
    
    $('.has-error').each(function(){
      $(this).removeClass('has-error');  
    });
    
    values['wsfy_attached_file'] = $('#wsfy_attached_file').val(); 
    $('#request_error').html('');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajax_search_settings.ajaxURL,
        data: {
            'action': 'wsfy_request_translator',
            'form_data': values
        },
        success: function(data) {
          if(data.success)
          {
            $('#modalMsg .modal-body').html(data.msg);
            $('#modalMsg').modal('show');
            if(data.redirect_to){ 
              $('#modalMsg').data('redirect_to', data.redirect_to);
              $('#modalMsg').on('hidden.bs.modal', function (e) {
                window.location.assign($('#modalMsg').data('redirect_to'));
              });
            }
          } else {
            $.each(data.errors, function(key, value) {
              $('#pnl'+key).addClass('has-error');
              $('#request_error').append('<p class="control-label"><b>ERROR</b>: '+value+'</p>');
            });
            
          }
        },
        error: function(errorThrown) {
        }
    }); 
  });  
  
  window.addEventListener( "afu_file_uploaded", function(e){
    if( "undefined" !== typeof e.data.response.media_uri ) {
      console.log( e.data.response.media_uri ); // the uploaded media URL
      $('#download_attached_file').attr('href', e.data.response.media_uri);
      $('#download_attached_file').html(e.data.response.media_uri);
      $('#download_attached_file').removeClass('hidden');
    }
  }, false);
  
  window.addEventListener( "afu_file_removed", function(e){
    $('#download_attached_file').addClass('hidden');
  }, false);    
})(jQuery);  