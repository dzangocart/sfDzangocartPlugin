<div>
  <div class="filter">
    <form>
      <fieldset>
        <label>
          <?php echo __('From', null, 'dzangocart'); ?>
          <input type="text" value="<?php echo $from->getRawValue()->format('d/m/Y'); ?>" id="date_from" name="date_from">
        </label>
        <label>
          <?php echo __('To', null, 'dzangocart'); ?>
          <input type="text" value="<?php echo $to->getRawValue()->format('d/m/Y'); ?>" id="date_to" name="date_to">
        </label>
        <p class="label test">
          <input type="checkbox" value="1" id="test" name="test">
          <label class="inlineLabel">
            <?php echo __('Include test orders', null, 'dzangocart'); ?>
          </label>
        </p>
      </fieldset>
    </form>
  </div>
  <div>
    <table class="list purchases">
      <thead>
        <tr>
          <th class="date"><?php echo __('Date', null, 'dzangocart'); ?></th>
          <th class="order_no"><?php echo __('No', null, 'dzangocart'); ?></th>
          <th class="customer"><?php echo __('Customer', null, 'dzangocart'); ?></th>
          <th class="currency"><?php echo __('Currency', null, 'dzangocart'); ?></th>
          <th class="net"><?php echo __('Net', null, 'dzangocart'); ?></th>
          <th class="tax"><?php echo __('Tax', null, 'dzangocart'); ?></th>
          <th class="gross"><?php echo __('Gross', null, 'dzangocart'); ?></th>
          <th class="due"><?php echo __('Due', null, 'dzangocart'); ?></th>
          <th class="affiliate"><?php echo __('Affiliate', null, 'dzangocart'); ?></th>
          <th class="test"><?php echo __('Test', null, 'dzangocart'); ?></th>
          <th class="actions"><?php echo __('Actions', null, 'dzangocart'); ?></th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
</script>