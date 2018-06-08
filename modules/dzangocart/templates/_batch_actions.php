<div class="batch-actions">
  <select name="action_name">
    <option value=""><?php echo __('Choose an action', null, 'dzangocart') ?></option>
    <?php foreach ($actions as $name => $action) { ?>
    <option value="<?php echo $name; ?>"><?php echo __(ucfirst($name), null, 'dzangocart') ?></option>
    <?php } ?>
  </select>
  <input type="submit" value="<?php echo __('go', null, 'dzangocart') ?>" />
</div>
