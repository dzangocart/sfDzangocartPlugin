<?php
class BaseDzangocartCallbackActions extends sfActions {
  
  public function executeProcess(sfWebRequest $request) {
    $data = $this->getOrderData($request);
  
    $this->processOrder($data);
    $request->setParameter('sf_format', 'json');
    return $this->renderText(1);
  }
  
  public function executeError(sfWebRequest $request) {
    $message = $request->getParameter('message', $this->getUser()->getFlash('error'));
    $this->error = array('error' => $message);
  }
  
  protected function getOrderData(sfWebRequest $request) {
    if (!$request->hasParameter('order')) {
      return $this->error('Missing order parameter in query');
    }
  
    $data = $request->getParameter('order');
  
    $key = $this->getDzangocartKey();
    if (!$key) {
      return $this->error('No secret key defined');
    }
  
    try {
      $data = json_decode(Dzangocart::decrypt($data, $key), true);
      $this->logMessage(print_r($data, true), 'debug');
    }
    catch (Exception $e) {
      return $this->error($e->getMessage());
    }
  
    if (!is_array($data)) {
      return $this->error('Invalid format for order data');
    }
  
    if (!array_key_exists('customer', $data)) {
      return $this->error('No customer data supplied');
    }
  
    return $data;
  }
  
  protected function getDzangocartKey() {
    return sfConfig::get('app_dzangocart_key');
  }
  
  protected function processOrder($data) {
    $order_class = $this->getOrderClass();
    $order = new $order_class($data);
    $order->process();
  }
  
  protected function getOrderClass() {
    return sfConfig::get('app_dzangocart_order_class', 'DzangocartOrder');
  }
  
  protected function checkCaller() {
    return true;
    
    /*
    if (!in_array(@$_SERVER['REMOTE_ADDR'], sfConfig::get('app_dzangocart_callback_authorized_ip_addresses', array('127.0.0.1')))) {
      $message = sprintf('Url %s requested by host %s [%s]',
                         @$_SERVER['REQUEST_URI'],
                         @$_SERVER['REMOTE_HOST'],
                         @$_SERVER['REMOTE_ADDR']);
                         $this->logMessage($message, 'alert');
      die('Not authorized');
    }
    */
  }
  
  protected function error($message) {
    $this->logMessage($message, 'err');
    $this->getUser()->setFlash('error', $message);
    $this->forward($this->getModuleName(), 'error');
  }
  
  
}