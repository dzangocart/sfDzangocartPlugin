(function($) {
  $.fn.orders = function(method) {

    var settings;
    var methods = {
      init: function(options) {

        settings = $.extend($.fn.orders.defaults, options);

        return this.each(function() {
          var $this = $(this);
          
          helpers.initOrders($this);
        });
      }
    };

    // Private methods
    var helpers = {

      initRefundForm: function(refund_form, control, t, cancelMessage) {
        if (control.hasClass('control-blocked')) {
          return false;
        }
        if (confirm(cancelMessage)) {
          // Show modal window with refund form
          $.modal(refund_form, {
            overlayClose : true,
            zIndex : 100
          });

          $('#simplemodal-container .refund-form .cancelAction').click(function() {
            // Close window
            $('#simplemodal-container .simplemodal-close').click();
          });

          $('#simplemodal-container .refund-form').submit(function() {
            var form_array = $(this).serializeArray(),
                form_data  = {};
            // Close window
            $('#simplemodal-container .simplemodal-close').click();

            $.each(form_array, function(i) {
              form_data[this.name] = this.value;
            });
            // Convert refund date to the YYYY-MM-DD format
            if (form_data['refund_info[date]']) {
              form_data['refund_info[date]'] = Date.fromString(form_data['refund_info[date]'])
                                                   .asString('yyyy-mm-dd');
            }

            control.addClass('control-blocked');
            $.ajax({
              url: control.attr('href'),
              dataType: 'json',
              type: 'post',
              data: form_data,
              success: function(data, textStatus, jqXHR) {
                control.removeClass('control-blocked');
                if (data && typeof data.error != 'undefined') {
                  alert(data.error);
                }
                t.fnDraw(false);
              },
              error: function(jqXHR, textStatus, errorThrown) {
                control.removeClass('control-blocked');
              }
            });
            return false;
          });

          // Init datepicker
          if ($('input[name=\'refund_info[date]\']').length) {
            $('input[name=\'refund_info[date]\']').datePicker({
              clickInput: true,
              startDate: '01/01/2010',
              createButton: false
            });
          }
        }
      },

      initOrders: function(container) {
        var t = $(container).dataTable({
          aaSorting: [[1, "asc"]],
          aoColumnDefs: [{sClass: 'amount', aTargets: [4, 5, 6, 7]},
                         {sClass: 'check', aTargets: [9]},
                         {sType: 'eu_date', aTargets: [0]},
                         {sType: 'formatted-num', aTargets: [4, 5, 6, 7]},
                         {bSortable: true, aTargets: [0, 1, 2, 4, 5, 6]},
                         {bSortable: false, aTargets: [3, 7, 8, 9, 10]}],
          asStripClasses: [],
          bAutoWidth: false,
          bDestroy: true,
          bProcessing: true,
          bPaginate: true,
          bServerSide: true,
          bStateSave: true,
          fnDrawCallback: function() {
            $('.with-details .order-details').click();
          },
          fnServerParams: function(aoData) {
            var openDetails = [];
            $('td.details').parent().prev('tr').find('td:eq(1)').each(function() {
              openDetails.push($(this).text());
            });
            aoData.push({
              'name': 'details',
              'value': openDetails.join(',')
            });
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
          oLanguage: {sUrl: settings.lang},
          sAjaxSource: settings.ajaxSource,
          sPaginationType: "full_numbers"
        });

        // Save refund form into a variable
        var refund_form = $('.refund-form-container').html();
        $('.refund-form-container').html('');
        // Set date format
        Date.format = settings.date_format;

        // send cancellation request and refresh the table
        $('.order-cancel', container).live('click', function() {
          helpers.initRefundForm(refund_form, $(this), t, settings.orderCancelMessage);
          return false;
        });

        $('.item-cancel', container).live('click', function() {
          helpers.initRefundForm(refund_form, $(this), t, settings.itemCancelMessage);
          return false;
        });

        $('.order-details', container).live('click', function() {
          var nTr = $(this).closest('tr')[0];
          if ($(this).hasClass('details-close')) {
            /* This row is already open - close it */
            $(this).attr('src', settings.zoomIn)
                   .removeClass('details-close')
                   .addClass('details-show');
            t.fnClose(nTr);
          }
          else {
            /* Open this row */
            $(this).attr('src', settings.zoomOut)
                   .removeClass('details-show')
                   .addClass('details-close');
            t.fnOpen(nTr, t.fnGetData(nTr).details, 'details');
          }
        });

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

  $.fn.orders.defaults = {
    date_format: 'dd/mm/yyyy'
  };

})(jQuery);