<?php

class DzangocartProcessOrderTask extends sfBaseTask {

  protected function configure() {
    $this->addArguments(array(
      new sfCommandArgument('order_id', sfCommandArgument::REQUIRED, 'order id'),
    ));
    
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'admin'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_OPTIONAL, 'The connection name'),
      new sfCommandOption('dry-run', null, sfCommandOption::PARAMETER_NONE, 'Executes a dry-run'),
    ));

    $this->namespace        = 'dzangocart';
    $this->name             = 'process_order';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [ProcessOrderTask|INFO] task does things.
Call it with:

  [php symfony ProcessOrderTask|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array()) {
    sfContext::createInstance($this->configuration);
    
    if (isset($options['connection'])) {
      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
    }
    
    $order_id = $arguments['order_id'];
    $order = DzangocartOrder::getOrder($order_id);
    
    if ($options['trace']) { 
      echo print_r($order->getData(), true);
    }
    
    if (!$options['dry-run']) {
      $order->process();
    }
  }
}
