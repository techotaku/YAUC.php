<?php
namespace Yauc;

/**
 * Service Locator
 */
class ServiceLocator
{
  private static $_instance;

  private $registeredServices;
  private $services;

  public function __construct()
  {
    $this->registeredServices = array();
    $this->services = array();
  }

  public static function instance()
  {
    if (!is_object(self::$_instance))
    {
      self::$_instance = new ServiceLocator();
    }
    return self::$_instance; 
  }

  public function registerService($serviceName, $className, array $parameters = array())
  {
    if (!array_key_exists($serviceName, $this->registeredServices)) {
      $this->registeredServices[$serviceName] = array('class' => $className, 'parameters' => $parameters);

      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function getService($serviceName, $interface = '')
  {
    if (!array_key_exists($serviceName, $this->services)) {
      if ($this->createServiceInstance($serviceName)) {
        if ($interface == '' || ($interface != '' && $this->services[$serviceName] instanceof $interface))
        {
          return $this->services[$serviceName];
        }        
      }
      
      throw new \Exception('Cannot locate specified service: '.$serviceName.'. Service has not been registered or failed to initialize.');
    } else {
      return $this->services[$serviceName];
    }
  }

  private function createServiceInstance($serviceName)
  {
    if (!array_key_exists($serviceName, $this->registeredServices)) {
      return FALSE;
    }

    $className = $this->registeredServices[$serviceName]['class'];
    $parameters = $this->registeredServices[$serviceName]['parameters'];
    $reflect  = new \ReflectionClass($className);
    $instance = $reflect->newInstanceArgs($parameters);
    if (is_object($instance)) {
      $this->services[$serviceName] = $instance;

      return TRUE;
    } else {
      return FALSE;
    }
  }

}
