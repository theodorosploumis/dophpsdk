# Simple Docker php sdk

**DOcker PHP SDK -> dophpsdk**

This is a small and simple php package that allows docker manipulations through php.

Similar projects can be found at https://github.com/theodorosploumis/awesome-docker-php.

### Requirements
- php
- composer

### Installation

```
composer require tplcom/dophpsdk
```

### Usage

```
<?php

use dophpsdk\Docker;
use dophpsdk\Container;

$image = "ubuntu:16.04";

// Create a new Container object
$container = new Container($image);

// Create a new Docker object
$docker = new Docker();
```

### License

GNU v2. See [LICENSE].
