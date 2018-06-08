<div class="sips">
  <h2>
    <?php echo __('%provider% transactions', array('%provider%' => $provider), 'dzangocart'); ?>
  </h2>
  <div id="sf_admin_content" class="transactions">
    <?php echo $filterForm->render(); ?>
    <?php echo form_tag($module . '/batchAction', array('id' => 'sips-form')); ?>
      <table class="list sips">
        <thead>
          <tr>
            <th class="check<?php if (!isset($batch_actions) || !$batch_actions) { echo " hidden"; } ?>">
              <input type="checkbox" id="check-all" />
              <?php echo __('All', null, 'dzangocart'); ?>
            </th>
            <th class="date"><?php echo __('Date', null, 'dzangocart'); ?></th>
            <th class="order_id"><?php echo __('Order id', null, 'dzangocart'); ?></th>
            <th class="amount"><?php echo __('Amount', null, 'dzangocart'); ?></th>
            <th class="currency"><?php echo __('Currency', null, 'dzangocart'); ?></th>
            <th class="payment_means"><?php echo __('Payment means', null, 'dzangocart'); ?></th>
            <th class="card_number"><?php echo __('Card number', null, 'dzangocart'); ?></th>
            <th class="transaction_id"><?php echo __('Transaction id', null, 'dzangocart'); ?></th>
            <th class="response_code"><?php echo __('Response code', null, 'dzangocart'); ?></th>
            <th class="bank_response_code"><?php echo __('Bank response code', null, 'dzangocart'); ?></th>
            <th class="cvv_response_code"><?php echo __('CVV response code', null, 'dzangocart'); ?></th>
            <th class="test"><?php echo __('Test', null, 'dzangocart'); ?></th>
            <th class="actions"><?php echo __('Actions', null, 'dzangocart'); ?></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <input type="hidden" name="type" value="sips" />
      <?php if (isset($batch_actions) && $batch_actions) {
              include_partial('dzangocart/batch_actions', array('actions' => $batch_actions));
            } ?>
    </form>
  </div>
</div>

<script type="text/javascript">
  var dzangocart = {
    sips: {
      datatable: { sAjaxSource: '<?php echo url_for($module . '/' . $action); ?>' }
    },
    lang: '<?php echo $sf_user->getCulture(); ?>'
  };
</script>