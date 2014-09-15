<?php
  /**
   * @package   ProductWidgets
   * @author    Kraut Computing <info@krautcomputing.com>
   * @license   GPL-2.0
   * @link      http://www.productwidgets.com/publishers/wordpress/
   * @copyright 2014 kraut computing UG (haftungsbeschränkt)
   */
  
  if (isset($_POST["tracking_ids"])) {
    $nonce = $_POST["_wpnonce"];
    if (!wp_verify_nonce($nonce, "tracking_ids-options"))
      die("Security check");
    try {
      $this->api->update_tracking_ids($_POST["tracking_ids"]);
      $flash = "Your Amazon affiliate tracking IDs were updated successfully!";
    } catch (Exception $e) {
      $flash = "Error updating Amazon affiliate tracking IDs: ".$e->getMessage();
    }
  }
?>
<div class='wrap'>
  <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
  </h2>
  <?php include("partials/_flash.php") ?>
  <?php $api_error = $this->api->test_connection() ?>
  
        <?php if (!empty($api_error)) { ?>
          <?php include_once("partials/_api-error.php") ?>
  
        <?php } else { ?>
          <h3>
    Amazon Affiliate Tracking IDs
  </h3>
  <p>
    Enter your Amazon affiliate tracking IDs to make sure that you receive commissions for sales that are generated through your widgets.
    <br>
    If you haven't yet signed up to any of the programs, you can find the signup links below.
  </p>
  <form action='' id='tracking-ids' method='post'>
    <?php include("partials/_ajax-loader.php") ?>
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
  
        <?php } ?>
</div>
