<?php /**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */
 ?>
<div class='wrap'>
  <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
  </h2>
  <?php include('partials/_flash.php') ?>
  <?php include('partials/_ajax-loader.php') ?>
  <form action='' method='post'>
    <table class='form-table'>
      <tr id='layout' valign='top'>
        <th scope='row'>
          Layout
        </th>
        <td></td>
      </tr>
      <tr id='effect' valign='top'>
        <th scope='row'>
          Effect
        </th>
        <td>
          <p>
            <input checked='checked' id='effect_none' name='effect' type='radio' value=''>
            <label for='effect_none'>
              None
            </label>
          </p>
          <p>
            <input id='effect_slider' name='effect' type='radio' value='slider'>
            <label for='effect_slider'>
              Slider
            </label>
          </p>
        </td>
      </tr>
      <tr id='products' valign='top'>
        <th scope='row'>
          Products
        </th>
        <td>
          <p>
            <input disabled='disabled' id='products_automated_content' name='products' type='radio' value='automated_content'>
            <label disabled='disabled' for='products_automated_content'>
              Find products related to the page content (coming soon)
            </label>
          </p>
          <p>
            <input checked='checked' id='products_automated_title' name='products' type='radio' value='automated_title'>
            <label for='products_automated_title'>
              Find products related to the page title
            </label>
          </p>
          <p>
            <input id='keywords_manual' name='products' type='radio' value='manual'>
            <label for='keywords_manual'>
              Find products related to these keywords:
            </label>
          </p>
          <p id='keywords'>
            <input id='keyword_1' name='keywords' placeholder='Keyword 1' type='text'>
            <input id='keyword_2' name='keywords' placeholder='Keyword 2' type='text'>
            <input id='keyword_3' name='keywords' placeholder='Keyword 3' type='text'>
          </p>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'></th>
        <td>
          <?php echo submit_button("Preview and get code") ?>
        </td>
      </tr>
      <tr id='shortcode' valign='top'>
        <th scope='row'>
          Widget shortcode
        </th>
        <td>
          <textarea class='autoselect' readonly></textarea>
        </td>
      </tr>
      <tr id='preview' valign='top'>
        <th scope='row'>
          Preview
        </th>
        <td>
          <div id='widget'></div>
          <p id='automated-title-note'>
            Note: These are only sample products. Once you implement this widget on a page, it will start to show products related to the title of that page.
          </p>
        </td>
      </tr>
    </table>
  </form>
</div>
