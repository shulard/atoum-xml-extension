<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use
    mageekguy\atoum,
    mageekguy\atoum\xml\asserters\nodes as testedClass
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
}
