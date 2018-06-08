<?php
class DzangocartFilter extends sfFilter {

  public function execute($filterChain) {
    $user = $this->getContext()->getUser();
    if ($user->isAuthenticated() && !sfConfig::get('app_dzangocart_disabled')) {
      $customer_data = $this->getCustomerData($user);
      if (empty($customer_data)) { Dzangocart::removeCookie(); }
      else {
        Dzangocart::set_cookie($customer_data,
                               sfConfig::get('app_dzangocart_key'),
                               sfConfig::get('app_dzangocart_cookie_lifetime'));
      }
    }
    else { Dzangocart::remove_cookie(); }
    
    // Execute next filter
    $filterChain->execute();
  }
  
  public function getCustomerData(sfUser $user) {
    return call_user_func(array($user, 
                                sfConfig::get('app_dzangocart_customer_data_getter',
                                              'toCustomerDataArray')));
  }
}