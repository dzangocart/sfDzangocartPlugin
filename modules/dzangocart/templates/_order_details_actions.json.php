<?php if (!$item['cancelled']) {
        echo link_to('<img src="' . public_path('/sfDzangocartPlugin/images/cross.png') . '" alt="cancel item" />',
                     'dzangocart/cancelItem?id=' . $item['id'],
                     array('class' => 'item-cancel'));
      } ?>