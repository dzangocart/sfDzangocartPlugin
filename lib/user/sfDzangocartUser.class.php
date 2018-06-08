<?php
abstract class sfDzangocartUser extends sfGuardSecurityUser {
  
  /**
   *  Returns a unique identifyer for this user.
   * 
   */
  abstract public function getCustomerCode();
  
  /**
   * Returns an array of data for this user in the format prescribed by Dzangocart.
   * 
   */
  abstract public function toCustomerDataArray();
  
  public function getOrders() {
    if (!$this->isAuthenticated()) { return array(); }
    $orders = Dzangocart::getOrders($this->getCustomerCode(), null, array(), true);

    if (!is_array($orders) || array_key_exists('error', $orders)) {
      $orders = array();
      $this->setFlash('error',
                      array_key_exists('error', $orders) ?
                        $orders['error'] :
                        'Sorry. An unexpected error occurred while retrieving your orders.');
    }

    return $orders;
  }

  public function getPurchases($category = null, $list_by = null, $sort_by = null) {
    if (!$this->isAuthenticated()) { return array(); }
    if (sfConfig::get('app_dzangocart_disabled', false)) { return array(); }

    $params = array();
    if ($category) { $params['category'] = $category; }
    if ($list_by) { $params['list_by'] = $list_by; }
    if ($sort_by) { $params['sort_by'] = $sort_by; }

    $items = Dzangocart::getItems($this->getCustomerCode(), null, $params, true);

    if (!is_array($items) || array_key_exists('error', $items)) {
      $items = array();
      $this->setFlash('error',
                      array_key_exists('error', $items) ?
                        $items['error'] :
                        'Sorry. An unexpected error occurred while retrieving your purchases.');
    }

    return $items ;
  }
  
  public function getTransactions() {
    
  }
  
  
  
}