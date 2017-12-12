<?php

namespace dophpsdk;

/**
 * dophpsdk\Docker
 * Several docker management commands and utilities.
 */
class Docker {
  
  /**
   * @ToDo: Make this abstract to allow usage for more than one containers
   *
   * Start one or more stopped containers.
   * See "docker start --help" for more details.
   *
   * @param string $unique
   * @return bool
   */
  public function ContainerStart($unique) {
    $cmd = "docker start " . $unique;
    exec($cmd, $result);
    return $result == $unique;
  }
  
  /**
   * @ToDo: Make this abstract to allow usage for more than one containers.
   *
   * Restart one or more containers.
   * See "docker restart --help" for more details.
   *
   * @param string $unique
   * @return bool
   */
  public function ContainerRestart($unique) {
    $cmd = "docker restart " . $unique;
    exec($cmd, $result);
    return $result == $unique;
  }
  
  /**
   * @ToDo: Make this abstract to allow usage for more than one containers.
   *
   * Stop one or more containers.
   * See "docker stop --help" for more details.
   *
   * @param string $unique
   * @return bool
   */
  public function ContainerStop($unique) {
    $cmd = "docker stop " . $unique;
    exec($cmd, $result);
    return $result == $unique;
  }
  
  /**
   * Check if container is running.
   * If running returns "true" else "false".
   * If there is no such container returns "null".
   *
   * @param string $unique
   * @return boolean|null
   */
  public function ContainerIsRunning($unique) {
    $cmd = "docker inspect --format='{{.State.Running}}' ";
    $cmd .= $unique;
    $cmd .= " 2>&1";
  
    exec($cmd, $result);
    
    if (isset($result[1])) {
      return NULL;
    }
    
    if ($result[0] == "false") {
      return FALSE;
    } else {
      return TRUE;
    }
  }
  
  /**
   * Create a new container.
   * See "docker create --help" for more details.
   *
   * Returns the new container id or FALSE.
   *
   * @param string $options
   * @param string $name
   * @param string $image
   *
   * @return string|bool
   */
  public function ContainerCreate($image, $name = "", $options = "") {
    $cmd = "docker create ";
    if ($name) {
      $cmd .= " --name " . $name;
    }
    $cmd .= " " . $options;
    $cmd .= " " . $image;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
    
    if (isset($result[0]) && !isset($result[1])) {
      // If success returns the container ID
      return $result;
    } else {
      return FALSE;
    }
  }
  
  /**
   * Run a command in a new container.
   * See "docker run --help" for more details.
   * Notice that there is no need to add the "/bin/sh -c" before the actual command.
   *
   * @param string $image
   * @param string $name
   * @param string $command
   * @param string $options
   *
   * @return string|bool
   */
  public function ContainerRun($image, $name = "", $command = "", $options = "") {
    $cmd = "docker run -d ";
    if ($name) {
      $cmd .= "--name " . $name;
    }
    $cmd .= " " . $options;
    $cmd .= " " . $image;
    if ($command) {
      $cmd .= " /bin/sh -c " . $command;
    }
    
    exec($cmd, $result);
  
    if (isset($result[0]) && !isset($result[1])) {
      // If success returns the container ID
      return $result;
    } else {
      return FALSE;
    }
  }
  
  /**
   * Remove a container.
   * See "docker rm --help" for more details.
   * Notice that if the container is running it will not be removed except if using "-f" as options.
   *
   * @param string $unique
   * @param string $options
   *
   * @return bool
   */
  public function ContainerRm($unique, $options = "") {
    $cmd = "docker rm ";
    $cmd .= $options;
    $cmd .= " " . $unique;
    $cmd .= " 2>&1";
  
    exec($cmd, $result);
  
    if ($result[0] == $unique) {
      // If success returns TRUE
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  /**
   * Kill a container.
   * See "docker kill --help" for more details.
   *
   * @param string $unique
   * @param string $options
   *
   * @return bool
   */
  public function ContainerKill($unique, $options = "") {
    $cmd = "docker kill ";
    $cmd .= $options;
    $cmd .= " " . $unique;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
    
    if ($result[0] == $unique) {
      // If success returns TRUE
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  /**
   * Delete a container for ever. Use with caution.
   *
   * @param $unique
   *
   * @return bool
   */
  public function ContainerDelete($unique) {
    return $this->ContainerRm($unique, "-f");
  }
  
  /**
   * Rename a container (change name).
   * See "docker rename --help" for more details.
   *
   * @param string $name
   * @param string $unique
   *
   * @return bool
   */
  public function ContainerRename($unique, $name) {
    $cmd = "docker rename ";
    $cmd .= $unique;
    $cmd .= " " . $this::sanitizeContainerName($name);
    
    exec($cmd, $result);
    
    if (!isset($result[0])) {
      // If success returns TRUE
      return TRUE;
    } else {
      return FALSE;
    }
  }
  
  /**
   * Inspect a container using Id or Name.
   * See "docker inspect --help" for more details.
   *
   * Returns an array from the json data.
   * We can use var_damp etc to get the array keys/values.
   *
   * @param string $options
   * @param string $unique
   *
   * @return mixed
   */
  public function ContainerInspect($unique, $options = "") {
    $cmd = "docker inspect ";
    $cmd .= $options;
    $cmd .= "--format '{{json .}}' ";
    $cmd .= " " . $unique;
    
    exec($cmd, $result);
    
    return $this::JsonOutputToArray($result, "Id");
  }
  
  /**
   * Fetch the logs of a container.
   * See "docker logs --help" for more details.
   *
   * @param string $options
   * @param string $unique
   *
   * @return array
   */
  public function ContainerLogs($unique, $options = "") {
    $cmd = "docker logs ";
    $cmd .= $options . " ";
    $cmd .= $unique;
    $cmd .= " 2>&1";

    exec($cmd, $result);
    
    return $result;
  }
  
  /**
   * Get container stats in array.
   * See "docker stats --help" for more details.
   *
   * @param string $unique
   * @return mixed
   */
  public function ContainerStats($unique = "") {
    $cmd = "docker stats ";
    $cmd .= " --no-stream --no-trunc --format '{{json .}}' ";
    $cmd .= $unique;
    
    exec($cmd, $result);
    
    return $this->JsonOutputToArray($result, "ID");
  }
  
  /**
   * Run a command in a running container.
   * See "docker exec --help" for more details.
   *
   * @param string $unique
   * @param string $command
   *
   * @return array|bool
   */
  public function ContainerExec($unique, $command) {
    $cmd = "docker exec -ti ";
    $cmd .= " ". $unique;
    $cmd .= " ". $command;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
    
    if ($result[0] == "Error: No such container: " . $unique) {
      return FALSE;
    } else {
      return $result;
    }
  }
  
  /**
   * Update configuration of one or more containers.
   * See "docker update --help" for more details.
   *
   * @param string $unique
   * @param string $options
   *
   * @return bool
   */
  public function ContainerUpdate($unique, $options) {
    $cmd = "docker update ";
    $cmd .= $options;
    $cmd .= " " . $unique;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
    
    if ($result[0] == $unique) {
      return TRUE;
    } else {
      return $result;
    }
  }
  
  /**
   * Pause all processes within a container.
   * See "docker pause --help" for more details.
   *
   * @param string $unique
   *
   * @return array|bool
   */
  public function ContainerPause($unique) {
    $cmd = "docker pause " . $unique;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
  
    if (isset($result[0]) && $result[0] == $unique) {
      return TRUE;
    } else {
      return $result;
    }
  }
  
  /**
   * Unpause all processes within a container.
   * See "docker unpause --help" for more details.
   *
   * @param string $unique
   *
   * @return array|bool
   */
  public function ContainerUnpause($unique) {
    $cmd = "docker unpause " . $unique;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
  
    if (isset($result[0]) && $result[0] == $unique) {
      return TRUE;
    } else {
      return $result;
    }
  }
  
  /**
   * Commit a container to create a new image.
   * See "docker commit --help" for more details.
   *
   * @param string $image | The new docker image in the format [image]:[tag]
   * @param string $unique
   * @param string $options
   *
   * @return array|bool
   */
  public function ContainerCommit($unique, $image, $options = "") {
    $cmd = "docker commit ";
    $cmd .= $options;
    $cmd .= " " . $unique;
    $cmd .= " " . $image;
    $cmd .= " 2>&1";
  
    exec($cmd, $result);
  
    if (isset($result[0]) && $result[0] == $unique) {
      return TRUE;
    } else {
      return $result;
    }
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
    $cmd .= " --format '{{json .}}'";
    
    exec($cmd, $result);
  
    return $this->JsonOutputToArray($result, "ID");
  }
  
  /**
   * List Images.
   * See "docker images --help" for more details.
   *
   * @param string $options
   * @param string $image
   * @return array|bool
   */
  public function images($options = "", $image = "") {
    $cmd = "docker images ";
    $cmd .= $options;
    $cmd .= " --no-trunc --digests --format '{{json .}}' ";
    $cmd .= $image;
    
    exec($cmd, $result);
    
    return $this::JsonOutputToArray($result, "ID");
  }
  
  /**
   * Remove one or more images.
   * See "docker rmi --help" for more details.
   *
   * @param string $image
   * @param string $options
   *
   * @return array|bool
   */
  public function rmi($image, $options = "") {
    $cmd = "docker rmi ";
    $cmd .= $options;
    $cmd .= " " . $image;
    $cmd .= " 2>&1";
  
    exec($cmd, $result);
  
    if (strpos($result[0], 'Error') === 0) {
      return $result;
    } else {
      return TRUE;
    }
  }
  
  /**
   * Inspect changes to files or directories on a container's filesystem.
   * See "docker diff --help" for more details.
   *
   * @param $unique
   *
   * @return array|bool
   */
  public function ContainerDiff($unique) {
    $cmd = "docker diff ";
    $cmd .= $unique;
    $cmd .= " 2>&1";
    
    exec($cmd, $result);
  
    return $result;
  }
  
  /**
   * Docker info
   *
   * @return array
   */
  public function info() {
    $cmd = "docker info --format '{{json .}}' ";
    
    exec($cmd, $result);
    
    return json_decode($result[0], TRUE);
  }
  
  /**
   * Docker version
   *
   * @return array
   */
  public function version() {
    $cmd = "docker version --format '{{json .}}' ";
    
    exec($cmd, $result);
    
    return json_decode($result[0], TRUE);
  }
  
  /**
   * Generate an array from a json output
   *
   * @param array $result
   * @param string $id
   *
   * @return array|bool
   */
  static function JsonOutputToArray($result, $id) {
    if ($result) {
      $array = [];
      foreach ($result as $key => $value) {
        // Decode json values
        $value = json_decode($value, TRUE);
        // Create the new key
        if ($value[$id]) {
          $k = $value[$id];
          // Add new data to array
          $array[$k] = $value;
        }
      }
      if (!empty($array)) {
        return $array;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }
  
  /**
   * Sanitize strings to create the container names (helper function).
   *
   * @param $string
   * @return string
   */
  static function sanitizeContainerName($string) {
    //$string = strtolower($string);
    $regex = "/[^a-zA-Z0-9\-\_\.]/";
    
    $string = preg_replace($regex, '', $string);
    $string = trim(preg_replace('/[\s\t\n\r\s]+/', ' ', $string));
    //$string = preg_replace('/__/', '_', $string);
    
    $string = substr($string, 0, 40);
    
    return $string;
  }

}
