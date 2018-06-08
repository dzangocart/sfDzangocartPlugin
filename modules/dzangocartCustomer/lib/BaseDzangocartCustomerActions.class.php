<?php
class BaseDzangocartCustomerActions extends sfActions {
  
  public function preExecute() {
    $this->customer_code = $this->getCustomerCode();
  }
  
  public function executeOrders(sfWebRequest $request) {
    $this->forward404Unless($this->customer_code);
        
    $this->date_format = $this->getuser()->getDateFormat();
    $this->orders = Dzangocart::getOrders($this->customer_code, null, array(), 
                                          $request->getParameter('test', sfConfig::get('app_dzangocart_show_test_orders')));
  }
  
  public function executePurchases(sfWebRequest $request) {
    
  }
  
  public function executePayments(sfWebRequest $request) {
    
  }
  
  protected function getCustomerCode() {
    $getter = sfConfig::get('app_dzangocart_customer_code_getter', 'getDzangocartCustomerCode');
    
    if (method_exists($this->getUser(), $getter)) {
      $customer_code = $this->getUser()->{$getter}();
    }
    
    return $customer_code;
  }
  
}