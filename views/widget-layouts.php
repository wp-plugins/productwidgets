<?php
  /**
   * @package   ProductWidgets
   * @author    Kraut Computing <info@krautcomputing.com>
   * @license   GPL-2.0
   * @link      http://www.productwidgets.com/publishers/wordpress/
   * @copyright 2014 kraut computing UG (haftungsbeschränkt)
   */
  
  $api_key = get_option("api_key");
?>
<div class='wrap'>
  <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
    <div class='thickbox-content' id='help'>
      <p>
        This screen shows a list of all your available widget layouts.
      </p>
      <p>
        To add a new widget to a post or page, pick a widget layout, copy the shortcode, and paste it where you want the widget to appear.
      </p>
      <p>
        Initially the widget will contain popular Amazon products from the categories books, electronics, and DVDs & Blu-Rays, but soon after you implement a new widget, the ProductWidgets algorithm will analyze the page that contains the widget and pick Amazon products that match your content better.
      </p>
      <p>
        Unfortunately it is not yet possible to create new widget layouts from this screen, but
        <a href='mailto:hello@productwidgets.com'>
          get in touch
        </a>
        if you need to do so and we will help you out.
      </p>
      <p>
        Also, be sure to
        <a href='http://eepurl.com/Z22BP' target='_blank'>
          sign up to our mailing list
        </a>
        to receive notifications about new features in this plugin!
      </p>
    </div>
    <a class='thickbox add-new-h2' href='#TB_inline?width=600&inlineId=help'>
      Help
    </a>
  </h2>
  <?php $api_error = $this->api->test_connection() ?>
  
        <?php if (!empty($api_error)) { ?>
          <?php include_once("partials/_api-error.php") ?>
  
        <?php } else if (empty($api_key)) { ?>
          <?php include_once("partials/_signup.php") ?>
  
        <?php } else { ?>
          <p>
    This screen shows a list of all your available widget layouts. Click the "Help" button above for more information.
  </p>
  <div id='widget-layouts'>
    <?php include("partials/_ajax-loader.php") ?>
  </div>
  
        <?php } ?>
  <?php echo add_thickbox(); ?>
</div>
