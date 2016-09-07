<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use
    mageekguy\atoum,
    mageekguy\atoum\xml\asserters\node as testedClass
;

class node extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf('mageekguy\atoum\asserter')
        ;
    }

    public function testSetWithInvalidXml()
    {
        $string = $this->realdom->regex('/[a-z]+/');
        $this
            ->given(
                $test = $this
            )
            ->if($asserter = new testedClass())
            ->then
                ->exception(function() use ($asserter, & $value, $test, $string) {
                        $asserter->setWith($value = $test->sample($string));
                    }
                )
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s is not a valid XML', $value))
        ;
    }

    public function testHasNamespaceFail()
    {
        $this
            ->given(
                $test = $this,
                $xml = new \SimpleXmlElement('<?xml version="1.0"?><root></root>'),
                $prefix = 'm',
                $uri = 'http://example.com'
            )
            ->if($asserter = new testedClass())
            ->and($asserter->setWith($xml))
            ->then
                ->exception(function() use ($asserter, & $value, $test, $prefix, $uri) {
                        $asserter->isUsedNamespace($prefix, $uri);
                    }
                )
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s namespace does not exists with URI: %s', $prefix, $uri))
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
                            ->parent
                                ->xpath('//atom:feed')
                                    ->hasSize(1)
                                    ->item(0)
                                        ->nodeValue->isEqualTo("12")
        ;
    }

    public function testHasDocNamespaceFail()
    {
        $this
            ->given(
                $test = $this,
                $xml = new \SimpleXmlElement('<?xml version="1.0"?><root></root>'),
                $prefix = 'm',
                $uri = 'http://example.com'
            )
            ->if($asserter = new testedClass())
            ->and($asserter->setWith($xml))
            ->then
                ->exception(function() use ($asserter, & $value, $test, $prefix, $uri) {
                        $asserter->hasNamespace($prefix, $uri);
                    }
                )
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s document namespace does not exists with URI: %s', $prefix, $uri))
        ;
    }

    public function testSetWithSimpleXmlElement()
    {
        $this
            ->given(
                $test = $this,
                $xml = new \SimpleXmlElement('<?xml version="1.0"?><root></root>')
            )
            ->if($asserter = new testedClass())
            ->and($asserter->setWith($xml))
            ->then
                ->object($asserter->size)
                    ->isInstanceOf('mageekguy\atoum\asserters\integer')
        ;
    }

    public function testChildrenProperty()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node>
        <subnode />
    </node>
    <node>
        <subnode />
    </node>
</root>
XML;

        $this
            ->if($asserter = new testedClass())
            ->and($asserter->setWith($xml))
            ->then
                ->object($children = $asserter->children)
                    ->isInstanceOf('mageekguy\atoum\xml\asserters\nodes')
        ;

        $children
            ->size
                ->isEqualTo(2);
    }

    public function testXpath()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node>Node content</node>
    <node>
        <subnode />
    </node>
</root>
XML;

        $this
            ->xml($xml)
            ->xpath('//node')
                ->hasSize(2)
                ->item(0)
                    ->nodeValue
                        ->isEqualTo('Node content')
        ;
    }

    public function testXpathWithNamespace()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root xmlns:space="http://example.com">
    <node>Node content</node>
    <space:node>
        <subnode space:attr="value" />
    </space:node>
</root>
XML;

        $xml = $this->xml($xml);

        $xml
            ->xpath('//node')
                ->hasSize(1)
        ;
        $xml
            ->withNamespace('m', 'http://example.com')
            ->xpath('//m:node')
                ->hasSize(1)
                ->first()
                ->xpath('./subnode/@space:attr')
                    ->hasSize(1)
                    ->item(0)
                        ->nodeValue
                            ->isEqualTo('value')
        ;
    }

    public function testAttributes()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root xmlns:m="http://example.com">
    <node attr1="value 1" m:attr2="value 2">Node content</node>
    <node>
        <subnode />
    </node>
</root>
XML;

        $item = $this
            ->xml($xml)
            ->isUsedNamespace('m', 'http://example.com')
            ->children
                ->item(0)
        ;
        $item
            ->attributes('m')
                ->hasSize(1)
                ->string['attr2']->isEqualTo('value 2')
        ;
        $item
            ->attributes('http://example.com')
                ->hasSize(1)
                ->string['attr2']->isEqualTo('value 2')
        ;
    }
}
