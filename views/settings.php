<?php /**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */
 ?>
<?php
  if (isset($_POST["tracking_ids"])) {
    $nonce = $_POST["_wpnonce"];
    if (!wp_verify_nonce($nonce, "tracking_ids-options"))
      die("Security check");
    try {
      $this->api->update_amazon_tracking_ids($_POST["tracking_ids"]);
      $flash_info = "Your Amazon affiliate tracking IDs were updated successfully!";
    } catch (Exception $e) {
      $flash_error = "Error updating Amazon affiliate tracking IDs: ".$e->getMessage();
    }
  }
?>
<div class='wrap'>
  <?php include('partials/_flash.php') ?>
  <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
  </h2>
  <?php include("partials/_ajax-loader.php") ?>
  <div id='no-settings'>
    Nothing here yet...
  </div>
  <div id='amazon'>
    <h3>
      Amazon Affiliate Tracking IDs
    </h3>
    <p>
      Enter your Amazon affiliate tracking IDs to make sure that you receive commissions for sales that are generated through your widgets.
      <br>
      If you haven't yet signed up to any of the programs, you can find the signup links below.
    </p>
    <form action='' id='tracking-ids' method='post'>
      <table class='form-table'>
        <tr valign='top'>
          <th scope='row'></th>
          <td>
            <input type='text'>
          </td>
        </tr>
      </table>
      <?php
        wp_nonce_field("tracking_ids-options");
        submit_button("Save");
      ?>
    </form>
  </div>
</div>
