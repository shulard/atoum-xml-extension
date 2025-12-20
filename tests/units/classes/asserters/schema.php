<?php

namespace mageekguy\atoum\xml\tests\units\asserters;

use mageekguy\atoum;
use mageekguy\atoum\xml\asserters\schema as SUT;

class schema extends \atoum\atoum\test
{
    public function test_class()
    {
        $this
            ->testedClass
                ->isSubClassOf('atoum\atoum\asserter')
        ;
    }

    public function test_set_with_invalid_parameter()
    {
        $this
            ->given(
                $asserter = new SUT(),
                $value = []
            )
            ->then
                ->exception(function () use ($asserter, $value) {
                    $asserter->setWith($value);
                })
                    ->isInstanceOf('atoum\atoum\asserter\exception')
                    ->hasMessage(sprintf('%s is not a valid string', var_export($value, true)))
                ;
    }

    public function test_set_with_invalid_xml()
    {
        $string = "invalid_xml";
        $this
            ->given(
                $test = $this,
                $asserter = new SUT(),
                $value = []
            )
            ->then
                ->exception(function () use ($asserter, &$value, $string, $test) {
                    $asserter->setWith($string);
                })
                    ->isInstanceOf('atoum\atoum\asserter\exception')
                    ->message
                        ->contains('Invalid XML string given')
                ;
    }

    public function test_missing_value()
    {
        $this
            ->given(
                $asserter = new SUT()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->dtd('');
                })
                    ->isInstanceOf('atoum\atoum\exceptions\logic')
                    ->hasMessage('XML document is undefined')
                ;
    }

    public function test_dtd_validation_from_uri_without_rootname()
    {
        $this
            ->given(
                $asserter = new SUT()
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->dtd('file://mypath/to.dtd');
                })
                    ->isInstanceOf('atoum\atoum\exceptions\logic')
                    ->hasMessage('You must give an URL + a root node name to valid with external DTD')
                ;
    }

    public function test_dtd_validation_from_invalid_dtd()
    {
        $this
            ->given(
                $asserter = new SUT(),
                $asserter->setWith('<?xml version="1.0"?><root></root>')
            )
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->dtd('file://mypath/to.dtd', 'root');
                })
                    ->isInstanceOf('atoum\atoum\asserter\exception')
                    ->message
                        ->contains('Can\'t validate document using the given DTD')
                ;
    }

    public function test_dtd_validation_from_path()
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
                $path = realpath(__DIR__.'/../../../resources/node.dtd')
            )
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd($path, 'root')
        ;
    }

    public function test_dtd_validation_from_domimpementation()
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
        ;
    }

    public function test_dtd_validation_from_uri()
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
                $path = realpath(__DIR__.'/../../../resources/node.dtd')
            )
            ->then
                ->xml($xml)
                    ->isValidAgainstSchema
                        ->dtd('file://'.$path, 'root')
        ;
    }

    public function test_schema_validation_from_invalid_path()
    {
        $string = "test_invalid_path";
        $this
            ->given(
                $asserter = new SUT(),
                $asserter->setWith('<?xml version="1.0"?><root></root>')
            )
            ->then
                ->exception(function () use ($asserter, &$value, $string) {
                    $asserter->schema($string);
                })
                    ->isInstanceOf('atoum\atoum\exceptions\logic')
                    ->message
                        ->contains('Given schema is not a valid file')
                ;
    }

    public function test_schema_validation_from_path()
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

    public function test_relax_ng_validation_from_invalid_path()
    {
        $string = "invalid_path_test";
        $this
            ->given(
                $test = $this,
                $asserter = new SUT(),
                $asserter->setWith('<?xml version="1.0"?><root></root>')
            )
            ->then
                ->exception(function () use ($asserter, &$value, $string, $test) {
                    $asserter->relaxNg($string);
                })
                    ->isInstanceOf('atoum\atoum\exceptions\logic')
                    ->message
                        ->contains('Given schema is not a valid file')
                ;
    }

    public function test_relax_ng_validation_from_path()
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
