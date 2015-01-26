<p class='exception'>
  <strong>
    
          <?php if (!empty($error_message)) { ?>
            Error:
    <?php echo $error_message ?>
    
          <?php } else { ?>
            An error occurred.
    
          <?php } ?>
  </strong>
  <?php $exception_message = $e->getMessage() ?>
  
        <?php if (!empty($exception_message)) { ?>
          <br>
  Message:
  <?php echo $e->getMessage() ?>
  
        <?php } ?>
  <br>
  <?php echo get_class($e) ?>
  occurred in
  <?php echo $e->getFile()."#".$e->getLine() ?>
</p>
