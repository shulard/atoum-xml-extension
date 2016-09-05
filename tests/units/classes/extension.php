<?php

namespace mageekguy\atoum\xml\tests\units;

use
    mageekguy\atoum,
    mageekguy\atoum\xml\extension as testedClass
;

class extension extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->hasInterface('mageekguy\atoum\extension')
        ;
    }

    public function test__construct()
    {
        $this
            ->if($script = new atoum\scripts\runner(uniqid()))
            ->and($script->setArgumentsParser($parser = new \mock\mageekguy\atoum\script\arguments\parser()))
        ;

        $configurator = new \mock\mageekguy\atoum\configurator($script);
        $this
            ->then
                ->object($extension = new testedClass())
            ->if($this->resetMock($parser))
            ->if($extension = new testedClass($configurator))
            ->then
                ->mock($parser)
                    ->call('addHandler')->twice()
        ;
    }

    public function testSetRunner()
    {
        $this
            ->if($extension = new testedClass())
            ->and($runner = new atoum\runner())
            ->then
                ->object($extension->setRunner($runner))->isIdenticalTo($extension)
        ;
    }

    public function testSetTest()
    {
        $this
            ->if($extension = new testedClass())
            ->and($test = new \mock\mageekguy\atoum\test())
            ->and($manager = new \mock\mageekguy\atoum\test\assertion\manager())
            ->and($test->setAssertionManager($manager))
            ->then
                ->object($extension->setTest($test))->isIdenticalTo($extension)
                ->mock($manager)
                    ->call('setHandler')->withArguments('xml')->once()
        ;

        $faker = $test->xml('<?xml version="1.0" ?><root></root>');
        $this->object($faker)->isInstanceOf('mageekguy\atoum\xml\asserters\xml');
    }
}
