;(function ($) {
  "use strict";
  $(document).ready(function() {
      $('body').prepend('<div class="modal fade bd-example-modal-sm" id="request_details"> <div class="modal-dialog modal-sm"> <div class="modal-content"> <div class="modal-header"> <a type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </a> <h4 class="modal-title">Appointment Details</h4> </div> <div class="modal-body"> </div> <div class="modal-footer"> <button id="reject_request" type="button" class="btn btn-secondary">Reject</button> <button id="approve_request" type="button" class="btn btn-primary" data-dismiss="modal">Approve</button> </div> </div><!-- /.modal-content --> </div><!-- /.modal-dialog --></div><!-- /.modal --> ');
    
      var wsfy_requests = $('#wsfy_requests').DataTable( {
          "processing": true,
          "serverSide": true,
          "bFilter" : true,
          "oSearch": {"sSearch": "all_requests"},
          "dom": '<<t>lp>',     
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
                    data: 'status',
                    title: 'Status',
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
                    data: 'wsfy_cost',
                    title: 'Cost',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      return data? data: '&dash;';  
                    }                     
                  },                  
                  {
                    data: 'wsfy_pay_rate',
                    title: 'Payout',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      return data? data: '&dash;';  
                    }                     
                  },
                  {
                    data: 'request_id',
                    title: 'Order #',
                    className: 'text-right',
                  },                               
                  {
                    data: 'wsfy_attached_file',
                    title: 'Attached File',
                    className: 'text-right unclickable',
                    bSortable: false,
                    render: function(data, type, full, meta)
                    {
                      return data? '<a class="download-attached" href="'+wsfy_data.site_url+data+'" target="_blank">Download</a>': '&dash;';  
                    }                     
                  }
               ]   
      } );
      
      $('#wsfy_requests').on( 'hover', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          wsfy_requests.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
        }
      } );

      $('#available_requests_select').change(function(){
        wsfy_requests.search($(this).val()).draw();   
      });

      $('#export_requests').click(function(){
        $.ajax({
          type: 'GET',
          dataType: 'json',
          url: ajax_search_settings.ajaxURL,
          data: {
              'action': 'wsfy_export_requests',
              'request_type': $('#available_requests_select').val()
          },
          success: function(data) {
            if(data.success){
              if(data.file_url){
                window.location.assign(data.file_url);  
              }
            } else {
            }
          },
          error: function(errorThrown) {
          }
        });           
      });

      $('#request_details .modal-footer button').each(function() {
        $(this).click(function(){
          if(this.id == 'reject_request'){
            if($('#p_rejected_text').hasClass('hidden')) {
              $('#p_rejected_text').removeClass('hidden');
              return;
            } else {
              $('#p_rejected_text').addClass('hidden');
              $('#request_details').modal('hide');  
            }
          }
          $.ajax({
              type: 'POST',
              dataType: 'json',
              url: ajax_search_settings.ajaxURL,
              data: {
                  'action': 'wsfy_accept_request',
                  'post_id': $('#accepted_post_id').val(),
                  'rejected_text': $('#rejected_text').val(),
                  'request_action': this.id
              },
              success: function(data) {
                console.log(data);
                if(data.success)
                {
                  $('#modalMsg .modal-body').html(data.msg);
                  $('#modalMsg').modal('show');
                  wsfy_requests.ajax.reload(function(){}, false);
                } else {
                  $('#modalMsg .modal-body').html(data.msg);
                  $('#modalMsg').modal('show');                
                }
              },
              error: function(errorThrown) {
              }
          });            
        });
      }); 

      $('#wsfy_requests tbody').on( 'click', 'td', function () {
        if($(this).hasClass('unclickable')) {
          return;
        }
        
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
                  $('#request_details .modal-footer').addClass('hidden');
                } else {
                  $('#request_details .modal-footer').removeClass('hidden');  
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
      
      $('.btn-payout-setting').each(function() {
              $(this).click(function(){
                var data = [];
                var form = 'pnl_'+this.id;
                var user_type = this.id;
                
                $('#'+form+' .setting-row').each(function(){
                  var duration = $(this).find('input[name="duration"]').val();
                  var payout = $(this).find('input[name="payout"]').val();
                  data[data.length] = {user_type: user_type, duration : duration, payout: payout};                  
                  
                }); 
                
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajax_search_settings.ajaxURL,
                    data: {
                        'action': 'wsfy_save_payout_settings',
                        'data': data
                    },
                    success: function(data) {
                      console.log(data);
                      if(data.success) {
                        $('#modalMsg .modal-body').html(data.msg);
                        $('#modalMsg').modal('show');                        
                      } else {
                      }
                    },
                    error: function(errorThrown) {
                    }
                });                       
              });
      });        

      $('.btn-cost-setting').each(function() {
              $(this).click(function(){
                var data = [];
                var form = 'pnlcost_'+this.id;
                var appointment_type = this.id;
                
                $('#'+form+' .setting-row').each(function(){
                  var duration = $(this).find('input[name="duration"]').val();
                  var cost = $(this).find('input[name="cost"]').val();
                  data[data.length] = {appointment_type: appointment_type, duration : duration, cost: cost};                  
                  
                }); 
                
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajax_search_settings.ajaxURL,
                    data: {
                        'action': 'wsfy_save_cost_settings',
                        'data': data
                    },
                    success: function(data) {
                      console.log(data);
                      if(data.success) {
                        $('#modalMsg .modal-body').html(data.msg);
                        $('#modalMsg').modal('show');                        
                      } else {
                      }
                    },
                    error: function(errorThrown) {
                    }
                });                       
                console.log(data);
              }); 
      });                
             
  } );
})(jQuery);