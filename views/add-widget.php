<?php /**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */
 ?>
<?php include('partials/_rollbar.php') ?>
<link href='//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.1/css/select2.min.css' rel='stylesheet'>
<script src='//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0-rc.1/js/select2.min.js'></script>
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
          <div>
            <input checked id='effect-none' name='effect' type='radio' value=''>
            <label for='effect-none'>
              None
            </label>
          </div>
          <div>
            <input id='effect_slider' name='effect' type='radio' value='slider'>
            <label for='effect_slider'>
              Slider
            </label>
          </div>
        </td>
      </tr>
      <tr id='product-source' valign='top'>
        <th scope='row'>
          Product source
        </th>
        <td>
          <select name='product-source'></select>
        </td>
      </tr>
      <tr id='categories' valign='top'>
        <th scope='row'>
          Categories
        </th>
        <td>
          <div>
            <input checked id='categories-none' name='categories' type='radio' value='none'>
            <label for='categories-none'>
              Don't filter products by categories.
            </label>
          </div>
          <div>
            <input id='categories-manual' name='categories' type='radio' value='manual'>
            <label for='categories-manual'>
              Find products from these categories:
            </label>
          </div>
          <div class='wrapper'>
            <?php include('partials/_ajax-loader.php') ?>
            <select name='category-1'></select>
            <select name='category-2'></select>
            <select name='category-3'></select>
          </div>
        </td>
      </tr>
      <tr id='keywords' valign='top'>
        <th scope='row'>
          Keywords
        </th>
        <td>
          <div>
            <input checked id='keywords-none' name='keywords' type='radio' value='none'>
            <label for='keywords-none'>
              Don't filter products by keywords.
            </label>
          </div>
          <div>
            <input id='keywords-manual' name='keywords' type='radio' value='manual'>
            <label for='keywords-manual'>
              Find products related to these keywords:
            </label>
          </div>
          <div>
            <input id='keyword-1' name='keyword-1' placeholder='Keyword 1' type='text'>
            <input id='keyword-2' name='keyword-2' placeholder='Keyword 2' type='text'>
            <input id='keyword-3' name='keyword-3' placeholder='Keyword 3' type='text'>
          </div>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'></th>
        <td>
          <?php echo submit_button("Preview and get code") ?>
          <div class='exception' id='error'></div>
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
          <?php include('partials/_ajax-loader.php') ?>
          <div id='widget'></div>
          <div id='note'></div>
        </td>
      </tr>
    </table>
  </form>
</div>
