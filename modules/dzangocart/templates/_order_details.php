<?php use_helper('Number'); ?>
<table width="100%">
  <thead>
    <tr>
      <th><?php echo __('Id', null, 'dzangocart'); ?></th>
      <th><?php echo __('Name', null, 'dzangocart'); ?></th>
      <th><?php echo __('Code', null, 'dzangocart'); ?></th>
      <th><?php echo __('Price', null, 'dzangocart'); ?></th>
      <th><?php echo __('Quantity', null, 'dzangocart'); ?></th>
      <th><?php echo __('Net', null, 'dzangocart'); ?></th>
      <th><?php echo __('Tax', null, 'dzangocart'); ?></th>
      <th><?php echo __('Gross', null, 'dzangocart'); ?></th>
      <th><?php echo __('Actions', null, 'dzangocart'); ?></th>
    </tr>
  </thead>
  <?php if ($items) { ?>
  <tbody>
    <?php foreach ($items as $i) { ?>
    <tr>      
      <td><?php echo $i['id']; ?></td>
      <td><?php echo $i['name']; ?></td>
      <td><?php echo $i['code']; ?></td>
      <td><?php echo format_currency($i['price_excl']); ?></td>
      <td><?php echo $i['quantity']; ?></td>
      <td><?php echo format_currency($i['amount_excl']); ?></td>
      <td><?php echo format_currency($i['tax']); ?></td>
      <td><?php echo format_currency($i['amount_incl']); ?></td>
      <td>
        <?php if (!$i['cancelled']) {
                echo link_to('<img src="' . public_path('/sfDzangocartPlugin/images/cross.png') . '" alt="cancel item" />',
                             'dzangocart/cancelItem?id=' . $i['id'],
                             array('class' => 'item-cancel'));
              } ?>
      </td>
    </tr>
    <?php } ?>
  </tbody>
  <?php } ?>
</table>
