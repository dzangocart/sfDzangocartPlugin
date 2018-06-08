<?php
class DzangocartCancelFilterForm extends sfFormFilter {
  
  public function setup() {
    $this->setWidgets(array(
      'order_id' => new sfWidgetFormInputHidden(),
      'item_id' => new sfWidgetFormInputHidden(),
      'date' => new cpDatePickerWidget(
          array('culture' => $this->getCulture(),
                'dateFormat' => 'dd/mm/yyyy',
                'startDate' => '01/01/2010',
                'noscript' => true),
          array('class' => 'dateInput')),
      'gateway_id' => new sfWidgetFormChoice(
          array('label' => 'Refund via',
                'choices' => $this->getGateways()))
    ));
    
    $this->setValidators(array(
      'date' => new sfValidatorDate(),
      'gateway_id' => new sfValidatorPass()
    ));
    
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('dzangocart');  
    $this->setOption('inlineLabels', true);
  }
  
  public function getCulture() {
    return $this->getOption('culture');
  }
  
  protected function getGateways() {
    return array(
      5 => "Sogénactif",
      10 => "Sogénactif test"    
    );
  }
}