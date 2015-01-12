<div class='thickbox-content' id='stats-<?php echo $item["identifier"] ?>'>
  <p>
    This widget layout has
    
          <?php if ($item["widgets_count"] == 0) { ?>
            not been implemented on any pages.
    
          <?php } else { ?>
            been implemented on
    <?php echo $item["widgets_count"]." page".($item["widgets_count"] != 1 ? "s" : null) ?>.
    
          <?php } ?>
  </p>
  <p>
    We are currently working on showing you detailed stats about how the widget layout performs on all pages it is implemented on, e.g., how often it was displayed and how many clicks it received.
  </p>
  <p>
    Be sure to
    <a href='http://eepurl.com/Z22BP' target='_blank'>
      sign up to our mailing list
    </a>
    to receive notifications about new features in this plugin!
  </p>
</div>
<a class='thickbox' href='#TB_inline?width=600&inlineId=stats-<?php echo $item["identifier"] ?>'>
  <?php echo $item["widgets_count"]." page".($item["widgets_count"] != 1 ? "s" : null) ?>
</a>
