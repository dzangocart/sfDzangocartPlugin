<?php if (!$purchase->cancelled) {
        echo link_to('<img src="' . public_path('/sfDzangocartPlugin/images/cross.png') . '" alt="cancel item" />',
                     'dzangocart/cancelItem?id=' . $purchase->id,
                     array('class' => 'item-cancel'));
      } ?>
<?php echo link_to('<img src="' . public_path('/sfDzangocartPlugin/images/cross.png') . '" alt="invoice" />', 
                   'dzangocart/invoice?order_id=' . $purchase->order_id, 
                   array()); ?>