
      <?php if (isset($flash_error)) { ?>
        <div class='error'>
  <p>
    <strong>
      <?php echo $flash_error ?>
    </strong>
  </p>
</div>

      <?php } ?>

      <?php if (isset($flash_info)) { ?>
        <div class='updated'>
  <p>
    <strong>
      <?php echo $flash_info ?>
    </strong>
  </p>
</div>

      <?php } ?>
