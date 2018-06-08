<?php
class BaseDzangocartActions extends sfActions {

  protected $filter;
  protected $purchases_batch_actions = array('cancel' => 'batchCancel',
                                             'invoice' => 'batchInvoice');

  protected $orders_batch_actions = array('cancel' => 'batchCancel',
                                          'invoice' => 'batchInvoice');

  /*
   * $param: a date string in dd/mm/yyyy format
   */
  protected function getDate($date) {
    return DateTime::createFromFormat('d/m/Y', $date);
    list($d, $m, $y) = explode('/', $param);
    $date = mktime(0, 0, 0, $m, $d, $y);
    return new DateTime($date);
  }

  public function preExecute() {
    $this->module = $this->getModuleName();
    $this->action = $this->getActionName();
    $request = $this->getRequest();
    if ($request->hasParameter('date_from')) {
      try {
        $this->from = DateTime::createFromFormat($this->getDateFormat(), $this->getRequestParameter('date_from'));
      }
      catch (Exception $e) {}
    }
    if (!$this->from) {
      $from = date('Y-m-01');
      $this->from = new DateTime($this->getUser()->getAttribute('from', $from, 'orders'));
    }

    if ($request->hasParameter('date_to')) {
      try {
        $this->to = DateTime::createFromFormat($this->getDateFormat(), $this->getRequestParameter('date_to'));
      }
      catch (Exception $e) {}
    }
    if (!$this->to) {
      $to = new DateTime($this->from->format('Y-m-d'));
      $to->modify('last day of ' . $to->format('M'));
      $this->to = new DateTime($this->getUser()->getAttribute('to', $to->format('Y-m-d'), 'orders'));
    }

    if ($request->hasParameter('test')) {
      $this->test = $request->getParameter('test');
    }
    else { $this->test = false; }

    $this->getUser()->setAttribute('from', $this->from->format('Y-m-d'), 'orders');
    $this->getUser()->setAttribute('to', $this->to->format('Y-m-d'), 'orders');
    $this->getUser()->setAttribute('test', $this->test, 'orders');

    /*
    $c = new Criteria();
    $c->add(AffiliatePeer::DZANGOCART_ID, null, Criteria::ISNOTNULL);
    $_affiliates = AffiliatePeer::doSelect($c);
    $affiliates = array();
    foreach ($_affiliates as $a) { $affiliates[$a->getDzangocartId()] = $a; }
    $this->affiliates = $affiliates;
    */
    $this->affiliates = array();
    $this->affiliate = null;
    $this->customer = null;

    $this->category = $this->getCategory($request);
    $this->include_subcategories = $this->includeSubcategories();

    $this->sort = array();
    $this->limit = $this->getLimit();

    $this->offset = $this->getOffset();

    $this->search = $this->getSearch();

    $this->filterForm = new DzangocartFilterForm(
      array('date_from' => $this->from->format('d/m/Y'),
            'date_to' => $this->to->format('d/m/Y'),
            'test' => $this->test),
      array('culture' => $this->getUser()->getCulture()));
  }

  public function executeOrders(sfWebRequest $request) {
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setRequestFormat('json');
      $this->sort = $this->getSort($this->getOrdersSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['customer'] = $this->customer;
      $vars['order_classname'] = $this->getOrderClassname();
      return $this->renderComponent('dzangocart', 'orders', $vars);
    }
    else {
      $this->filterForm->getWidget('test')->setLabel('Include test orders');
      $this->batch_actions = $this->getBatchActions('orders');
    }
  }

  public function executePurchases(sfWebRequest $request) {
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setParameter('sf_format', 'json');
      $this->sort = $this->getSort($this->getPurchasesSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['customer'] = $this->customer;
      $vars['purchase_classname'] = $this->getPurchaseClassname($request->getParameter('category'));
      return $this->renderComponent('dzangocart', 'purchases', $vars);
    }
    else {
      $this->refund_form = $this->getRefundForm();
      $this->batch_actions = $this->getBatchActions('purchases');
    }
  }

  public function executeInvoice(sfWebRequest $request) {
    $order_id = $request->getParameter('order_id');
    $item_id = $request->getParameter('item_id');
    $this->forward404Unless($order_id || $item_id);
    list($headers, $body) = Dzangocart::getInvoice(array('order_id' => $order_id,
                                                         'item_id' => $item_id));
    // Set http headers
    $this->getResponse()->clearHttpHeaders();
    foreach ($headers as $name => $value) {
      $this->getResponse()->setHttpHeader($name, $value);
    }
    return $this->renderText($body);
  }

  public function executeBatchAction(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $action_name = $request->getParameter('action_name');
    $actions = $this->getBatchActions($type);
    // if empty type or action params, or action does not exist - do nothing (just redirect to a referer)
    if ($type && $action_name && array_key_exists($action_name, $actions)) {
      $action = $actions[$action_name];
      if (method_exists($this, 'execute' . ucfirst($action))) {
        $this->forward($this->getModuleName(), $action);
      }
      else {
        // TODO [ALK 2012-01-25] Handle the case when method does not exist
      }
    }
    $this->redirect($request->getReferer());
  }

  // TODO [ALK 2012-01-25]
  public function executeBatchCancel(sfWebRequest $request) {
    $this->redirect($request->getReferer());
  }

  public function executeBatchInvoice(sfWebRequest $request) {
    $type = $request->getParameter('type');
    $ids = $request->getParameter('ids');
    switch ($type) {
      case 'orders':
        $this->getRequest()->setParameter('order_id', $ids);
        $this->forward($this->getModuleName(), 'invoice');
        break;
      case 'purchases':
        $item_id = array();
        $order_id = array();
        if ($ids) {
          foreach ($ids as $id) {
            list($order_id[], $item_id[]) = explode('_', $id);
          }
        }
        $this->getRequest()->setParameter('order_id', $order_id);
        $this->forward($this->getModuleName(), 'invoice');
        break;
      default:
        $this->redirect($request->getReferer());
    }
  }

  public function executePo(sfWebRequest $request) {
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setRequestFormat('json');
      $this->sort = $this->getSort($this->getPOTransactionsSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['transaction_classname'] = $this->getPOClassname();
      return $this->renderComponent('dzangocart', 'po', $vars);
    }
    else {
      $this->filterForm->getWidget('test')->setLabel('Include test transactions');
    }
  }

  public function executeSogenactif(sfWebRequest $request) {
    $this->provider = 'Sogenactif';
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setRequestFormat('json');
      $this->sort = $this->getSort($this->getSipsTransactionsSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['provider'] = $this->provider;
      $vars['transaction_classname'] = $this->getSipsClassname();
      return $this->renderComponent('dzangocart', 'sips', $vars);
    }
    else {
      $this->filterForm->getWidget('test')->setLabel('Include test transactions');
      $this->setTemplate('sips');
    }
  }

  public function executePaypalDirect(sfWebRequest $request) {
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setRequestFormat('json');
      $this->sort = $this->getSort($this->getPaypalDirectSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['transaction_classname'] = $this->getPPDirectClassname();
      return $this->renderComponent('dzangocart', 'paypalDirect', $vars);
    }
    else {
      $this->filterForm->getWidget('test')->setLabel('Include test transactions');
    }
  }

  public function executePaypalExpress(sfWebRequest $request) {
    if ($request->hasParameter('x') || $request->isXmlHttpRequest()) {
      $request->setRequestFormat('json');
      $this->sort = $this->getSort($this->getPaypalExpressSortColumns());
      $vars = $this->varHolder->getAll();
      $vars['affiliate'] = $this->affiliate;
      $vars['transaction_classname'] = $this->getPPExpressClassname();
      return $this->renderComponent('dzangocart', 'paypalExpress', $vars);
    }
    else {
      $this->filterForm->getWidget('test')->setLabel('Include test transactions');
    }
  }

  public function executeCancel(sfWebRequest $request) {
    $cancel_form = new DzangocartCancelFilterForm(
      array('order_id' => $request->getParameter('order_id'),
            'item_id' => $request->getParameter('item_id')),
      array('culture' => $this->getUser()->getCulture())
    );

    if ($request->isMethod('post') || $request->getParameter('debug') == 1) {
      try {
        if ($order_id = $request->getParameter('order_id')) {
          $date = $request->getParameter('date');
          if ($date) {
            $date = DateTime::createFromFormat(sfConfig::get('app_dzangocart_date_format'), $date);
          }
          else {
            $date = new DateTime();
          }
          $data = Dzangocart::cancelOrder($order_id,
                                          $request->getParameter('gateway_id'),
                                          $date->format('Y-m-d h:i:s'));
          $request->setParameter('sf_format', 'json');
          return $this->renderText(json_encode($data));
        }
        else if ($item_id = $request->getParameter('item_id')) {
          // Dzangocart::cancelItem($item_id);
        }
      }
      catch (Exception $e) {

      }
    }
    return $this->renderPartial('cancel', array('form' => $cancel_form));
  }

  /*
  public function executeCancelOrder(sfWebRequest $request) {
    if ($request->isXmlHttpRequest()) {
      $order_id = $request->getParameter('id');
      $this->forward404Unless($order_id);
      $request->setRequestFormat('json');
      try {
        Dzangocart::cancelOrder($order_id, $request->getParameter('refund_info'));
      }
      catch (Exception $e) {
        return $this->renderError($e->getMessage());
      }
    }
    return sfView::NONE;
  }

  public function executeCancelItem(sfWebRequest $request) {
    if ($request->isXmlHttpRequest()) {
      $item_id = $request->getParameter('id');
      $this->forward404Unless($item_id);
      $request->setRequestFormat('json');
      try {
        Dzangocart::cancelItem($item_id, $request->getParameter('refund_info'));
      }
      catch (Exception $e) {
        return $this->renderError($e->getMessage());
      }
    }
    return sfView::NONE;
  }
  */

  public function executeDirectPayments() {
    $from = date('Y-m-01 H:i:s');
    $this->from = new DateTime($this->getRequestParameter('date_from', $from));
    $to = new DateTime(date('Y-m-01 H:i:s'));
    $to->add(new DateInterval('P1M'));
    $to->sub(new DateInterval('P1D'));
    $this->to = new DateTime($this->getRequestParameter('date_to', $to->format('Y-m-d H:i:s')));
    $this->type = $this->getRequestParameter('type');
    $this->test = $this->getRequestParameter('test');

    $params['date_from'] = $this->from->format('Y-m-d');
    $params['date_to'] = $this->to->format('Y-m-d');
    $params['type'] = $this->type;

    $txn = Dzangocart::getPOTransactions($params, $this->test);
    $this->transactions = $txn ? $txn : array();
  }

  public function executeSipsPayments($request) {
    $from = date('Y-m-01 H:i:s');
    $this->from = new DateTime($this->getRequestParameter('date_from', $from));
    $to = new DateTime(date('Y-m-01 H:i:s'));
    $to->add(new DateInterval('P1M'));
    $to->sub(new DateInterval('P1D'));
    $this->to = new DateTime($this->getRequestParameter('date_to', $to->format('Y-m-d H:i:s')));
    $this->merchant_id = $this->getRequestParameter('merchant_id');
    $this->test = $this->getRequestParameter('test');

    $params = array();
    $params['date_from'] = $this->from->format('Y-m-d');
    $params['date_to'] = $this->to->format('Y-m-d');
    $params['merchant_id'] = $this->merchant_id;

    $txn = Dzangocart::getSipsTransactions($params, $this->test);
    $this->transactions = $txn ? $txn : array();
  }

  public function getDateFormat() { return 'd/m/Y'; }

  public function getCategory() {
    return $this->getRequestParameter('category');
  }

  public function includeSubcategories() {
    return $this->getRequestParameter('include_subcategories', true);
  }

  public function getLimit() {
    return $this->getRequestParameter('iDisplayLength', 100);
  }

  public function getOffset() {
    return $this->getRequestParameter('iDisplayStart', 0);
  }

  public function getSearch() {
    return $this->getRequestParameter('sSearch');
  }

  public function getSort($columns = array()) {
    $sort = array();
    if ($sortCount = $this->getRequestParameter('iSortingCols')) {
      for ($i = 0; $i < $sortCount; $i++) {
        $sortCol = $this->getRequestParameter('iSortCol_' . $i, null);
        $sortDir = strtolower($this->getRequestParameter('sSortDir_' . $i, null));
        if ($sortDir != 'desc') { $sortDir = 'asc'; }

        if (!is_null($sortCol) && isset($columns[$sortCol])) {
          if (is_array($columns[$sortCol])) {
            foreach ($columns[$sortCol] as $v) {
              $sort[] = $v;
              $sort[] = $sortDir;
            }
          }
          else {
            $sort[] = $columns[$sortCol];
            $sort[] = $sortDir;
          }
        }
      }
    }
    return $sort;
  }

  public function setTemplate($name, $module = null) {
    if ('dzangocart' === $module) {
      // Set proper assets
      $config = sfViewConfigHandler::getConfiguration(array(sfConfig::get('sf_plugins_dir') . '/sfDzangocartPlugin/modules/dzangocart/config/view.yml'));
      $assets = $config[$name . 'Success'];
      if ($assets) {
        if (isset($assets['stylesheets']) && is_array($assets['stylesheets'])) {
          foreach ($assets['stylesheets'] as $file) {
            $this->getResponse()->addStylesheet($file, 'last');
          }
        }
        if (isset($assets['javascripts']) && is_array($assets['javascripts'])) {
          foreach ($assets['javascripts'] as $file) {
            $this->getResponse()->addJavascript($file, 'last');
          }
        }
      }
      // Set proper template
      $name = sfConfig::get('sf_plugins_dir') . '/sfDzangocartPlugin/modules/dzangocart/templates/' . $name;
    }
    parent::setTemplate($name);
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

  protected function getSipsClassname() {
    return 'DzangocartSipsTransaction';
  }

  protected function getPPExpressClassname() {
    return 'DzangocartPPExpressTransaction';
  }

  protected function getPPDirectClassname() {
    return 'DzangocartPPDirectTransaction';
  }

  protected function renderError($error) {
    return $this->renderPartial($this->getModuleName() . '/error', array('error' => $error));
  }

  protected function getPurchasesSortColumns() {
    return array(
             1 => 'cart.DATE',
             2 => 'item.ORDER_ID',
             3 => array('user_profile.SURNAME', 'user_profile.GIVEN_NAMES'),
             4 => 'item.NAME',
             5 => 'item.AMOUNT_EXCL',
             6 => 'item.TAX_AMOUNT',
             7 => 'item.AMOUNT_INCL',
             9 => 'cart.TEST'
           );
  }

  protected function getOrdersSortColumns() {
    return array(
             1 => 'cart.DATE',
             2 => 'cart.ID',
             3 => array('user_profile.SURNAME', 'user_profile.GIVEN_NAMES'),
             5 => 'cart.AMOUNT_EXCL',
             6 => 'cart.TAX_AMOUNT',
             7 => 'cart.AMOUNT_INCL',
             10 => 'cart.TEST'
           );
  }

  protected function getPOTransactionsSortColumns() {
    return array(
             1 => 'po_transaction.DATE',
             2 => 'po_transaction.ORDER_ID',
             3 => 'po_transaction.CHEQUE_NUMBER',
             4 => 'po_transaction.BANK',
             5 => 'po_transaction.AMOUNT',
             7 => 'po_transaction.VERIFIED_AT',
             8 => 'cart.TEST'
           );
  }

  protected function getSipsTransactionsSortColumns() {
    return array(
             1 => array('sips_transaction.PAYMENT_DATE', 'sips_transaction.PAYMENT_TIME'),
             2 => 'cart.ID',
             3 => 'sips_transaction.AMOUNT',
             5 => 'sips_transaction.PAYMENT_MEANS',
             6 => 'sips_transaction.CARD_NUMBER',
             7 => 'sips_transaction.TRANSACTION_ID',
             8 => 'sips_transaction.RESPONSE_CODE',
             9 => 'sips_transaction.BANK_RESPONSE_CODE',
             10 => 'sips_transaction.CVV_RESPONSE_CODE'
           );
  }

  protected function getPaypalExpressSortColumns() {
    return array(
             1 => 'paypal_express_transaction.ORDER_TIME',
             2 => 'paypal_express_transaction.TRANSACTION_ID',
             3 => 'paypal_express_transaction.ORDER_ID',
             4 => 'paypal_express_transaction.AMOUNT'
           );
  }

  protected function getPaypalDirectSortColumns() {
    return array(
             1 => 'paypal_direct_transaction.TIMESTAMP',
             2 => 'paypal_direct_transaction.TRANSACTION_ID',
             3 => 'paypal_direct_transaction.ORDER_ID',
             4 => 'paypal_direct_transaction.AMOUNT',
             6 => 'paypal_direct_transaction.PAYMENT_MEANS'
           );
  }

  protected function getRefundForm() {
    return Dzangocart::getRefundInfoForm();
  }

  protected function getBatchActionsProperty($type) {
    $property = $type . '_batch_actions';
    return property_exists($this, $property) ? $property : null;
  }

  /**
   * Gets the array of batch actions
   * @param string $type - type of batch actions ('purchases', 'orders', etc.)
   * @return array
   */
  protected function getBatchActions($type) {
    return ($property = $this->getBatchActionsProperty($type)) ? $this->$property : array();
  }

  /**
   * Sets batch actions
   * @param string $type - type of batch actions ('purchases', 'orders', etc.)
   * @param array $actions - the array of batch actions ('name' => 'action name')
   */
  protected function setBatchActions($type, $actions = array()) {
    if ($property = $this->getBatchActionsProperty($type)) {
      $this->$property = $actions;
    }
  }

  /**
   * Unsets the batch actions by names
   * @param string $type - type of batch actions ('purchases', 'orders', etc.)
   * @param mixed $name - the name (or array of names) of the actions
   */
  protected function unsetBatchActions($type, $name) {
    if ($property = $this->getBatchActionsProperty($type)) {
      $name = (array) $name;
      foreach ($name as $n) {
        unset($this->{$property}[$n]);
      }
    }
  }

  /**
   * Adds actions to the existing batch actions
   * @param string $type - type of batch actions ('purchases', 'orders', etc.)
   * @param array $actions - the array of batch actions ('name' => 'action name')
   */
  protected function addBatchActions($type, $actions) {
    if ($property = $this->getBatchActionsProperty($type)) {
      $this->$property = array_merge($this->$property, $actions);
    }
  }
}