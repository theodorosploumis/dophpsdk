<?php

/**
 * Several docker management commands and utilities.
 */

namespace dophpsdk;

class Docker {
  
  public function images($options = "", $image = "") {
    $cmd = "docker images ";
    $cmd .= $options;
    $cmd .= " --format ";
    $cmd .= '\"{{.ID}}\":{\"image\":\"{{.Repository}}:{{.Tag}}\",\"created\":\"{{.CreatedSince}}\",\"size\":\"{{.Size}}\"}';
    $cmd .= " " . $image;
  
    exec($cmd, $result);
    
    // Create a temporary json file
    $file = "/tmp/docker_images_" . mt_rand(0,999) . ".json";
    $result = "{\n" . implode(',', $result) . "\n}";
    file_put_contents($file, $result);
  
    // Get array from json
    $array = json_decode(file_get_contents($file), TRUE);
  
    // Delete file
    unlink($file);
  
    // Return the final php array
    return $array;
  }
  
  /**
   * List containers.
   * See "docker ps --help" for more details.
   *
   * @param string $options
   * @return array
   */
  public function ps($options = "-a") {
    $cmd = "docker ps ";
    $cmd .= $options;
    $cmd .= " --format ";
    $cmd .= '\'"{{.ID}}":{"id":"{{.ID}}","image":"{{.Image}}","created":"{{.CreatedAt}}","ports":"{{.Ports}}", "status":"{{.Status}}","names":"{{.Names}}"}\'';
  
    exec($cmd, $result);
  
    // Create a temporary json file
    $file = "/tmp/docker_ps" . mt_rand(0, 999) . ".json";
    $result = "{\n" . implode(',', $result) . "\n}";
    file_put_contents($file, $result);
  
    // Get array from json
    $array = json_decode(file_get_contents($file), TRUE);
  
    // Delete file
    unlink($file);
  
    // Return the final php array
    return $array;
  }
  
  /**
   * Get container stats in array.
   * See "docker stats --help" for more details.
   *
   * @param string $container_id
   * @return array
   */
  public function stats($container_id = "") {
    $cmd = 'docker stats --no-stream ';
    $cmd .= '--format ';
    $cmd .= '\'"{{.Container}}": {"ID": "{{.Container}}","memory":{"raw":"{{.MemUsage}}","percent":"{{.MemPerc}}"},"cpu":"{{.CPUPerc}}"}\'';
    $cmd .= " " . $container_id;
    
    exec($cmd, $result);
    
    // Create a temporary json file
    $file = "/tmp/docker_stats " . mt_rand(0, 999) . ".json";
    $result = "{\n" . implode(',', $result) . "\n}";
    file_put_contents($file, $result);
    
    // Get array from json
    $array = json_decode(file_get_contents($file), TRUE);
    
    // Delete file
    unlink($file);
    
    // Return the final php array
    return $array;
  }
  
  /**
   * Get a container by ID or Name.
   *
   * @param string $unique
   * @return \dophpsdk\Container|bool
   */
  public function getContainer($unique) {
    $cmd = "docker inspect --format '{{.ID}}' " . $unique;
    
    exec($cmd, $result);
    
    if ($result[0] == "") {
      print "There is no such container.";
      return FALSE;
    }
    else {
      $container = new Container("any", "any");
      $container->setId($result[0]);
      
      $inspect = $container->inspect();
      $name = $inspect["Name"];
      $name = str_replace("/", "", $name);
      
      $image = $inspect["Config"]["Image"];
      $command = $inspect["Config"]["Cmd"];
      $exposedports = $inspect["Config"]["ExposedPorts"];
      
      $container->setName($name);
      $container->setImage($image);
      $container->setCommand($command);
      $container->setPorts($exposedports);
      
      return $container;
    }
    
  }
  
}
