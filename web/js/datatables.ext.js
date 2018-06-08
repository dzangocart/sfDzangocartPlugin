(function($) {  
  function calculate_date(date) {
    var date = date.replace(" ", "");

    if (date.indexOf('.') > 0) {
      /*date a, format dd.mn.(yyyy) ; (year is optional)*/
      var eu_date = date.split('.');
    }
    else if (date.indexOf('/') > 0){
      /*date a, format dd/mn/(yyyy) ; (year is optional)*/
      var eu_date = date.split('/');
    }
    else if (date.indexOf('-') > 0){
      /*date a, format dd-mn-(yyyy) ; (year is optional)*/
      var eu_date = date.split('-');
    }
    else { return 0; }


    /*year (optional)*/
    if (eu_date[2]) { var year = eu_date[2]; }
    else { var year = 0; }

    /*month*/
    var month = eu_date[1];
    if (month.length == 1) {
      month = 0+month;
    }

    /*day*/
    var day = eu_date[0];
    if (day.length == 1) {
      day = 0+day;
    }

    return (year + month + day) * 1;
  }
  
  $.fn.dataTableExt.oSort['amount-asc']  = function(a, b) {
    x = (a == "-") ? 0 : a.replace(/([.,\s])|(&nbsp;)/g, '');
    y = (b == "-") ? 0 : b.replace(/([.,\s])|(&nbsp;)/g, '');
    return parseInt(x) - parseInt(y);
  };

  $.fn.dataTableExt.oSort['amount-desc'] = function(a, b) {
    x = (a == "-") ? 0 : a.replace(/([.,\s])|(&nbsp;)/g, '');
    y = (b == "-") ? 0 : b.replace(/([.,\s])|(&nbsp;)/g, '');
    return parseInt(y) - parseInt(x);
  };

  $.fn.dataTableExt.oSort['test-asc'] = function(a, b) {
    var x = a.toLowerCase();
    var y = b.toLowerCase();
    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
	};

  $.fn.dataTableExt.oSort['test-desc'] = function(a, b) {
    var x = a.toLowerCase();
    var y = b.toLowerCase();
    return ((x < y) ? 1 : ((x > y) ? -1 : 0));
  };

  // filter by date
  $.fn.dataTableExt.afnFiltering.push(
    function(oSettings, aData, iDataIndex) {
      var dateFrom = $('#date_from').length ? calculate_date($('#date_from').val()) : 0;
      var dateTo = $('#date_to').length ? calculate_date($('#date_to').val()) : 0;

      if (dateFrom == 0 && dateTo == 0) { return true; }

      // check index of the column with 'eu_date' type
      var colNum = -1;
      $(oSettings.aoColumns).each(function(i) {
        if (this.sType == 'eu_date') {
          colNum = i;
          return;
        }
      });
      if (colNum < 0) { return true; }

      var date = calculate_date(aData[colNum]);
      
      if ((dateFrom == 0 || dateFrom <= date) && (dateTo == 0 || dateTo >= date)) { return true; }
      
      return false;
    }
  );

  // filter test
  $.fn.dataTableExt.afnFiltering.push(
    function(oSettings, aData, iDataIndex) {
      if (!$('#test').length) { return true; }

      // check index of the column with 'test' type
      var colNum = -1;
      $(oSettings.aoColumns).each(function(i) {
        if (this.sType == 'test') {
          colNum = i;
          return;
        }
      });
      if (colNum < 0) { return true; }

      if ($('#test:checked').length) {
        return true;
      }
      else if (aData[colNum]) {
        return false;
      }

      return true;
    }
  );
    
})(jQuery);
