<?php

namespace mageekguy\atoum\xml\tests\units;

use mageekguy\atoum;
use mageekguy\atoum\xml\extension as SUT;

class extension extends atoum\test
{
    public function test_class()
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
                ->object($extension = new SUT())
            ->if($this->resetMock($parser))
            ->if($extension = new SUT($configurator))
            ->then
                ->mock($parser)
                    ->call('addHandler')->once()
        ;
    }

    public function test_set_runner()
    {
        $this
            ->if($extension = new SUT())
            ->and($runner = new atoum\runner())
            ->then
                ->object($extension->setRunner($runner))->isIdenticalTo($extension)
        ;
    }

    public function test_set_handler()
    {
        $this
            ->if($extension = new SUT())
            ->and($test = new \mock\mageekguy\atoum\test())
            ->and($manager = new \mock\mageekguy\atoum\test\assertion\manager())
            ->and($test->setAssertionManager($manager))
            ->then
                ->object($extension->setTest($test))->isIdenticalTo($extension)
                ->mock($manager)
                    ->call('setHandler')->withArguments('xml')->once()
        ;
    }

    public function test_xml_asserter_with_value()
    {
        $asserter = $this->then->xml('<?xml version="1.0" ?><root></root>');
        $this
            ->then
                ->object($asserter)
                    ->isInstanceOf('mageekguy\atoum\xml\asserters\node')
        ;
    }

    public function test_xml_asserter_without_value()
    {
        $this
            ->then
                ->exception(function () {
                    $this->then->xml();
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('XML is undefined')
        ;
    }

    public function test_html_asserter_with_value()
    {
        $asserter = $this->then->html('<html />');
        $this
            ->then
                ->object($asserter)
                    ->isInstanceOf('mageekguy\atoum\xml\asserters\node')
        ;
    }

    public function test_html_asserter_without_value()
    {
        $this
            ->then
                ->exception(function () {
                    $this->then->html();
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('HTML is undefined')
        ;
    }
}
