(function() {
  (function($) {
    var disableFormButtonsAfterSubmit, enableTextareaAutoselect, formButtonsOriginalAttrName, initPreviewButtons, loadAmazonTrackingIDs, loadLazyLoad, loadWidgetLayouts, loadWidgets, reenableFormButtons, round, watchKeywordInputs, watchWidgetForm;
    $(function() {
      enableTextareaAutoselect();
      disableFormButtonsAfterSubmit();
      if ($('body.productwidgets_page_productwidgets-settings').length) {
        return loadAmazonTrackingIDs();
      } else if ($('body.toplevel_page_productwidgets-widgets').length) {
        return loadWidgets();
      } else if ($('body.productwidgets_page_productwidgets-add-widget').length) {
        loadWidgetLayouts();
        watchKeywordInputs();
        return watchWidgetForm();
      }
    });
    enableTextareaAutoselect = function() {
      return $(document).on('click', '.autoselect', function() {
        return this.select();
      });
    };
    formButtonsOriginalAttrName = 'data-original-value';
    disableFormButtonsAfterSubmit = function() {
      return $(document).on('submit', 'form', function() {
        var $submit;
        $submit = $('input[type="submit"]', this);
        return $submit.attr(formButtonsOriginalAttrName, $submit.val()).val('Please wait...').attr('disabled', 'disabled');
      });
    };
    loadAmazonTrackingIDs = function() {
      var countries, signup_links;
      countries = {
        de: 'Amazon.de (Germany)',
        gb: 'Amazon.co.uk (United Kingdom)',
        us: 'Amazon.com (United States)',
        ca: 'Amazon.ca (Canada)',
        es: 'Amazon.es (Spain)',
        fr: 'Amazon.fr (France)',
        it: 'Amazon.it (Italy)'
      };
      signup_links = {
        de: 'https://partnernet.amazon.de/',
        gb: 'https://affiliate-program.amazon.co.uk/',
        us: 'https://affiliate-program.amazon.com/',
        ca: 'https://associates.amazon.ca/',
        es: 'https://afiliados.amazon.es/',
        fr: 'https://partenaires.amazon.fr/',
        it: 'https://programma-affiliazione.amazon.it/'
      };
      return $.get("" + ajaxurl + "?action=get_amazon_tracking_ids", function(response) {
        var $row, $table, $td, $th, locale, trackingId;
        $('.ajax-loader').hide();
        if (typeof response === 'string') {
          return $('#no-settings').show();
        } else {
          $table = $('#amazon table');
          for (locale in response) {
            trackingId = response[locale];
            $row = $('tr:first-child', $table).clone();
            $th = $('th', $row);
            $td = $('td', $row);
            $th.text(countries[locale]);
            $('input', $td).attr('name', "tracking_ids[" + locale + "]").val(trackingId);
            if (!(trackingId != null ? trackingId.length : void 0)) {
              $td.append("<span>Sign up here: <a href='" + signup_links[locale] + "' target='_blank'>" + signup_links[locale] + "</a></span>");
            }
            $row.appendTo($table);
          }
          $('tr:first-child', $table).remove();
          return $('#amazon').show();
        }
      });
    };
    reenableFormButtons = function() {
      return $("input[type='submit'][" + formButtonsOriginalAttrName + "]").each(function(_, el) {
        var originalValue;
        originalValue = $(el).attr(formButtonsOriginalAttrName);
        return $(el).val(originalValue).removeAttr('disabled').removeAttr(formButtonsOriginalAttrName);
      });
    };
    loadLazyLoad = function(selector) {
      return $('img.lazy', selector).show().lazyload({
        effect: 'fadeIn'
      });
    };
    loadWidgets = function() {
      var $content;
      $content = $("#content");
      if ($content.length) {
        return $content.load("" + ajaxurl + "?action=get_widgets", function() {
          loadLazyLoad();
          return initPreviewButtons();
        });
      }
    };
    initPreviewButtons = function() {
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
    round = function(number, digits) {
      return parseFloat(number.toFixed(digits));
    };
    loadWidgetLayouts = function() {
      return $.get("" + ajaxurl + "?action=get_widget_layouts", function(response) {
        var $form, $td, checkedString, first, iconHeight, iconVerticalMargin, iconWidth, id, maxIconHeight, widgetLayout, widgetLayoutNames, _i, _len, _ref;
        $('.ajax-loader').hide();
        $form = $('form');
        if (typeof response === 'string') {
          return $form.html(response);
        } else {
          $('body').data('widget_layouts', response);
          $td = $('tr#layout td');
          widgetLayoutNames = [];
          first = true;
          maxIconHeight = 55;
          widgetLayoutNames = [];
          for (_i = 0, _len = response.length; _i < _len; _i++) {
            widgetLayout = response[_i];
            if (jQuery.inArray(widgetLayout['name'], widgetLayoutNames) > -1) {
              continue;
            } else {
              widgetLayoutNames.push(widgetLayout['name']);
            }
            checkedString = first ? 'checked="checked" ' : '';
            id = ['widget-layout', widgetLayout['name'].toLowerCase().replace(/\s+/g, '-')].join('-');
            _ref = $.map(['width', 'height'], function(attr) {
              return round(widgetLayout[attr] / 8, 2);
            }), iconWidth = _ref[0], iconHeight = _ref[1];
            if (iconHeight > maxIconHeight) {
              iconWidth = round(iconWidth * maxIconHeight / iconHeight, 2);
              iconHeight = maxIconHeight;
            }
            iconVerticalMargin = round((maxIconHeight - iconHeight) / 2, 2);
            $td.append(("<input type='radio' name='widget-layout' id='" + id + "' value='" + widgetLayout['name'] + "' " + checkedString + "/>") + ("<label for='" + id + "'>") + ("<div class='icon' style='width: " + iconWidth + "px; height: " + iconHeight + "px; margin-top: " + iconVerticalMargin + "px; margin-bottom: " + iconVerticalMargin + "px'></div>") + ("<div class='name'>" + widgetLayout['name'] + "</div>") + ("<div class='size'>" + widgetLayout['width'] + "px &times; " + widgetLayout['height'] + "px</div>") + "</label>");
            if (first) {
              first = false;
            }
          }
          return $form.show();
        }
      });
    };
    watchKeywordInputs = function() {
      return $(document).on('click', 'input[name="keywords"]', function(e) {
        return $('input#keywords_manual').prop('checked', true);
      });
    };
    return watchWidgetForm = function() {
      return $(document).on('submit', 'form', function(e) {
        var effect, keywords, productsType, shortcode, widgetLayout, widgetLayoutName, widgetLayouts;
        e.preventDefault();
        widgetLayoutName = $('input[name="widget-layout"]:checked').val();
        effect = $('input[name="effect"]:checked').val();
        widgetLayouts = $('body').data('widget_layouts');
        widgetLayout = $.grep(widgetLayouts, function(widgetLayout) {
          return widgetLayout['name'] === widgetLayoutName && widgetLayout['configuration']['show_slider'] === (effect === 'slider');
        });
        productsType = $('input[name="products"]:checked').val();
        if (productsType === 'automated_title') {
          keywords = ['TITLE'];
        } else {
          keywords = $('input[name="keywords"]').map(function(i, el) {
            var keyword;
            keyword = $(el).val().toLowerCase().trim();
            if (keyword.length === 0) {
              return null;
            } else {
              return keyword;
            }
          }).get();
          if (keywords.length === 0) {
            alert('Please enter at least one keyword.');
            $('input[name="keywords"]:first').focus();
            reenableFormButtons();
            return;
          }
        }
        shortcode = "[productwidget layout=\"" + widgetLayout[0]['identifier'] + "\" keywords=\"" + (keywords.join(',')) + "\"]";
        $('tr#preview td #widget').empty();
        $.get("" + ajaxurl + "?action=parse_widget_shortcode&shortcode=" + shortcode, function(response) {
          $('tr#preview td #widget').html(response);
          $('#automated-title-note').toggle(productsType === 'automated_title');
          return reenableFormButtons();
        });
        $('tr#shortcode textarea').val(shortcode);
        return $('tr#shortcode, tr#preview').show();
      });
    };
  })(jQuery);

}).call(this);
