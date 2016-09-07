<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\xml\asserters\nodes as testedClass
;

class nodes extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->isSubClassOf('mageekguy\atoum\asserter')
        ;
    }

    public function testSetWithInvalid()
    {
        $string = $this->realdom->regex('/[a-z]+/');
        $this
            ->given(
                $test = $this
            )
            ->if($asserter = new testedClass())
            ->then
                ->exception(function () use ($asserter, & $value, $test, $string) {
                        $asserter->setWith($value = $test->sample($string));
                })
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s is not a valid array of SimpleXMLElement', var_export($value, true)))
        ;
    }
}
