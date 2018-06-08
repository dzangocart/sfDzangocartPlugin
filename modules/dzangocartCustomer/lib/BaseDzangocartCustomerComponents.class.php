<?php
class BaseDzangocartCustomerComponents extends sfComponents {
  
  public function executeOrders(sfWebRequest $request) {
    $this->date_format = $this->getUser()->getDateFormat();
    $this->orders = Dzangocart::getOrders($this->customer_code, null, array(),
                                          $this->show_test_orders);
  }
}