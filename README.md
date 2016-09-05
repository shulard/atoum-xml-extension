# shulard/atoum-xml-extension [![Build Status](https://travis-ci.org/shulard/atoum-xml-extension.svg?branch=master)](https://travis-ci.org/shulard/atoum-xml-extension)

![atoum](http://atoum.org/images/logo/atoum.png)

*This project is currently in development and shouldn't be used in production !*

## Install it

Install extension using [composer](https://getcomposer.org):

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:shulard/atoum-xml-extension.git"
        }
    ],
    "require-dev": {
        "shulard/atoum-xml-extension": "dev-master"
    },
}

```

Enable the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use mageekguy\atoum\xml;

$extension = new xml\extension($script);

$extension->addToRunner($runner);
```
