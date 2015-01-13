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
    <a class='add-new-h2' href='<?php echo admin_url('admin.php?page='.$this->plugin_slug.'/add-widget.php') ?>'>
      Add New
    </a>
  </h2>
  <div id='content'>
    <?php include('partials/_ajax-loader.php') ?>
  </div>
</div>
<?php echo add_thickbox(); ?>
