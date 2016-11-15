# shulard/atoum-xml-extension [![Build Status](https://travis-ci.org/shulard/atoum-xml-extension.svg?branch=master)](https://travis-ci.org/shulard/atoum-xml-extension) [![Latest Stable Version](https://img.shields.io/packagist/v/shulard/atoum-xml-extension.svg)](https://packagist.org/packages/shulard/atoum-xml-extension)

This atoum extension allows you to test XML document using [atoum](https://github.com/atoum/atoum). It's possible to execute
xpath against the document or to validate it using DTD, XSD or RelaxNG schema.

## Example

```php
<?php
namespace shulard\example\xml;

use atoum;

class foo extends atoum\test
{
    public function testXMLDocument()
    {
        $xml = <<<XML
<?xml version="1.0" ?>
<root xmlns:atom="http://purl.org/atom/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
    <atom:feed>1<dc:node>namespaced content</dc:node>2</atom:feed>
    <node attribute="value" />
    <node m:attribute="namespaced value" />
</root>
XML;

        $this
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd('file://path/to.dtd', 'root')
                ->node
                    ->hasNamespace('atom', 'http://purl.org/atom/ns#')
                    ->isUsedNamespace('dc', 'http://purl.org/dc/elements/1.1/')
                    ->withNamespace('m', 'http://purl.org/atom/ns#')
                        ->xpath('//m:feed')
                            ->hasSize(1)
        ;
    }
}
```

When running this test, the XML document will be loaded and:

* Validate the document using a DTD;
* Check if `atom` namespace is present in document declaration;
* Check that `dc` namespace is used inside the document;
* Execute a xpath one namespaced node and check returning node collection.

## Install it

Install extension using [composer](https://getcomposer.org):

```bash
composer require --dev shulard/atoum-xml-extension
```

Enable and configure the extension using atoum configuration file:

```php
<?php

// .atoum.php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use mageekguy\atoum\xml;

$runner->addExtension(new xml\extension($script));
```

## Use it

```php
<?php
namespace shulard\example\xml;

use atoum;

class foo extends atoum\test
{
    /**
     * Test attribute on nodes
     */
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

    /**
     * Test node content using phpString asserter
     */
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

    /**
     * Validate namespace on nodes
     */
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
                            ->parent
                                ->xpath('//atom:feed')
                                    ->hasSize(1)
                                    ->item(0)
                                        ->nodeValue->isEqualTo("12")
        ;
    }

    /**
     * Validate document through schema (DTD, XSD, RNG)
     */
    public function testSchemaValidation()
    {
        $xml = <<<XML
<?xml version="1.0" ?>
<root>
    <atom:feed>1<dc:node>namespaced content</dc:node>2</atom:feed>
</root>
XML;

        $this
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd('file://path/to.dtd', 'root')
                ->node
                    ->isValidAgainstSchema
                        ->schema('/path/to/schema.xsd')
                ->node
                    ->isValidAgainstSchema
                        ->relaxNg('/path/to/file.rng')
        ;
    }
}
```

## Links

* [atoum](http://atoum.org)
* [atoum's documentation](http://docs.atoum.org)

## Licence

atoum-xml-extension is released under the Apache2 License. See the bundled [LICENSE](https://github.com/shulard/atoum-xml-extension/blob/master/LICENSE) file for details.

![atoum](http://atoum.org/images/logo/atoum.png)
