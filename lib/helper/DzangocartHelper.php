<?php

function dzangocart_link_to($product, $price, $quantity = 1,
                            $category = 'default', $options = array(),
                            $checkout = false, $label = null, $html_options = array()) {

  sfContext::getInstance()->getResponse()->addJavascript(sfConfig::get('app_dzangocart_url').'/cart/js', 'first');
  sfContext::getInstance()->getResponse()->addStylesheet(sfConfig::get('app_dzangocart_css'), 'last');

  if (!$label) {
    $label = sfConfig::get('app_dzangocart_cart_label');
    $img = sfConfig::get('app_dzangocart_cart_image');
    if ($img) {
      sfContext::getInstance()->getConfiguration()->loadHelpers('Asset');
      $label = image_tag($img, array('alt' => $label, 'title' => $label));
    }
  }

  $html_options = array_merge(array('title' => __('Shopping cart')), $html_options);
  $html_options['class'] = array_key_exists('class', $html_options) ?
                             'dzangocart ' . $html_options['class'] :
                             'dzangocart';

  return link_to($label,
                 dzangocart_url_for($product, $price, $quantity,
                                    $category, $options, $checkout),
                 $html_options);
}

function dzangocart_url_for($product, $price, $quantity = 1,
                            $category = 'default', array $options = array(),
                            $checkout = false) {
  $url = sfConfig::get('app_dzangocart_url');
  $url .= '/cart/show?';

  $params = array('name' => is_object($product) ? $product->__toString() : $product,
                  'price' => $price);
  if ($quantity) { $params['quantity'] = $quantity; }
  if ($category) { $params['category'] = $category; }
  if (sfConfig::get('app_dzangocart_send_userid')) {
    $user = sfContext::getInstance()->getUser();
    if ($user->isAuthenticated()) {
      $params['user_id'] =
        call_user_func(array($user, sfConfig::get('app_dzangocart_userid_method',
                                                  'getUsername')));
    }
  }
  if ($checkout) { $params['checkout'] = true; }

  if (!array_key_exists('test', $options) && sfConfig::has('app_dzangocart_testcode')) {
    $options['test'] = sfConfig::get('app_dzangocart_testcode');
  }
  $params = array_merge($params, $options);
  $url .= http_build_query($params);
  return $url;
}

function dzangocart_set_cookie($array, $key, $lifetime) {
  sfContext::getInstance()->getResponse()->setCookie('dzangocart', 
                                                     Dzangocart::encode($array, $key, $lifetime), 
                                                     date('c', time() + $lifetime), 
                                                     '/');
}

function dzangocart_remove_cookie() {
  sfContext::getInstance()->getResponse()->setCookie('dzangocart', 
                                                     '', 
                                                     time() - 3600, 
                                                     '/');
}

