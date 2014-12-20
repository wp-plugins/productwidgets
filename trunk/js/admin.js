(function() {
  (function($) {
    var disableFormButtonsAfterSubmit, enableTextareaAutoselect, initPreviewButtons, loadLazyLoad, loadTrackingIDs, loadWidgetLayouts, watchExistingAPIKeyButton;
    $(function() {
      enableTextareaAutoselect();
      disableFormButtonsAfterSubmit();
      if (window.location.href.match(/settings\.php/)) {
        watchExistingAPIKeyButton();
        return loadTrackingIDs();
      } else if (window.location.href.match(/widget-layouts\.php/)) {
        return loadWidgetLayouts();
      }
    });
    enableTextareaAutoselect = function() {
      return $(document).on('click', '.autoselect', function() {
        return this.select();
      });
    };
    disableFormButtonsAfterSubmit = function() {
      return $('form').submit(function() {
        return $('input[type="submit"]', this).val('Please wait...').attr('disabled', 'disabled');
      });
    };
    loadLazyLoad = function(selector) {
      return $('img.lazy', selector).show().lazyload({
        effect: 'fadeIn'
      });
    };
    watchExistingAPIKeyButton = function() {
      return $(document).on("click", "#existing-api-key-button", function(e) {
        e.preventDefault();
        return $("#existing-api-key-form").slideDown();
      });
    };
    loadTrackingIDs = function() {
      var $trackingIds, countries, signup_links;
      countries = {
        de: "Amazon.de (Germany)",
        gb: "Amazon.co.uk (United Kingdom)",
        us: "Amazon.com (United States)",
        ca: "Amazon.ca (Canada)",
        es: "Amazon.es (Spain)",
        fr: "Amazon.fr (France)",
        it: "Amazon.it (Italy)"
      };
      signup_links = {
        de: "https://partnernet.amazon.de/",
        gb: "https://affiliate-program.amazon.co.uk/",
        us: "https://affiliate-program.amazon.com/",
        ca: "https://associates.amazon.ca/",
        es: "https://afiliados.amazon.es/",
        fr: "https://partenaires.amazon.fr/",
        it: "https://programma-affiliazione.amazon.it/"
      };
      $trackingIds = $("#tracking-ids");
      return $.get("" + ajaxurl + "?action=get_tracking_ids", function(response) {
        var $row, $table, $td, $th, locale, trackingId;
        $(".ajax-loader", $trackingIds).hide();
        if (typeof response === 'string') {
          return $trackingIds.html(response);
        } else {
          $table = $("table", $trackingIds);
          for (locale in response) {
            trackingId = response[locale];
            $row = $("tr:first-child", $table).clone();
            $th = $("th", $row);
            $td = $("td", $row);
            $th.text(countries[locale]);
            $("input", $td).attr("name", "tracking_ids[" + locale + "]").val(trackingId);
            if (!(trackingId != null ? trackingId.length : void 0)) {
              $td.append("<span>Sign up here: <a href='" + signup_links[locale] + "' target='_blank'>" + signup_links[locale] + "</a></span>");
            }
            $row.appendTo($table);
          }
          $("tr:first-child", $table).remove();
          return $table.show();
        }
      });
    };
    loadWidgetLayouts = function() {
      return $("#widget-layouts").load("" + ajaxurl + "?action=get_widget_layouts", function() {
        loadLazyLoad();
        return initPreviewButtons();
      });
    };
    return initPreviewButtons = function() {
      return $('a.widget-preview-button').click(function(e) {
        var $button, $wrapper, script;
        $button = $(e.currentTarget);
        $wrapper = $button.siblings('.widget-preview-container').find('.widget-preview-wrapper');
        if (!$wrapper.find('.pw-widget').length) {
          script = document.createElement('script');
          script.type = 'text/javascript';
          script.src = $button.data('js-code');
          return $wrapper.append(script);
        }
      });
    };
  })(jQuery);

}).call(this);
