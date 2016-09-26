;(function ($) {
  "use strict";
  
  

  jQuery(document).ready( function($) {
    $('#pnlRoleFields').attr('class', 'qualified_interpreter');    
  });  
  
  $('#interpreter').click(function() {
    getRoleFields(this.id);
    $('#i_am_a').val(this.value);
  });
  
  $('#requester').click(function() {
    getRoleFields(this.id);
    $('#i_am_a').val(this.value);
  });  
  
  $('#btnBack').click(
    function() {
      $('#login_error').html('');
      $('#btnRegister').addClass('hidden');
      $('#pnlRoleFields').addClass('hidden');
      $('#btnBack').addClass('hidden');      
      $('#pnlChoseType').removeClass('hidden'); 
       
  });
  
  function getRoleFields(type)
  {
    var action = 'get_'+type+'_fields';
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajax_search_settings.ajaxURL,
        data: {
            'action': action,
        },
        success: function(data) {
          if(data.success)
          {
            $('#pnlRoleFields').html(data.html);
            $('#pnlRoleFields').removeClass('hidden');
            $('#pnlChoseType').addClass('hidden');
            $('#btnBack').removeClass('hidden');
            $('#btnRegister').removeClass('hidden');          
          } else {
            console.log(data);
          }
        },
        error: function(errorThrown) {
        }
    });     
  } 
  
  $('#wp-submit').click(function(){
    var inputs = $('#registerform .user-data');

    var values = {};
    inputs.each(function() {
        values[this.name] = $(this).val();
    });
    $('#login_error').html('');
    
    $('.has-error').each(function() {
        $(this).removeClass('has-error');
    });
    $('#login_error').addClass('has-error');
    
    $('#registerform').addClass('hidden');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: ajax_search_settings.ajaxURL,
        data: {
            'action': 'wsfy_register',
            'form_data': values
        },
        success: function(data) {
          if(data.success)
          {
            $('#modalMsg .modal-body').html(data.msg);
            $('#modalMsg').modal('show');            
            setTimeout(function($url){ window.location.assign('/'); }, 5000);
          } else {
            $('#registerform').removeClass('hidden');
            $.each(data.errors, function(key, value) {
              $('#pnl'+key).addClass('has-error');
              
              $('#login_error').append('<p class="control-label"><b>ERROR</b>: '+value+'</p>');
            });
          }
        },
        error: function(errorThrown) {
        }
    });    
  }); 
})(jQuery);