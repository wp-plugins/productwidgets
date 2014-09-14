<?php
  /**
   * @package   ProductWidgets
   * @author    Kraut Computing <info@krautcomputing.com>
   * @license   GPL-2.0
   * @link      http://www.productwidgets.com/publishers/wordpress/
   * @copyright 2014 kraut computing UG (haftungsbeschränkt)
   */
  
  $api_key = get_option("api_key");
  
  if (isset($_POST["signup"])) {
    $nonce = $_POST["_wpnonce"];
    if (!wp_verify_nonce($nonce, "signup-options"))
      die("Security check");
    if (!empty($api_key)) {
      $flash = "You are already signed up.";
    } else {
      try {
        $results = $this->api->signup();
        update_option("api_key", $results["api_key"]);
        $flash = "You have successfully created your free account!<br>Now you can fill in your Amazon affiliate tracking IDs below or go ahead and <a href='".admin_url('admin.php?page='.$this->plugin_slug.'/widget-layouts.php')."'>add your first widget</a>!";
      } catch (Exception $e) {
        $flash = "Error creating account: ".$e->getMessage();
      }
    }
  } else if (isset($_POST["api_key"])) {
    $nonce = $_POST["_wpnonce"];
    if (!wp_verify_nonce($nonce, "api_key-options"))
      die("Security check");
    if (empty($_POST["api_key"])) {
      $flash = "API Key cannot be empty.";
    } else if ($_POST["api_key"] == get_option("api_key")) {
      $flash = "API Key was not updated.";
    } else {
      try {
        $this->api->get_account($_POST["api_key"]);
        update_option("api_key", $_POST["api_key"]);
        $flash = "Your API Key was updated successfully!";
      } catch (Exception $e) {
        $flash = "Error setting API Key: ".$e->getMessage();
      }
    }
  } else if (isset($_POST["tracking_ids"])) {
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
  
  $api_key = get_option("api_key");
?>
<div class='wrap'>
  <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
  </h2>
  <?php include("partials/_flash.php") ?>
  <?php $api_error = $this->api->test_connection() ?>
  
        <?php if (!empty($api_error)) { ?>
          <?php include_once("partials/_api-error.php") ?>
  
        <?php } else if (empty($api_key)) { ?>
          <h3>
    Create your free ProductWidgets account
  </h3>
  <p>
    To be able to use this plugin, you need a free
    <a href='http://www.productwidgets.com/' target='_blank'>
      ProductWidgets
    </a>
    account.
    <br>
    ProductWidgets will create and manage your widgets, optimize them, and track stats for you.
  </p>
  <p>
    <strong>
      Creating an account is completly free and there are no further obligations for you.
      <br>
      Neither your email address nor any other personal information are shared!
    </strong>
  </p>
  <p>
    To learn more, please read our
    <a href='http://www.productwidgets.com/terms-and-conditions/' target='_blank'>Terms and Conditions</a>
    and
    <a href='http://www.productwidgets.com/privacy-policy/' target='_blank'>Privacy Policy</a>.
  </p>
  <form action='' method='post'>
    <input name='signup' type='hidden'>
    <?php
      wp_nonce_field("signup-options");
      submit_button("Create my free account", "primary large");
    ?>
  </form>
  <a href='#' id='existing-api-key-button'>
    I already have an API Key
  </a>
  <form action='' id='existing-api-key-form' method='post'>
    <p>
      Please enter your existing API Key and press save:
    </p>
    <?php
      settings_fields("api_key");
      do_settings_sections($this->plugin_slug);
      submit_button("Save");
    ?>
  </form>
  
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
  <h3>
    API Key
  </h3>
  <p>
    Your API Key is used to authenticate your account when communicating with the ProductWidgets service.
    <br>
    Once it is set, there is normally no reason to update it.
  </p>
  <form action='' method='post'>
    <?php
      settings_fields("api_key");
      do_settings_sections($this->plugin_slug);
      submit_button("Save");
    ?>
  </form>
  
        <?php } ?>
</div>
