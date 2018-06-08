<div id="orders" class="orders">
  <h2>
    <?php echo __('Orders', null, 'dzangocart'); ?>
  </h2>
  <div id="sf_admin_content">
    <div class="filter">
      <?php echo $filterForm->render(); ?>
    </div>
    <?php echo form_tag($module . '/batchAction', array('id' => 'orders-form')); ?>
      <div class="sf_admin_list">
        <table id="orders_list" class="list dzangocart orders">
          <thead>
            <tr>
              <th class="check<?php if (!isset($batch_actions) || !$batch_actions) { echo " hidden"; } ?>">
                <input type="checkbox" id="check-all" />
              </th>
              <th class="date"><?php echo __('Date', null, 'dzangocart'); ?></th>
              <th class="order_no"><?php echo __('No', null, 'dzangocart'); ?></th>
              <th class="customer"><?php echo __('Customer', null, 'dzangocart'); ?></th>
              <th class="currency"><?php echo __('Currency', null, 'dzangocart'); ?></th>
              <th class="net"><?php echo __('Net', null, 'dzangocart'); ?></th>
              <th class="tax"><?php echo __('Tax', null, 'dzangocart'); ?></th>
              <th class="gross"><?php echo __('Gross', null, 'dzangocart'); ?></th>
              <th class="amount_paid"><?php echo __('Paid', null, 'dzangocart'); ?></th>
              <th class="affiliate"><?php echo __('Affiliate', null, 'dzangocart'); ?></th>
              <th class="demo"><?php echo __('Test', null, 'dzangocart'); ?></th>
              <th class="actions"><?php echo __('Actions', null, 'dzangocart'); ?></th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <input type="hidden" name="type" value="orders" />
        <?php if (isset($batch_actions) && count($batch_actions)) {
                include_partial('dzangocart/batch_actions', array('actions' => $batch_actions));
              } ?>
      </div>
    </form>
  </div>
</div>

<script type="text/javascript">
  var dzangocart = {
    date_format: "<?php echo sfConfig::get('app_dzangocart_datepicker_date_format'); ?>",
    orders: {
      datatable: { sAjaxSource: '<?php echo url_for($module . '/' . $action); ?>' },
      orderCancelMessage: '<?php echo __('Do you want to cancel this order?', null, 'dzangocart') ?>',
      itemCancelMessage: '<?php echo __('Do you want to cancel this purchase?', null, 'dzangocart') ?>'
    },
    lang: '<?php echo $sf_user->getCulture(); ?>'
  };
</script>