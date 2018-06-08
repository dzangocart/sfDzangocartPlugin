<div class="filter">
  <form>
    <fieldset>
      <label>
        <?php echo __('From', null, 'dzangocart'); ?>
        <input type="text" value="<?php echo $from->format('d/m/Y'); ?>" id="date_from" name="date_from">
      </label>
      <label>
        <?php echo __('To', null, 'dzangocart'); ?>
        <input type="text" value="<?php echo $to->format('d/m/Y'); ?>" id="date_to" name="date_to">
      </label>
      <p class="label test">
        <input type="checkbox" value="1" id="test" name="test">
        <label class="inlineLabel">
          <?php echo __('Include test transactions', null, 'dzangocart'); ?>
        </label>
      </p>
    </fieldset>
  </form>
</div>