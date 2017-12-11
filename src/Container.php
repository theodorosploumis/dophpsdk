<?php

namespace dophpsdk;

/**
 * dophpsdk\Container
 */
class Container {

  private $id;
  private $image;
  private $name;
  private $exposedports;
  private $command;
  private $hash;

  public $debug = FALSE;

  /**
   * Container constructor.
   * Notice that this will not create a new docker container!
   * The newly created object will not have an $id, $command etc.
   *
   * @param string $image | The docker image.
   * @param string $id | The container id.
   * @param string $command | The container command.
   * @param array $exposedports | The container ExposedPorts.
   * @param string $name | The container name.
   * @param string $hash | A hash used to generate a unique name.
   */
  public function __construct($image, $id = "", $name = "", $hash = "", $exposedports = [], $command = []) {
    // The docker image. It can use the format [image]:[tag]
    $this->image = $image;

    // Generate a random number to avoid name conflicts on duplicates
    if ($hash) {
      $hash = $this::sanitizeContainerName($hash);
      $this->hash = $hash;
    } else {
      $hash = mt_rand(1000, 9999);
    }

    // Create the name from all the options joined together
    if ($name) {
      $name = $hash . "." . $name;
    }
    else {
      $name = "dophpsdk_" . $hash;
    }
    $name = $this::sanitizeContainerName($name);
    // Add the new name to the object values
    $this->name = $name;

    if ($id) {
      $this->id = $id;
    }

    if ($exposedports) {
      $this->exposedports = $exposedports;
    }
  }

  /**
   * Get the container id.
   *
   * If there is no id in the object try and get it with the "docker ps" command.
   * Notice that this will return the id even of container is stopped.
   * If there is no suck container it will return "Null".
   *
   * @return string|bool
   */
  public function getId() {
    if ($this->id != "") {
      return $this->id;
    }
    else {
      $cmd = "docker ps --no-trunc --format '{{.ID}}' -a --filter 'name=" . $this->getName() . "'";

      $id = exec($cmd);
      $this->id = $id;
      return $id;
    }
  }


  /**
   * @param mixed $id
   */
  public function setId($id) {
    $this->id = $id;
  }

  /**
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * @param string $image
   */
  public function setImage($image) {
    $this->image = $image;
  }

  /**
   * @param mixed $command
   */
  public function setCommand($command) {
    $this->command = $command;
  }

  /**
   * @param array $exposedports
   */
  public function setPorts($exposedports) {
    $this->exposedports = $exposedports;
  }

  /**
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getImage() {
    return $this->image;
  }

  /**
   * @param bool $debug
   */
  public function setDebug($debug) {
    $this->debug = $debug;
  }

  public function updateData($id) {
    $docker = new Docker();
    $container = $docker->getContainer($id);

    foreach($parsedOutputLine as $key => $value) {
      $container->{$containerFields[$key]} = $value;
    }
  }

  /**
   * Check if container is running.
   * If running returns "true" else "false".
   * If there is no such container returns "null".
   *
   * @return boolean|null
   */
  public function isRunning() {
    $cmd = "docker inspect --format='{{.State.Running}}' " . $this->getId();
    $cmd .= " 2> /dev/null";

    return exec($cmd);
  }

  /**
   * @ToDo: Make this abstract to allow usage for more than one containers
   *
   * Start one or more stopped containers.
   * See "docker start --help" for more details.
   */
  public function start() {
    $cmd = "docker start " . $this->getId();

    if (!$this->isRunning()) {

    }

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * @ToDo: Make this abstract to allow usage for more than one containers.
   *
   * Restart one or more containers.
   * See "docker restart --help" for more details.
   */
  public function restart() {
    $cmd = "docker restart " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * @ToDo: Make this abstract to allow usage for more than one containers.
   *
   * Stop one or more containers.
   * See "docker stop --help" for more details.
   */
  public function stop() {
    $cmd = "docker stop " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Create a new container.
   * See "docker create --help" for more details.
   *
   * Returns the new container id if debug is enabled.
   *
   * @param string $options
   */
  public function create($options = "") {
    $cmd = "docker create ";
    $cmd .= " --name " . $this->getName();
    $cmd .= " " . $options;
    $cmd .= " " . $this->getImage();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Run a command in a new container.
   * See "docker run --help" for more details.
   * Notice that there is no need to add the "/bin/sh -c" before the actual command.
   *
   * @param string $command
   * @param string $options
   */
  public function run($command, $options = "") {
    if ($command) {
      $this->command = $command;
    }

    $cmd = "docker run -d ";
    $cmd .= " " . $options;
    $cmd .= " " . $this->getImage();
    $cmd .= " /bin/sh -c " . $command;

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Remove a container.
   * See "docker rm --help" for more details.
   * Notice that if the container is running it will not be removed except if using "-f" as options.
   *
   * @param string $options
   */
  public function rm($options = "") {
    $cmd = "docker rm " . $options . " " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Rename a container (change name).
   * See "docker rename --help" for more details.
   *
   * @param $name
   */
  public function rename($name) {
    $cmd = "docker rename " . $this->getId() . " " . $name;

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Kill a container.
   * See "docker kill --help" for more details.
   *
   * @param string $options
   */
  public function kill($options = "") {
    $cmd = "docker kill " . $options . " " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
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
   * @return array
   */
  public function inspect($options = "", $unique = "") {
    if (!$unique) {
      $unique = $this->getId();
    }

    $cmd = "docker inspect " . $options . " " . $unique;
    exec($cmd, $result);

    // Create a temporary json file
    $file = "/tmp/inspect." . $this->getId() . ".json";
    file_put_contents($file, $result);

    // Get array from json
    $array = json_decode(file_get_contents($file), TRUE);

    // Delete file
    unlink($file);

    // Return the final php array
    return $array[0];
  }

  /**
   * Run a command in a running container.
   * See "docker exec --help" for more details.
   *
   * @param $command
   * @return string|void
   */
  public function exec($command) {
    if (!$this->isRunning()) {
      if ($this->debug) {
        print "Container is not running.";
      }

      return;
    }

    $cmd = "docker exec -d " . $this->getId() ." ". $command;
    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Update configuration of one or more containers.
   * See "docker update --help" for more details.
   *
   * @param string $options
   */
  public function update($options) {
    $cmd = "docker update " . $options . " " . $this->getId();
    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Pause all processes within a container.
   * See "docker pause --help" for more details.
   */
  public function pause() {
    $cmd = "docker pause " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Commit a container to create a new image.
   * See "docker commit --help" for more details.
   *
   * @param string $image | The new docker image in the format [image]:[tag]
   * @param string $options
   */
  public function commit($image, $options = "") {
    $cmd = "docker commit " . $options . " " . $this->getId() . " " . $image;

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Inspect changes to files or directories on a container's filesystem.
   * See "docker diff --help" for more details.
   */
  public function diff() {
    $cmd = "docker diff " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
    }
  }

  /**
   * Fetch the logs of a container.
   * See "docker logs --help" for more details.
   *
   * @param string $options
   */
  public function logs($options = "") {
    $cmd = "docker logs " . $options . " " . $this->getId();

    exec($cmd, $result);

    if ($this->debug) {
      var_dump($result);
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
