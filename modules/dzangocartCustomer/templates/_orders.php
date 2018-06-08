<?php use_helper('Number'); ?>
<section class="dzangocart orders">
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
      <?php foreach ($orders->getRawValue() as $order) { 
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
</section>
<?php if (sfConfig::get('sf_debug')) { ?>
<pre>
<?php echo print_r($orders->getRawValue(), true); ?>
</pre>
<?php } ?>
