<?php
class sfDzangocartPluginConfiguration extends sfPluginConfiguration {

  public function initialize() {
    Dzangocart::$api_url = sfConfig::get('app_dzangocart_api_url'); ;
    Dzangocart::$api_credentials = sfConfig::get('app_dzangocart_api_credentials');
    Dzangocart::$key = sfConfig::get('app_dzangocart_key');
    Dzangocart::$cookie_lifetime = 36000;
  }
}