<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\xml\asserters\nodes as SUT;

class nodes extends atoum\test
{
    public function test_class()
    {
        $this
            ->testedClass
                ->isSubClassOf('mageekguy\atoum\asserter')
        ;
    }

    public function test_set_with_invalid()
    {
        $string = "a_string_that_is_not_a_valid_xml";
        $this
            ->if($asserter = new SUT())
            ->then
                ->exception(function () use ($asserter, &$value, $string) {
                        $asserter->setWith($string);
                })
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s is not a valid array or SimpleXMLElement', var_export($string, true)))
            ;
    }

    public function test_set_with_array_mixed()
    {
        $string = "an_invalid_string";
        $this
            ->if($asserter = new SUT())
            ->then
                ->exception(function () use ($asserter, &$value, $string) {
                        $asserter->setWith([$string]);
                })
                    ->isInstanceOf('mageekguy\atoum\asserter\exception')
                    ->hasMessage(sprintf(
                        '%s Collection does not only contains SimpleXMLElement',
                        var_export([$string], true)
                    ))
            ;
    }

    public function test_set_with_array()
    {
        $this
            ->given(
                $xml = new \SimpleXmlElement('<?xml version="1.0"?><root></root>'),
                $asserter = new SUT(),
                $asserter->setWith([$xml])
            )
            ->when($result = $asserter->first()->nodename->isEqualTo('root'))
            ->then
                ->object($result)
                    ->isInstanceOf('mageekguy\atoum\asserters\phpString')
            ->when($result = $asserter->size->isEqualTo(1))
            ->then
                ->object($result)
                    ->isInstanceOf('mageekguy\atoum\asserters\integer')
        ;
    }

    public function test_set_with_simplexmlelement()
    {
        $this
            ->given(
                $xml = new \SimpleXmlElement('<?xml version="1.0"?><root><node /></root>'),
                $asserter = new SUT(),
                $asserter->setWith($xml)
            )
            ->when($result = $asserter->first()->nodename->isEqualTo('node'))
            ->then
                ->object($result)
                    ->isInstanceOf('mageekguy\atoum\asserters\phpString')
            ->when($result = $asserter->size->isEqualTo(1))
            ->then
                ->object($result)
                    ->isInstanceOf('mageekguy\atoum\asserters\integer')
        ;
    }

    public function test_no_value_given()
    {
        $this
            ->if($asserter = new SUT())
            ->then
                ->exception(function () use ($asserter) {
                        $asserter->size;
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('Node collection is undefined')
            ;
    }

    public function test_invalid_assert()
    {
        $this
            ->if($asserter = new SUT())
            ->then
                ->exception(function () use ($asserter) {
                        $asserter->nope;
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('Invalid asserter name nope')
            ;
    }

    public function test_invalid_item_position()
    {
        $this
            ->given(
                $xml = new \SimpleXMLElement('<?xml version="1.0"?><root></root>'),
                $asserter = new SUT(),
                $asserter->setWith([$xml])
            )
            ->then
                ->exception(function () use ($asserter) {
                        $asserter->item(2);
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('No item at position 2')
                ;
    }

    public function test_no_parent()
    {
        $this
            ->if($asserter = new SUT())
            ->then
                ->exception(function () use ($asserter) {
                        $asserter->parent;
                })
                    ->isInstanceOf('mageekguy\atoum\exceptions\logic')
                    ->hasMessage('Node source is undefined')
            ;
    }

    public function test_parent_asserter()
    {
        $this
            ->given(
                $asserter = new atoum\xml\asserters\node,
                $asserter->setWith('<?xml version="1.0"?><root><node /><node /></root>')
            )
            ->when($parent = $asserter->children->parent)
            ->then
                ->object($parent)
                    ->isIdenticalTo($asserter)
        ;
    }
}
