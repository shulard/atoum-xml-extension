<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use
    mageekguy\atoum,
    mageekguy\atoum\xml\asserters\xml as testedClass
;

class xml extends atoum\test
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
}
