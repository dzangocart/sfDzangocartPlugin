<img src="<?php echo public_path('/sfDzangocartPlugin/images/zoom_in.png'); ?>" alt="details" class="order-details details-show" />
<?php if (!$order['cancelled']) {
        echo link_to('<img src="' . public_path('/sfDzangocartPlugin/images/cross.png') . '" alt="cancel order" />',
                     'dzangocart/cancelOrder?id=' . $order['id'],
                     array('class' => 'order-cancel'));
      } ?>

