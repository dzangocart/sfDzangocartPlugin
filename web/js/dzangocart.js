(function($) {
  $.fn.dzangocart = function(method) {

    var settings;
    var cancelling = false;

    var methods = {
      init: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
        });
      },

      purchases: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initPurchases($this);
        });
      },

      orders: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initOrders($this);
        });
      },

      po: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initPo($this);
        });
      },

      paypalExpress: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initPaypalExpress($this);
        });
      },

      paypalDirect: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initPaypalDirect($this);
        });
      },

      sips: function(options) {
        settings = $.extend(true, {}, this.dzangocart.defaults, options);
        return this.each(function() {
          var $this = $(this);
          helpers.initSips($this);
        });
      }
    };

    var helpers = {
      initCancel: function(url, table, settings) {
        $.ajax({
          url: url,
          error: function() { document.location.reload(true); },
          success: function(data) {
            $.modal(data, {
              containerId: 'dzangocart-cancel-container',
              overlayId: 'dzangocart-cancel-overlay',
              onShow: function(dialog) {
                $('a.cancel', dialog.data).click(function() {
                  $.modal.close();
                });

                $('input.dateInput', dialog.data).datePicker({
                  dateFormat: settings.date_format,
                  startDate: settings.start_date,
                  clickInput: true,
                  createButton: true
                });

                $.modal.update();

                $('form', dialog.data).submit(function() {
                  var form = $(this);
                  $.ajax({
                    url: $(this).attr('action'),
                    dataType: 'json',
                    type: 'post',
                    data: form.serialize(),
                    success: function(data) {
                      if (data && typeof data.error != 'undefined') {
                      }
                      else {
                        table.fnDraw(false);
                        $.modal.close();
                      }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                      alert(settings.debug);
                      if (settings.debug) { console.log(errorThrown); }
                      else { document.location.reload(true); }
                    }
                  });
                  return false;
                });
              }
            });
          }

        });
      },

      initRefundForm: function(refund_form, control, t, cancelMessage) {
        if (control.hasClass(settings.blocking_class)) {
          return false;
        }
        if (confirm(cancelMessage)) {
          // Show modal window with refund form
          $.modal(refund_form, {
            overlayClose : true,
            zIndex : settings.overlay_zindex
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

            control.addClass(settings.blocking_class);
            $.ajax({
              url: control.attr('href'),
              dataType: 'json',
              type: 'post',
              data: form_data,
              success: function(data, textStatus, jqXHR) {
                alert(data);
                control.removeClass(settings.blocking_class);
                if (data && typeof data.error != 'undefined') {
                  alert(data.error);
                }
                t.fnDraw(false);
              },
              error: function(jqXHR, textStatus, errorThrown) {
                control.removeClass(settings.blocking_class);
              }
            });
            return false;
          });

          // Init datepicker
          if ($('input[name=\'refund_info[date]\']').length) {
            $('input[name=\'refund_info[date]\']').datePicker({
              clickInput: true,
              dateFormat: settings.date_format,
              startDate: settings.satart_date,
              createButton: false
            }).click(function() {
              $('#dp-popup').css('z-index', settings.overlay_zindex + 50);
            });
          }
        }
      },

      initDatePickers: function(e) {
        $('.dateInput', e).datePicker({
          dateFormat: settings.date_format,
          startDate: settings.start_date,
          clickInput: false,
          createButton: true
        });
      },

      initFilters: function(t) {
        $('input.dateInput').change(function() {
          t.fnDraw(true);
        });
        $('#test').change(function() {
          t.fnDraw(false);
          return true;
        });
      },

      initAjaxActions: function(t) {
        $('.actions a.' + settings.ajax_class).live('click', function() {
          var $this = $(this);
          if ($this.hasClass(settings.blocking_class)) {
            return false;
          }
          $this.addClass(settings.blocking_class);
          $.ajax({
            url: $this.attr('href'),
            dataType: 'json',
            type: 'post',
            data: {},
            success: function(data, textStatus, jqXHR) {
              $this.removeClass(settings.blocking_class);
              t.fnDraw(false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              $this.removeClass(settings.blocking_class);
            }
          });

          return false;
        });
      },

      initBatchActions: function(e) {
        $('th.check input[type=checkbox]', e).click(function() {
          var check = this.checked;
          $('.batch-action').each(function() {
            this.checked = check;
          });
        });
      },

      initDataTable: function(e, dataTableSettings) {
        dataTableSettings = $.extend(true, {}, settings.datatable, dataTableSettings);
        if (dataTableSettings.oLanguage === undefined) {
          dataTableSettings.oLanguage = {
            sUrl: '/cpDataTablesPlugin/lang/' +  settings.lang + '.txt'
          }
        }
        return $(e).dataTable(dataTableSettings);
      },

      initPurchases: function(container) {
        // Save refund form into a variable
        var refund_form = $('.refund-form-container').html();
        $('.refund-form-container').html('');
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.purchases', container),
                                      settings.purchases.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
        helpers.initAjaxActions(t);
        helpers.initBatchActions(container);

        $('.item-cancel').live('click', function() {
          helpers.initRefundForm(refund_form, $(this), t, settings.purchases.itemCancelMessage);
          return false;
        });
      },

      initOrders: function(container) {
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.orders', container),
                                      settings.orders.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
        helpers.initAjaxActions(t);

        // send cancellation request and refresh the table
        $('a.cancel', container).live('click', function() {
          helpers.initCancel($(this).attr('href'), t, settings);
//          helpers.initRefundForm(refund_form, $(this), t, settings.orders.orderCancelMessage);
          return false;
        });

        $('.item-cancel', container).live('click', function() {
          helpers.initRefundForm(refund_form, $(this), t, settings.orders.itemCancelMessage);
          return false;
        });

        $('a.details', container).live('click', function() {
          var row = $(this).closest('tr')[0];
          if ($(this).hasClass('showing')) {
            t.fnClose(row);
          }
          else {
            t.fnOpen(row, t.fnGetData(row).details, 'details');
          }
          $(this).toggleClass('showing');
          return false;
        });
      },

      initPo: function(container) {
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.po', container), settings.po.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
      },

      initPaypalExpress: function(container) {
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.paypal', container), settings.paypalExpress.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
      },

      initPaypalDirect: function(container) {
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.paypal', container), settings.paypalDirect.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
      },

      initSips: function(container) {
        // Set date format
        Date.format = settings.date_format;

        var t = helpers.initDataTable($('table.sips', container), settings.sips.datatable);
        helpers.initDatePickers(container);
        helpers.initFilters(t);
      }
    };

    if (methods[method]) {
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    }
    else if (typeof method === 'object' || !method) {
      return methods.init.apply(this, arguments);
    }
    else {
      $.error('Method ' +  method + ' does not exist in jQuery.dzangocart.');
    }

  };

  $.fn.dzangocart.defaults = {
    ajax_class:     'ajax',
    blocking_class: 'control-blocked',
    date_format:    'dd/mm/yyyy',
    lang:           'fr',
    overlay_zindex: 1000,
    start_date:     '01/01/2010 ',
    datatable: {
      asStripClasses: [],
      bAutoWidth:     false,
      bDestroy:       true,
      bProcessing:    true,
      bPaginate:      true,
      bServerSide:    true,
      bStateSave:     true,
      fnDrawCallback: function() {
        if ($('th.check').hasClass('hidden')) {
          $('td.check').hide();
        }
      },
      fnServerParams: function(aoData) {
        aoData.push({name: 'date_from', value: $('#date_from').val()});
        aoData.push({name: 'date_to', value: $('#date_to').val()});
        aoData.push({name: 'test', value: $('#test:checked').length ? 1 : 0});
      },
      sAjaxSource:     document.location.href,
      sPaginationType: "full_numbers"
    },
    purchases: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [
          { sClass: 'amount', aTargets: [5, 6, 7] },
          { sClass: 'check', aTargets: [0] },
          { sClass: 'center', aTargets: [8, 9] },
          { sClass: 'actions', aTargets: [10] },
          { sType: 'eu_date', aTargets: [2] },
          { sType: 'formatted-num', aTargets: [5, 6, 7] },
          { bSortable: true, aTargets: [1, 2, 3, 4, 5, 6, 7] },
          { bSortable: false, aTargets: [0, 8, 9, 10] }
        ],
        asStripeClasses: []
      },
      itemCancelMessage:  ''
    },
    orders: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [{sClass: 'amount', aTargets: [5, 6, 7, 8]},
                       {sClass: 'check', aTargets: [0]},
                       {sClass: 'actions', aTargets: [11]},
                       {sType: 'eu_date', aTargets: [1]},
                       {sType: 'formatted-num', aTargets: [5, 6, 7, 8]},
                       {bSortable: true, aTargets: [1, 2, 3, 5, 6, 7]},
                       {bSortable: false, aTargets: [0, 4, 8, 9, 10, 11]}],
        asStripeClasses: [],
        fnDrawCallback: function() {
          if ($('th.check').hasClass('hidden')) {
            $('td.check').hide();
          }
          $('.with-details .order-details').click();
        },
        fnServerParams: function(aoData) {
          var openDetails = [];
          $('td.details').parent().prev('tr').find('td:eq(1)').each(function() {
            openDetails.push($(this).text());
          });
          aoData.push({name: 'details', value: openDetails.join(',')});
          aoData.push({name: 'test', value: $('#test:checked').length ? 1 : 0});
          aoData.push({name: 'date_from', value: $('#date_from').val()});
          aoData.push({name: 'date_to', value: $('#date_to').val()});
        }
      },
      itemCancelMessage:  '',
      orderCancelMessage: '',
      zoomIn:             '',
      zoomOut:            ''
    },
    po: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [{sClass: 'amount', aTargets: [5]},
                       {sClass: 'check', aTargets: [0]},
                       {sClass: 'actions', aTargets: [9]},
                       {sType: 'eu_date', aTargets: [1]},
                       {sType: 'formatted-num', aTargets: [5]},
                       {bSortable: true, aTargets: [1, 2, 3, 4, 5, 7]},
                       {bSortable: false, aTargets: [0, 6, 8, 9]}]
      }
    },
    paypalExpress: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [{sClass: 'amount', aTargets: [4]},
                       {sClass: 'check', aTargets: [0]},
                       {sClass: 'actions', aTargets: [6]},
                       {sType: 'eu_date', aTargets: [1]},
                       {sType: 'formatted-num', aTargets: [4]},
                       {bSortable: true, aTargets: [1, 2, 3, 4]},
                       {bSortable: false, aTargets: [0, 5, 6]}]
      }
    },
    paypalDirect: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [{sClass: 'amount', aTargets: [4]},
                       {sClass: 'check', aTargets: [0]},
                       {sClass: 'actions', aTargets: [7]},
                       {sType: 'eu_date', aTargets: [1]},
                       {sType: 'formatted-num', aTargets: [4]},
                       {bSortable: true, aTargets: [1, 2, 3, 4, 6]},
                       {bSortable: false, aTargets: [0, 5, 7]}]
      }
    },
    sips: {
      datatable: {
        aaSorting: [[1, "asc"]],
        aoColumnDefs: [{sClass: 'amount', aTargets: [2]},
                       {sClass: 'check', aTargets: [0]},
                       {sClass: 'actions', aTargets: [12]},
                       {sType: 'eu_date', aTargets: [1]},
                       {sType: 'formatted-num', aTargets: [2]},
                       {bSortable: true, aTargets: [1, 2, 3, 5, 6, 7, 8, 9, 10]},
                       {bSortable: false, aTargets: [0, 4, 11, 12]}]
      }
    }
  };
})(jQuery);

var dzangocart = dzangocart || {};

$(document).ready(function() {
  if ($('div.purchases').length) {
    $('div.purchases').dzangocart('purchases', dzangocart);
  }

  $('#orders').dzangocart('orders', dzangocart);

  if ($('div.po').length) {
    $('div.po').dzangocart('po', dzangocart);
  }
  if ($('div.paypal-express').length) {
    $('div.paypal-express').dzangocart('paypalExpress', dzangocart);
  }
  if ($('div.paypal-direct').length) {
    $('div.paypal-direct').dzangocart('paypalDirect', dzangocart);
  }
  if ($('div.sips').length) {
    $('div.sips').dzangocart('sips', dzangocart);
  }
});