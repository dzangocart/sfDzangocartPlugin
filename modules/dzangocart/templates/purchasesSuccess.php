<div id="purchases" class="purchases">
  <h2>
    <?php echo __('Purchases', null, 'dzangocart'); ?>
  </h2>
  <?php include_partial('dzangocart/refund_form', array('form' => $refund_form)); ?>
  <div id="sf_admin_content">
    <?php echo $filterForm->render(); ?>
    <?php echo form_tag($module . '/batchAction', array('id' => 'purchases-form')); ?>
      <div class="sf_admin_list">
        <table id="purchase_list" class="list purchases">
          <thead>
            <tr>
              <th class="check<?php if (!isset($batch_actions) || !$batch_actions) { echo " hidden"; } ?>">
                <input type="checkbox" id="check-all" />
                <?php echo __('All', null, 'dzangocart'); ?>
              </th>
              <th class="date"><?php echo __('Date', null, 'purchase'); ?></th>
              <th class="order_id"><?php echo __('Order id', null, 'dzangocart'); ?></th>
              <th class="customer"><?php echo __('Customer', null, 'dzangocart'); ?></th>
              <th class="item"><?php echo __('Product/Service', null, 'dzangocart'); ?></th>
              <th class="amount net"><?php echo __('Net', null, 'dzangocart'); ?></th>
              <th class="amount tax"><?php echo __('VAT', null, 'dzangocart'); ?></th>
              <th class="amount gross"><?php echo __('Gross', null, 'dzangocart'); ?></th>
              <th class="paid"><?php echo __('Paid', null, 'dzangocart'); ?></th>
              <th class="demo"><?php echo __('Test', null, 'dzangocart'); ?></th>
              <th class="actions">&nbsp;</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <input type="hidden" name="type" value="purchases" />
        <?php if (isset($batch_actions) && $batch_actions) {
                include_partial('dzangocart/batch_actions', array('actions' => $batch_actions));
              } ?>
      </div>      
    </form>
  </div>
  <div id="sf_admin_footer">
  </div>
</div>
<script type="text/javascript">
  var dzangocart = {
    purchases: {
      datatable: { sAjaxSource: '<?php echo url_for($module . '/' . $action); ?>' },
      itemCancelMessage: '<?php echo __('Do you want to cancel this purchase?', null, 'dzangocart') ?>'
    },
    lang: '<?php echo $sf_user->getCulture(); ?>'
  };
</script>