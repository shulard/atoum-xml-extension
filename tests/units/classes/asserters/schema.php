<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\xml\asserters\node as SUT;

class schema extends atoum\test
{
    public function test_class()
    {
        $this
            ->testedClass
                ->isSubClassOf('mageekguy\atoum\asserter')
        ;
    }

    public function test_dtd_validation()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node/>
    <node>
        <subnode />
    </node>
</root>
XML;

        $this
            ->given(
                $path = realpath(__DIR__.'/../../../resources/node.dtd'),
                $impl = new \DOMImplementation,
                $docType = $impl->createDocumentType('root', '', $path)
            )
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd($docType)
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd('file://'.$path, 'root')
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd($path, 'root')
        ;
    }

    public function test_schema_validation()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node/>
    <node>
        <subnode />
    </node>
</root>
XML;

        $this
            ->given(
                $path = realpath(__DIR__.'/../../../resources/node.xsd')
            )
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->schema($path)
        ;
    }

    public function test_relax_ng_validation()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <node/>
    <node>
        <subnode />
    </node>
</root>
XML;

        $this
            ->given(
                $path = realpath(__DIR__.'/../../../resources/node.rng')
            )
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->relaxNg($path)
        ;
    }
}
