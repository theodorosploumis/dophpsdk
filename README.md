# Simple Docker php sdk

**DOcker PHP SDK -> dophpsdk**

This is a small and simple php package that allows docker manipulations through php.

Similar projects can be found at https://github.com/theodorosploumis/awesome-docker-php.

### Requirements
- php
- composer

Php needs to be able to run docker commands. For example, if using Apache server run this:

```
usermod -aG docker www-data
```

### Installation

```
composer require tplcom/dophpsdk
```

### Usage

For all the available functions see the classes [Container.php](https://github.com/theodorosploumis/dophpsdk/blob/master/src/Container.php) and [Docker.php](https://github.com/theodorosploumis/dophpsdk/blob/master/src/Docker.php).

```
<?php

require_once __DIR__ . "/src/Docker.php";
require_once __DIR__ . "/src/Container.php";

include 'vendor/autoload.php';

use dophpsdk\Docker;
use dophpsdk\Container;

$name = "drupal";
$image = "drupal:8.4.3";
$container_name = "drupal_4";

// Create some new Container objects for image https://hub.docker.com/_/drupal
 for ($i = 1; $i <= 5; $i++) {
   $settings = [
     "Name" => $name . "_" . $i,
     "Config" => [
       "Image" => $image
     ]
   ];
   
   $container = new Container($settings);

   // Get the Container object
   var_dump($container);
 }
 
// Create a new Docker object
$docker = new Docker();

$info = $docker->info();
var_dump($info);

$version = $docker->version();
var_dump($version);

$images = $docker->images();
var_dump($images);

$ps = $docker->ps();
var_dump($ps);

$stats = $docker->ContainerStats();
var_dump($stats);

$logs = $docker->ContainerLogs($container_name);
var_dump($logs);

$docker->ContainerStop($container_name);
$docker->ContainerStart($container_name);
$docker->ContainerRestart($container_name);

$isRunning = $docker->ContainerIsRunning($container_name);

if($isRunning == TRUE) {
  print "Yes, container " . $container_name . " is running\n";
} else if (!is_null($isRunning) && $isRunning == FALSE) {
  print "No, container " . $container_name . " is not running\n";
} else {
  print "There is no such container " . $name . "\n";
}

$create = $docker->ContainerCreate($image);
var_dump($create);

$run = $docker->ContainerRun($image);
var_dump($run);

$rm = $docker->ContainerRm("drupal_1");
var_dump($rm);

$kill = $docker->ContainerKill("drupal_1");
var_dump($kill);

$delete = $docker->ContainerDelete("drupal_2");
var_dump($delete);

$rename = $docker->ContainerRename("drupal_4", "My name");
var_dump($rename);

$exec = $docker->ContainerExec("drupal_4", "ls -l");
var_dump($exec);

$update = $docker->ContainerUpdate("drupal_4", "--restart on-failure");
var_dump($docker->ContainerInspect("drupal_4"));

$docker->ContainerPause("drupal_4");
$unpause = $docker->ContainerUnpause("drupal_4");
var_dump($unpause);

$commit = $docker->ContainerCommit("drupal_4", "test/test");
var_dump($docker->images("test/test"));

$rmi = $docker->rmi("test/test");
var_dump($rmi);

$diff = $docker->ContainerDiff("drupal_4");
var_dump($diff);

```

### License

**GNU v2**. 

See [LICENSE](https://github.com/theodorosploumis/dophpsdk/blob/master/LICENSE).
