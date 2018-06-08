<?php
class DzangocartFilterForm extends sfFormFilter {
  
  public function setup() {
    $this->setWidgets(array(
      'date_from' => new cpDatePickerWidget(
          array('culture' => $this->getCulture(),
                'dateFormat' => 'dd/mm/yyyy',
                'startDate' => '01/01/2010',
                'label' => 'From',
                'noscript' => true),
          array('class' => 'dateInput')),
      'date_to' => new cpDatePickerWidget(
          array('culture' => $this->getCulture(),
                'dateFormat' => 'dd/mm/yyyy',
                'startDate' => '01/01/2010',
                'label' => 'To',
                'noscript' => true),
          array('class' => 'dateInput')),
      'test' => new sfWidgetFormInputCheckbox(
          array('label' => 'Include test purchases'))
    ));
    
    $this->setValidators(array(
      'date_from' => new sfValidatorDate(),
      'date_to' => new sfValidatorDate(),
      'test' => new sfValidatorPass()
    ));
    
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('dzangocart');  
    $this->widgetSchema->setFormFormatterName('uniForm');
  }
  
  public function getCulture() {
    return $this->getOption('culture');
  }
}