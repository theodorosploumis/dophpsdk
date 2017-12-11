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

require_once __DIR__ . "/vendor/autoload.php";

use dophpsdk\Docker;
use dophpsdk\Container;

// Create some new Container objects for image https://hub.docker.com/_/drupal
for ($i = 1; $i <= 4; $i++) {
    $container = new Container("drupal:8.4.3", "drupal_" . $i);

    // Start a container
    $container->run();

    // Get the container id
    print $container->getId();

    // Inspect a container
    var_dump($container->inspect());
}

// Create a new Docker object
$docker = new Docker();

// Get running containers
$docker_running_containers = $docker->ps();
var_dump($docker_running_containers);

// Get docker images
$docker_images = $docker->images();
var_dump($docker_images);

```

### License

**GNU v2**. 

See [LICENSE](https://github.com/theodorosploumis/dophpsdk/blob/master/LICENSE).
