<?php

namespace dophpsdk;

/**
 * dophpsdk\Container
 */
class Container {

  private $data;
  public $debug = FALSE;

  /**
   * Container constructor.
   *
   * @param array $settings
   *
   * $settings = [
   *  "Id" => "ea1720f9eb0a6f074f84a6253db9ebd095315edd8da877cadc61051dba78e511",
   *  "Name" => "drupal_4",
   *  "Config" => [
   *    "Image" => "drupal:8.4.3"
   *  ]
   * ];
   */
  public function __construct($settings) {
    $docker = new Docker();
    
    if (isset($settings["Id"])) {
      $unique = $settings["Id"];
    }
    else if (isset($settings["Name"])) {
      $unique = $settings["Name"];
    }
    
    if (isset($unique)) {
      $inspect = $docker->ContainerInspect($unique);
      
      if ($inspect) {
        $inspect = array_shift(array_values($inspect));
        $this->setConfig($inspect);
      }
      else if (isset($settings["Config"]["Image"])) {
        $image = $settings["Config"]["Image"];
        $name = $settings["Name"];
        
        $container = $docker->ContainerRun($image, $name);
      
        $data = $docker->ContainerInspect($container[0]);
        $data = array_shift(array_values($data));
  
        $this->setConfig($data);
      }
    }
    
  }
  
  /**
   * @param mixed $data
   */
  public function setConfig($data) {
    $this->data = $data;
  }
  
  /**
   * @return mixed
   */
  public function getConfig() {
    return $this->data;
  }
  
}
