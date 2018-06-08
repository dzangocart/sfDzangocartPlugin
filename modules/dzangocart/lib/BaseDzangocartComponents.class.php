<?php
class BaseDzangocartComponents extends sfComponents {
  
  public function executePurchases(sfWebRequest $request) {
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=UTF-8');
    
    if (!isset($this->purchase_classname)) {
      $this->purchase_classname = $this->getPurchaseClassName($this->category);
    }
    
    $params = array();
    $params['date_from'] = $this->from->format('Y-m-d');
    $params['date_to'] = $this->to->format('Y-m-d');
    
    if ($this->category) {
      $params['category'] = $this->category;
      $params['include_subcategories'] = isset($this->include_subcategories) ? 
                                           $this->include_subcategories : 
                                           1;
    }
      
    if ($this->search) {
      $params['search'] = '%' . $this->search . '%';
    }
    
    $this->data = $this->getPurchases($this->customer, $this->affiliate, $params, $this->test, $this->sort, null, $this->limit, $this->offset);
    
    if (sfConfig::get('sf_debug')) { 
      $this->params = $this->varHolder->getAll();
    }
    // Check if 'error' key exists in returned data. 
  }

  public function executeOrders(sfWebRequest $request) {
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=UTF-8');

    if (!isset($this->order_classname)) {
      $this->order_classname = $this->getOrderClassname();
    }

    $params = array();
    $params['date_from'] = $this->from->format('Y-m-d');
    $params['date_to'] = $this->to->format('Y-m-d');

    if ($this->search) {
      $params['search'] = '%' . $this->search . '%';
    }

    $this->details = $request->getParameter('details') ? explode(',', $request->getParameter('details')) : array();
    $this->data = $this->getOrders($this->customer, $this->affiliate, $params, $this->test, $this->sort, null, $this->limit, $this->offset);

    if (sfConfig::get('sf_debug')) {
      $this->params = $this->varHolder->getAll();
    }
  }

  public function executePo(sfWebRequest $request) {
    $params = $this->prepareTransactionsParams($this->getPOClassName());
    $this->data = $this->getPOTransactions($params, $this->test, $this->sort, null, $this->limit, $this->offset);
  }

  public function executePaypalExpress(sfWebRequest $request) {
    $params = $this->prepareTransactionsParams($this->getPPExpressClassName());
    $this->data = $this->getPaypalExpressTransactions($params, $this->test, $this->sort, null, $this->limit, $this->offset);    
  }

  public function executePaypalDirect(sfWebRequest $request) {
    $params = $this->prepareTransactionsParams($this->getPPDirectClassName());
    $this->data = $this->getPaypalDirectTransactions($params, $this->test, $this->sort, null, $this->limit, $this->offset);
  }

  public function executeSips(sfWebRequest $request) {
    $params = $this->prepareTransactionsParams($this->getSipsClassName());
    $params['provider'] = $this->provider;
    $this->data = $this->getSipsTransactions($params, $this->test, $this->sort, null, $this->limit, $this->offset);
  }

  protected function prepareTransactionsParams($classname) {
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=UTF-8');

    if (!isset($this->transaction_classname)) {
      $this->transaction_classname = $classname;
    }
    
    $params = array();
    $params['affiliate'] = $this->affiliate;
    $params['date_from'] = $this->from->format('Y-m-d');
    $params['date_to'] = $this->to->format('Y-m-d');

    if ($this->search) {
      $params['search'] = '%' . $this->search . '%';
    }

    if (sfConfig::get('sf_debug')) {
      $this->params = $this->varHolder->getAll();
    }
    
    return $params;
  }

  protected function getPurchaseClassname($category = null) {
    return 'DzangocartPurchase';
  }

  protected function getOrderClassname() {
    return 'DzangocartOrder';
  }

  protected function getPOClassname() {
    return 'DzangocartPOTransaction';
  }

  protected function getPPExpressClassname() {
    return 'DzangocartPPExpressTransaction';
  }

  protected function getPPDirectClassname() {
    return 'DzangocartPPDirectTransaction';
  }

  protected function getSipsClassname() {
    return 'DzangocartSipsTransaction';
  }

  protected function getPurchases($customer_code = null,
                                  $affiliate_id = null, 
                                  $params = array(), 
                                  $test = false, 
                                  $sort_by = null, 
                                  $list_by = null, 
                                  $limit = null, 
                                  $offset = 0) {
    return Dzangocart::getPurchases($customer_code, $affiliate_id, $params, $test, $sort_by, $list_by, $limit, $offset);    
  }

  protected function getOrders($customer_code = null,
                               $affiliate_id = null,
                               $params = array(),
                               $test = false,
                               $sort_by = null,
                               $list_by = null,
                               $limit = null,
                               $offset = 0) {
    return Dzangocart::getOrders($customer_code, $affiliate_id, $params, $test, $sort_by, $list_by, $limit, $offset);
  }

  protected function getPOTransactions($params = array(), 
                                       $test = false,
                                       $sort_by = null,
                                       $list_by = null,
                                       $limit = null,
                                       $offset = 0) {
    return Dzangocart::getPurchaseOrderTransactions($params, $test, $sort_by, $list_by, $limit, $offset);
  }

  protected function getPaypalExpressTransactions($params = array(),
                                                  $test = false,
                                                  $sort_by = null,
                                                  $list_by = null,
                                                  $limit = null,
                                                  $offset = 0) {
    return Dzangocart::getPaypalExpressTransactions($params, $test, $sort_by, $list_by, $limit, $offset);
  }

  protected function getPaypalDirectTransactions($params = array(),
                                                 $test = false,
                                                 $sort_by = null,
                                                 $list_by = null,
                                                 $limit = null,
                                                 $offset = 0) {
    return Dzangocart::getPaypalDirectTransactions($params, $test, $sort_by, $list_by, $limit, $offset);
  }

  protected function getSipsTransactions($params = array(),
                                         $test = false,
                                         $sort_by = null,
                                         $list_by = null,
                                         $limit = null,
                                         $offset = 0) {
    return Dzangocart::getSipsTransactions($params, $test, $sort_by, $list_by, $limit, $offset);
  }
}