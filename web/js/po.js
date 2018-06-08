(function($) {
  $.fn.po = function(method) {

    var settings;
    var methods = {
      init: function(options) {

        settings = $.extend($.fn.po.defaults, options);

        return this.each(function() {
          var $this = $(this);
          
          helpers.initPO($this);
        });
      }
    };

    // Private methods
    var helpers = {

      initPO: function(container) {
        var t = $(container).dataTable({
          bProcessing: true,
          bServerSide: true,
          sAjaxSource: settings.ajaxSource,
          'fnServerParams': function(aoData) {
            /*var openDetails = [];
            $('td.details').parent().prev('tr').find('td:eq(1)').each(function() {
              openDetails.push($(this).text());
            });
            aoData.push({
              'name': 'details',
              'value': openDetails.join(',')
            });*/
            aoData.push({
              'name': 'test',
              'value': $('#test:checked').length ? 1 : 0
            });
            aoData.push({
              'name': 'date_from',
              'value': $('#date_from').val()
            });
            aoData.push({
              'name': 'date_to',
              'value': $('#date_to').val()
            });            
          },
          /*'fnDrawCallback': function() {
            $('.with-details .po-details').click();
          },*/
          oLanguage: {sUrl: settings.lang}
        });

        /*$('.po-details', container).live('click', function() {
          var nTr = $(this).closest('tr')[0];
          if ($(this).hasClass('details-close')) {
            // This row is already open - close it
            $(this).attr('src', settings.zoomIn)
                   .removeClass('details-close')
                   .addClass('details-show');
            t.fnClose(nTr);            
          }
          else {
            // Open this row
            $(this).attr('src', settings.zoomOut)
                   .removeClass('details-show')
                   .addClass('details-close');
            t.fnOpen(nTr, t.fnGetData(nTr).details, 'details');
          }
        });*/

        Date.format = settings.date_format;

        $("#date_from,#date_to").datePicker({
          clickInput: true,
          startDate: '01/01/2010',
          createButton: false
        }).bind('dateSelected', function(e, selectedDate, $td) {
                                  t.fnDraw(false);
                                });
                                
        $('#test').change(function() {
          t.fnDraw(false);
          return true;
        });
      }
    };

    // Method calling logic
    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    }
    else if (typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    }
    else {
      $.error( 'Method ' +  method + ' does not exist' );
    }
  };

  $.fn.po.defaults = {
    date_format: 'dd/mm/yyyy'
  };

})(jQuery);