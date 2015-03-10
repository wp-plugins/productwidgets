<?php /**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */
 ?>
<?php include('partials/_rollbar.php') ?>
<div class='wrap'>
  <h2>
    Houston, we have a problem.
  </h2>
</div>
<h3>
  Hi and welcome to ProductWidgets!
</h3>
<p>
  Unfortunately, there is a tiny problem we need to solve before we can get going.
</p>
<p>
  This plugin needs to be able to communicate with the ProductWidgets API (https://api.productwidgets.com).
  <br>
  Currently this is not possible because your hosting provider seems to block outgoing communication to this URL.
</p>
<p>
  <strong>
    Please contact your hosting provider and ask him kindly to unblock outgoing communication to https://api.productwidgets.com
    <br>
    The error that occurred was
    "<?php echo $api_error ?>"
  </strong>
</p>
<p>
  Please return to this screen once this problem is fixed.
  <br>
  Apologies for the inconvenience!
</p>
