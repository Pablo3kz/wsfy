;(function ($) {
  "use strict";
  $(document).ready(function() {
      $('body').prepend('<div class="modal fade bd-example-modal-sm"id="request_details"><div class="modal-dialog modal-sm"><div class="modal-content"><div class="modal-header"><a type="button"class="close"data-dismiss="modal"aria-label="Close"><span aria-hidden="true">&times;</span></a><h4 class="modal-title">Appointment Details</h4></div><div class="modal-body"></div><div id="pnl_new_request_actions"class="modal-footer"><button id="deny_request"type="button"class="btn btn-secondary deny-aprove-button"data-dismiss="modal">Deny</button><button id="accept_request"type="button"data-dismiss="modal"class="btn btn-primary deny-aprove-button">Accept</button></div><div id="pnl_my_request_actions"class="modal-footer hidden"><button id="cancel_request"type="button"class="btn btn-secondary deny-aprove-button start-actions hidden">Cancel</button><button id="start_request"type="button"data-dismiss="modal"class="btn btn-primary deny-aprove-button start-actions">Start</button><button id="finish_request"type="button"data-dismiss="modal"class="btn btn-primary deny-aprove-button end-actions hidden">End</button></div></div></div></div>');
    
      var wsfy_translator_available_requests = $('#wsfy_translator_available_requests').DataTable( {
          "processing": true,
          "serverSide": true,
          "bFilter" : true,
          "dom": '<<t>lp>',          
          "caption": 'Available Appointments',
          "ajax": ajax_search_settings.ajaxURL+"?action=wsfy_translator_available_requests",
              "columns": [
                  {
                    data: 'wsfy_appointment_type',
                    title: 'Appointment Type',
                    render: function(data, type, full, meta)
                    {
                      data = full.wsfy_appointment_sub_type?data+'/'+full['wsfy_appointment_sub_type']:data;
                      return data;  
                    }                                         
                  },               
                  {
                    data: 'wsfy_translation_type',
                    title: 'Translation Type',
                  },                
                  {
                    data: 'wsfy_date',
                    title: 'Date',
                    render: function(data, type, full, meta)
                    {
                      var d = data.split('-');
                      data = d[1]+"/"+d[2]+"/"+d[0];
                      return data;  
                    } 
                  },   
                  {
                    data: 'wsfy_location',
                    title: 'Location',
                  },
                  {
                    data: 'wsfy_time',
                    title: 'Time',
                  },                   
                  {
                    data: 'wsfy_length',
                    title: 'Length',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      data = data + '<input type="hidden" value="'+full.post_id+'" />';
                      return data;  
                    }                       
                  },
                  {
                    data: 'duration',
                    title: 'Duration',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      return data?data:'&dash;';  
                    }                     
                  },                  
                  {
                    data: 'wsfy_pay_rate',
                    title: 'Pay Rate',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      return data? data: '&dash;';  
                    }                     
                  }                                
               ]   
      } );

      $('.wsfy_translator_available_requests_tab').each(function() {
        $(this).click(function(){
          if(this.id == 'available_requests') {
            $('#pnl_new_request_actions').removeClass('hidden');
          } else {
            $('#pnl_new_request_actions').addClass('hidden');
          }
          wsfy_translator_available_requests.search(this.id).draw();    
        });
      });      
      $('#available_requests_select').change(function(){
        wsfy_translator_available_requests.search($(this).val()).draw();   
      });
     
      $('#wsfy_translator_available_requests').on( 'hover', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          wsfy_translator_available_requests.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
        }
      } );

      $('.deny-aprove-button').each(function() {
        $(this).click(function(){
          if(this.id == 'cancel_request'){
            if($('#p_rejected_text').hasClass('hidden')) {
              $('#p_rejected_text').removeClass('hidden');
              return;
            } else {
              $('#p_rejected_text').addClass('hidden');
            }
          }
          $('#request_details').modal('hide');
          
          $.ajax({
              type: 'POST',
              dataType: 'json',
              url: ajax_search_settings.ajaxURL,
              data: {
                  'action': 'wsfy_accept_request',
                  'post_id': $('#accepted_post_id').val(),
                  'request_action': this.id
              },
              success: function(data) {
                $('#modalMsg .modal-body').html(data.msg);
                $('#modalMsg').modal('show');
                
                if(data.success) {
                  wsfy_translator_available_requests.ajax.reload(function(){}, false);
                }
              },
              error: function(errorThrown) {
              }
          }); 
        });           
        
      });

      $('#wsfy_translator_available_requests tbody').on( 'click', 'tr', function () {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajax_search_settings.ajaxURL,
            data: {
                'action': 'wsfy_get_request_details',
                'post_id': $('tr.selected input').val()
            },
            success: function(data) {
              if(data.success)
              {
                if(data.hide_buttons) {
                  $('#pnl_new_request_actions').addClass('hidden');
                } else {
                  $('#pnl_new_request_actions').removeClass('hidden');  
                } 
                if(data.show_cancel_start_buttons) {
                  $('.start-actions').removeClass('hidden');  
                } else {
                  $('.start-actions').addClass('hidden');    
                } 
                if(data.show_finish_button) {
                  $('#finish_request').removeClass('hidden');  
                } else {
                  $('#finish_request').addClass('hidden');    
                }
                if(data.show_cancel_start_buttons || data.show_finish_button) {
                  $('#pnl_my_request_actions').removeClass('hidden');    
                } else {
                  $('#pnl_my_request_actions').addClass('hidden');   
                }
                if(data.hide_start_button) {
                  $('#start_request').addClass('hidden');
                } else {
                  $('#start_request').removeClass('hidden');  
                }
                
                             
                $('#request_details .modal-body').html(data.html);
                $('#request_details').modal('show');
                
              } else {
              }
            },
            error: function(errorThrown) {
            }
        });            
      } ); 
             
  } );
})(jQuery);