(function() {
  (function($) {
    var disableFormButtonsAfterSubmit, enableTextareaAutoselect, formButtonsOriginalAttrName, loadAmazonTrackingIDs, loadCategories, loadLazyLoad, loadWidgetLayoutsAndProductSources, loadWidgets, reenableFormButtons, round, watchCategoriesAndKeywords, watchWidgetForm;
    formButtonsOriginalAttrName = 'data-original-value';
    $(function() {
      enableTextareaAutoselect();
      disableFormButtonsAfterSubmit();
      if ($('body.productwidgets_page_productwidgets-settings').length) {
        return loadAmazonTrackingIDs();
      } else if ($('body.toplevel_page_productwidgets-widgets').length) {
        return loadWidgets();
      } else if ($('body.productwidgets_page_productwidgets-add-widget').length) {
        loadWidgetLayoutsAndProductSources();
        watchCategoriesAndKeywords();
        return watchWidgetForm();
      }
    });
    enableTextareaAutoselect = function() {
      return $(document).on('click', '.autoselect', function() {
        return this.select();
      });
    };
    disableFormButtonsAfterSubmit = function() {
      return $(document).on('submit', 'form', function() {
        var $submit;
        $submit = $('input[type="submit"]', this);
        return $submit.attr(formButtonsOriginalAttrName, $submit.val()).val('Please wait...').attr('disabled', 'disabled');
      });
    };
    loadLazyLoad = function(selector) {
      return $('img.lazy', selector).show().lazyload({
        effect: 'fadeIn'
      });
    };
    round = function(number, digits) {
      return parseFloat(number.toFixed(digits));
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
    loadWidgets = function() {
      var $content;
      $content = $("#content");
      if ($content.length) {
        return $content.load("" + ajaxurl + "?action=get_widgets", function() {
          loadLazyLoad();
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
        });
      }
    };
    loadWidgetLayoutsAndProductSources = function() {
      return $.when($.get("" + ajaxurl + "?action=get_product_sources"), $.get("" + ajaxurl + "?action=get_widget_layouts")).then(function(productSourcesResult, widgetLayoutsResult) {
        var $form, $layoutTd, $productSourceSelect, checkedString, errorMessages, first, iconHeight, iconVerticalMargin, iconWidth, id, maxIconHeight, productSource, productSources, widgetLayout, widgetLayoutNames, widgetLayouts, _i, _j, _len, _len1, _ref, _ref1;
        $form = $('form');
        $form.prev('.ajax-loader').hide();
        _ref = [productSourcesResult[0], widgetLayoutsResult[0]], productSources = _ref[0], widgetLayouts = _ref[1];
        errorMessages = $.grep([productSources, widgetLayouts], function(el) {
          return typeof el === 'string';
        });
        if (errorMessages.length > 0) {
          Rollbar.error(errorMessages);
          $form.html(errorMessages[0]);
        } else {
          $productSourceSelect = $form.find('tr#product-source select');
          for (_i = 0, _len = productSources.length; _i < _len; _i++) {
            productSource = productSources[_i];
            $productSourceSelect.append("<option value='" + productSource.id + "' data-url='" + productSource.url + "' data-image='" + productSource.logo_url_thumb + "'>" + productSource.name + "</option>");
          }
          loadCategories();
          $productSourceSelect.change(loadCategories);
          $productSourceSelect.select2({
            templateResult: function(option) {
              var $el;
              if (option.id) {
                $el = $(option.element);
                return $("<div class='product-source-option'>" + ("<img src='" + ($el.data('image')) + "'>") + ("<span class='product-source-name'>" + option.text + "</span><br>") + ("<span class='product-source-url'>" + ($el.data('url').replace(/\Ahttps?:\/\//, '')) + "</span>") + "</div>");
              } else {
                return option.text;
              }
            }
          });
          $('body').data('widget-layouts', widgetLayouts);
          $layoutTd = $form.find('tr#layout td');
          first = true;
          maxIconHeight = 55;
          widgetLayoutNames = [];
          for (_j = 0, _len1 = widgetLayouts.length; _j < _len1; _j++) {
            widgetLayout = widgetLayouts[_j];
            if (jQuery.inArray(widgetLayout['name'], widgetLayoutNames) > -1) {
              continue;
            } else {
              widgetLayoutNames.push(widgetLayout['name']);
            }
            checkedString = first ? 'checked="checked" ' : '';
            id = ['widget-layout', widgetLayout['name'].toLowerCase().replace(/\s+/g, '-')].join('-');
            _ref1 = $.map(['width', 'height'], function(attr) {
              return round(widgetLayout[attr] / 8, 2);
            }), iconWidth = _ref1[0], iconHeight = _ref1[1];
            if (iconHeight > maxIconHeight) {
              iconWidth = round(iconWidth * maxIconHeight / iconHeight, 2);
              iconHeight = maxIconHeight;
            }
            iconVerticalMargin = round((maxIconHeight - iconHeight) / 2, 2);
            $layoutTd.append(("<input type='radio' name='widget-layout' id='" + id + "' value='" + widgetLayout['name'] + "' " + checkedString + "/>") + ("<label for='" + id + "'>") + ("<div class='icon' style='width: " + iconWidth + "px; height: " + iconHeight + "px; margin-top: " + iconVerticalMargin + "px; margin-bottom: " + iconVerticalMargin + "px'></div>") + ("<div class='name'>" + widgetLayout['name'] + "</div>") + ("<div class='size'>" + widgetLayout['width'] + "px &times; " + widgetLayout['height'] + "px</div>") + "</label>");
            if (first) {
              first = false;
            }
          }
        }
        return $form.show();
      });
    };
    loadCategories = function() {
      var $categoryLoader, $categorySelects, $categoryWrapper, productSourceId;
      $categoryWrapper = $('tr#categories .wrapper');
      $categorySelects = $categoryWrapper.find('select');
      $categoryLoader = $categoryWrapper.find('.ajax-loader');
      productSourceId = $('tr#product-source select').val();
      $categorySelects.empty().hide();
      $categoryLoader.show();
      return $.get("" + ajaxurl + "?action=get_categories&product_source_id=" + productSourceId, function(response) {
        var category, _i, _len;
        if (typeof response === 'string') {
          Rollbar.error(response);
          return $categoryWrapper.html(response);
        } else {
          if (response[0] !== '') {
            response.unshift('');
          }
          for (_i = 0, _len = response.length; _i < _len; _i++) {
            category = response[_i];
            $categorySelects.append("<option value='" + category + "'>" + category + "</option>");
          }
          $categoryLoader.hide();
          return $categorySelects.show();
        }
      });
    };
    watchCategoriesAndKeywords = function() {
      $(document).on('click', '#categories select', function(e) {
        return $('input#categories-manual').prop('checked', true);
      });
      return $(document).on('click', '#keywords input[type="text"]', function(e) {
        return $('input#keywords-manual').prop('checked', true);
      });
    };
    return watchWidgetForm = function() {
      return $(document).on('submit', 'form', function(e) {
        var $categorieFields, $keywordFields, categories, categoriesType, data, effect, keywords, keywordsType, productSourceId, productSourceName, widgetLayout, widgetLayoutName, widgetLayouts;
        e.preventDefault();
        $('tr#shortcode, tr#preview').show();
        $('#error').hide();
        $('tr#preview').find('#widget, #note').empty();
        $('tr#preview .ajax-loader').show();
        $('tr#shortcode textarea').val('');
        widgetLayoutName = $('input[name="widget-layout"]:checked').val();
        effect = $('input[name="effect"]:checked').val();
        widgetLayouts = $('body').data('widget-layouts');
        widgetLayout = $.grep(widgetLayouts, function(widgetLayout) {
          return widgetLayout['name'] === widgetLayoutName && widgetLayout['configuration']['show_slider'] === (effect === 'slider');
        })[0];
        productSourceId = $('tr#product-source select').val();
        productSourceName = $('tr#product-source select option:selected').text();
        categoriesType = $('input[name="categories"]:checked').val();
        if (categoriesType === 'none') {
          categories = [];
        } else {
          $categorieFields = $('#categories select:visible');
          categories = $categorieFields.map(function(i, el) {
            var category;
            category = $(el).val().trim();
            if (category.length === 0) {
              return null;
            } else {
              return category;
            }
          }).get();
        }
        keywordsType = $('input[name="keywords"]:checked').val();
        if (keywordsType === 'none') {
          keywords = [];
        } else {
          $keywordFields = $('#keywords input[type="text"]');
          keywords = $keywordFields.map(function(i, el) {
            var keyword;
            keyword = $(el).val().toLowerCase().trim();
            if (keyword.length === 0) {
              return null;
            } else {
              return keyword;
            }
          }).get();
        }
        data = {
          widget: {
            layout_id: widgetLayout.id,
            search_combos: [
              {
                product_source_id: productSourceId,
                categories: categories,
                keywords: keywords
              }
            ]
          }
        };
        return $.post("" + ajaxurl + "?action=create_widget", data, function(response) {
          var shortcode;
          $('tr#preview .ajax-loader').hide();
          if (response.success) {
            shortcode = "[productwidget id=\"" + response.data.identifier + "\"]";
            $.get("" + ajaxurl + "?action=parse_widget_shortcode&shortcode=" + shortcode, function(response) {
              var noteText;
              $('tr#preview td #widget').html(response);
              noteText = 'This widget contains popular products ';
              if (keywords.length === 1) {
                noteText += "for the keyword \"" + keywords[0] + "\"";
              } else if (keywords.length > 1) {
                noteText += "for the keywords " + (($.map(keywords.slice(0, -1), function(keyword) {
                  return "\"" + keyword + "\"";
                })).join(', ')) + (keywords.length > 2 ? ',' : '') + " and \"" + keywords[keywords.length - 1] + "\"";
              }
              noteText += ' from ';
              noteText += categories.length === 0 ? 'all categories' : categories.length === 1 ? "the category \"" + categories[0] + "\"" : "the categories " + (($.map(categories.slice(0, -1), function(category) {
                return "\"" + category + "\"";
              })).join(', ')) + (categories.length > 2 ? ',' : '') + " and \"" + categories[categories.length - 1] + "\"";
              noteText += " of " + productSourceName + ".";
              $('#note').text(noteText);
              return reenableFormButtons();
            });
            $('tr#shortcode textarea').val(shortcode);
            return $('html, body').animate({
              scrollTop: $('tr#preview').offset().top
            }, 1000);
          } else {
            $('tr#shortcode, tr#preview').hide();
            $('#error').html(response.data).show();
            return reenableFormButtons();
          }
        });
      });
    };
  })(jQuery);

}).call(this);
