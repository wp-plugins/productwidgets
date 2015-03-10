<table>
  <tbody>
    <tr>
      <th>
        Total
      </th>
      <td class='counts'>
        <?php echo number_format($item["stats"][$stats_type]["total"]) ?>
      </td>
    </tr>
    
          <?php foreach (array_slice($item["stats"][$stats_type]["referrers"], 0, 5) as $key => $val) { ?>
            <tr>
      <th>
        <a href='<?php echo $key ?>' target='_blank'>
          <?php echo preg_replace('/([^-]{60})(?=.)/', '\1 ', $key) ?>
        </a>
      </th>
      <td class='counts'>
        <?php echo number_format($val) ?>
      </td>
    </tr>
    
          <?php } ?>
  </tbody>
</table>
<?php $referrer_count = count($item["stats"][$stats_type]["referrers"]) ?>

      <?php if ($referrer_count > 5) { ?>
        and <?php echo $referrer_count - 5 ?> more URLs

      <?php } ?>
