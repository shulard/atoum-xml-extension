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
}
