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
        "shulard/atoum-xml-extension": "^0.0"
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

## Use it

```php

<?php
namespace shulard\example\xml;

use atoum;

class foo extends atoum\test
{
    public function testAttributes()
    {
        $xml = <<<XML
<?xml version="1.0" ?>
<root>
    <node attribute="value" />
    <node m:attribute="namespaced value" />
</root>
XML;

        $node = $this->xml($xml)
            ->children
            ->item(0);
        $node
            ->attributes()
                ->hasSize(1)
                ->string['attribute']->isEqualTo('value')
        ;
        $node
            ->attributes('m')
                ->hasSize(1)
                ->string['attribute']->isEqualTo('namespaced value')
        ;
    }

    public function testXpathAndNodeContent()
    {
        $xml = <<<XML
<?xml version="1.0" ?>
<root>
    <node attribute="value">content</node>
</root>
XML;

        $this
            ->then
                ->xml($xml)
                ->xpath('//node')
                    ->hasSize(1)
                    ->item(0)
                        ->nodeValue->isEqualTo('content')
        ;
    }

    public function testNamespaces()
    {
        $xml = <<<XML
<?xml version="1.0" ?>
<root xmlns:atom="http://purl.org/atom/ns#" xmlns:toto="http://example.com" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <atom:feed>1<dc:node>namespaced content</dc:node>2</atom:feed>
</root>
XML;

        $this
            ->then
                ->xml($xml)
                ->hasNamespace('atom', 'http://purl.org/atom/ns#')
                ->isUsedNamespace('dc', 'http://purl.org/dc/elements/1.1/')
                    ->withNamespace('m', 'http://purl.org/atom/ns#')
                    ->xpath('//m:feed')
                        ->hasSize(1)
                        ->item(0)
                            ->xpath('./dc:node')
                                ->hasSize(1)
                            ->parent()
                                ->xpath('//atom:feed')
                                    ->hasSize(1)
                                    ->item(0)
                                        ->nodeValue->isEqualTo("12")
        ;
    }
}

```
