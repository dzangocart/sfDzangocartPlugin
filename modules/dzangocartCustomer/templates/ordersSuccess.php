<?php use_helper('Number'); ?>
<section class="block main account edit">
  <section class="top clearfix">
    <h2><?php echo __('Account', null, 'user'); ?></h2>
    <span class="__loading">
      <?php echo image_tag('loading.gif'); ?>
    </span>
  </section>
  <section class="content yui-g">
    <div class="yui-u first">
      <table>
        <thead>
          <tr>
            <th class="date"><?php echo __('Date', null, 'order'); ?></th>
            <th class="order_no"><?php echo __('Order no.', null, 'order'); ?></th>
            <th class="currency"><?php echo __('Ccy', null, 'order'); ?></th>
            <th class="amount"><?php echo __('Amount', null, 'order'); ?></th>
            <th class="amount"><?php echo __('Amount paid', null, 'order'); ?></th>
            <th class="actions">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order) { 
            $date = new DateTime($order['date']); ?>
            <tr>
              <td><?php echo $date->format($date_format . ' H:i'); ?></td>
              <td><?php echo $order['id']; ?></td>
              <td><?php echo $order['currency']; ?></td>
              <td><?php echo $order['amount']; ?></td>
              <td><?php echo $order['amount_paid']; ?></td>
              <td></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
    <div class="yui-u">
      <section = "expires">
        <?php // echo __('Valid until %date%', array('%date%' => $account->getExpiresAt($date_format)), 'account'); ?>
      </section>
    </div>
  </section>
  <pre><?php echo $customer_code; ?></pre>
  <pre><?php echo print_r($orders->getRawValue(), true); ?></pre>
</section>