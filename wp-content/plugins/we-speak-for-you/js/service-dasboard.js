;(function ($) {
  "use strict";
  $(document).ready(function() {
      var wsfy_service_dashboard = $('#wsfy_service_dashboard').DataTable( {
          "processing": true,
          "serverSide": true,
          "bFilter" : true, 
          'fnDrawCallback': function (oSettings) {
        		$('.dataTables_filter').each(function () {
        			$(this).html('<button onclick="window.location.assign(\''+wsfy_data.request_service_url+'\');" class="btn btn-default mr-xs pull-right" type="button">Request a translator</button>');
        		});
        	},
          "ajax": ajax_search_settings.ajaxURL+"?action=wsfy_service_dashboard_data",
              "columns": [
                  {
                    data: 'wsfy_appointment_type',
                    title: 'Appointment Type',
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
                    data: 'request_id',
                    title: 'Order #',
                    className: 'text-right',
                    render: function(data, type, full, meta)
                    {
                      data = data + '<input type="hidden" value="'+data+'" />';
                      return data;  
                    }                       
                  }                
               ]   
      } );
     
      $('#wsfy_service_dashboard tbody').on( 'hover', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
          $(this).removeClass('selected');
        } else {
          wsfy_service_dashboard.$('tr.selected').removeClass('selected');
          $(this).addClass('selected');
        }
      } );        
      $('#wsfy_service_dashboard tbody').on( 'click', 'tr', function () {
         window.location.assign(wsfy_data.request_service_url+'?request='+$('tr.selected input').val());
      } );         
  } );
})(jQuery);