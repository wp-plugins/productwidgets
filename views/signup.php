<?php /**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */
 ?>
<?php include('partials/_rollbar.php') ?>
<?php
  if (isset($_POST["signup"])) {
    $nonce = $_POST["_wpnonce"];
    $api_key = get_option("api_key");
    if (!wp_verify_nonce($nonce, "signup-options")) {
      die("Security check");
    } else if (!empty($api_key)) {
      $flash_error = "You are already signed up.";
    } else {
      try {
        $account = $this->api->create_account($_POST["signup"]);
        update_option("api_key", $account["api_key"]);
      } catch (Exception $e) {
        $flash_error = "Error creating account: ".$e->getMessage();
      }
    }
  }
  
  $api_key = get_option("api_key");
  $account_activated_at = get_option("account_activated_at");
  
  if (!empty($api_key) && empty($account_activated_at)) {
    $account = $this->api->get_account();
    update_option("account_activated_at", $account["activated_at"]);
  }
  
  $account_activated_at = get_option("account_activated_at");
?>
<div class='wrap'>
  <?php include('partials/_flash.php') ?>
  
        <?php if (!empty($api_key) && !empty($account_activated_at)) { ?>
          <h2>
    <?php echo esc_html(get_admin_page_title()) ?>
  </h2>
  <p>
    You are already signed up and your account has been activated.
  </p>
  <p>
    Check your
    <a href='<?php echo admin_url('admin.php?page='.$this->plugin_slug.'/widgets.php') ?>'>
      list of widgets
    </a>
    or
    <a href='<?php echo admin_url('admin.php?page='.$this->plugin_slug.'/add-widget.php') ?>'>
      add a new widget!
    </a>
  </p>
  
        <?php } else if (!empty($api_key)) { ?>
          <h2>
    Thanks for signing up!
  </h2>
  <p>
    We have received your signup and will activate your account shortly.
    <br>
    You will receive a confirmation email to the email address you provided.
  </p>
  <p>
    If you have any further questions, feel free to
    <a href='mailto:hello@productwidgets.com'>
      contact us.
    </a>
  </p>
  
        <?php } else { ?>
          <h2>
    Welcome to ProductWidgets!
  </h2>
  <div id='intro'>
    <p>
      <strong>
        ProductWidgets is a tool for publishers that lets them add intelligent widgets to their websites. The widgets show products which are related to the page content, and if a visitor clicks on a product and buys it, the publisher receives a commission. Simple as that.
      </strong>
    </p>
    <p>
      To start using ProductWidgets, you need to create
      <strong>
        a free account.
      </strong>
      <br>
      This is necessary to avoid publishers with objectionable content which advertisers don't want to promote their products next to.
    </p>
    <p>
      <strong>
        Creating an account is quick, free, and does not create any obligation for you to use ProductWidgets!
        <br>
        No information other than what is in the form below will be shared with us.
      </strong>
    </p>
    <p>
      If you have any more questions, check out
      <a href='https://www.productwidgets.com/' target='_blank'>
        the ProductWidgets website
      </a>
      or
      <a href='mailto:hello@productwidgets.com'>
        get in touch with us!
      </a>
    </p>
  </div>
  <form action='' method='post'>
    <table class='form-table'>
      <tr valign='top'>
        <th scope='row'>
          First name
        </th>
        <td>
          <input name='signup[first_name]' type='text' value='<?php echo isset($_POST["signup"]["first_name"]) ? $_POST["signup"]["first_name"] : "" ?>'>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Last name
        </th>
        <td>
          <input name='signup[last_name]' type='text' value='<?php echo isset($_POST["signup"]["last_name"]) ? $_POST["signup"]["last_name"] : "" ?>'>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Email
        </th>
        <td>
          <input name='signup[email]' type='text' value='<?php echo isset($_POST["signup"]["email"]) ? $_POST["signup"]["email"] : get_option("admin_email") ?>'>
          <p class='description'>
            Please make sure this email address is correct!
            <br>
            You will receive a confirmation email once your account is activated.
          </p>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Domain
        </th>
        <td>
          <input name='signup[domain]' type='text' value='<?php echo isset($_POST["signup"]["domain"]) ? $_POST["signup"]["domain"] : get_option("siteurl") ?>'>
          <p class='description'>
            The domain of the website you want to use ProductWidgets on.
            <br>
            Please make sure this is a publicly accessible domain we can look at.
          </p>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Primary country
        </th>
        <td>
          <select name='signup[country]'>
            
                  <?php foreach ($this->countries as $country) { ?>
                    <option value='<?php echo $country ?>'
                  <?php if (isset($_POST["signup"]["country"]) && $_POST["signup"]["country"] == $country) { ?>
                    <?php echo " selected" ?>
                  <?php } ?>
                ><?php echo $country ?></option>
            
                  <?php } ?>
          </select>
          <p class='description'>
            The country your website is primarily focussed on. Preferable your website should have the country-specific top-level domain and the content should be in the local language.
            <br>
            Unfortunately we cannot offer ProductWidgets for all countries yet. Read more about it on
            <a href='https://www.productwidgets.com/get-started/' target='_blank'>the ProductWidgets website</a>.
          </p>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Comments
        </th>
        <td>
          <textarea name='signup[comments]'></textarea>
          <p class='description'>
            Anything else you want to tell us about your website.
          </p>
        </td>
      </tr>
      <tr valign='top'>
        <th scope='row'>
          Accept
          <a href='https://www.productwidgets.com/terms-and-conditions/' target='_blank'>Terms & Conditions</a>
        </th>
        <td>
          <input name='signup[terms_and_conditions]' type='checkbox' value='true'>
        </td>
      </tr>
    </table>
    <?php
      wp_nonce_field("signup-options");
      submit_button("Create my free account", "primary large");
    ?>
  </form>
  
        <?php } ?>
</div>
