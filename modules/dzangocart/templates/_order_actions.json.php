<?php 
  echo implode(array('<a href="#" class="details">',
                     image_tag('/sfDzangocartPlugin/images/zoom_in.png',
                               array('alt_title' => __('Show details', null, 'dzangocart'),
                                     'class' => 'show')),
                     image_tag('/sfDzangocartPlugin/images/zoom_out.png',
                               array('alt_title' => __('Hide details', null, 'dzangocart'),
                                     'class' => 'hide')),
                     '</a>'));
  echo link_to(image_tag('/sfDzangocartPlugin/images/invoice-16x16.png',
                         array('alt_title' => __('invoice', null, 'dzangocart'))),
               'dzangocart/invoice?order_id=' . $order->id, 
               array('class' => 'invoice')); 
  
  if (!$order->cancelled && !$order->isCredit()) {
    echo link_to(image_tag('/sfDzangocartPlugin/images/cross.png',
                           array('alt_title' => __('Cancel order', null, 'dzangocart'))),
                 'dzangocart/cancel?order_id=' . $order->id,
                 array('class' => 'cancel')); 
  } 
?>