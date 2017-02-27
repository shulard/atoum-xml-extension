<?php
namespace mageekguy\atoum\xml;

use mageekguy\atoum;
use mageekguy\atoum\observable;
use mageekguy\atoum\runner;
use mageekguy\atoum\test;

class extension implements atoum\extension
{
    public function __construct(atoum\configurator $configurator = null)
    {
        if ($configurator) {
            $configurator
                ->getScript()
                ->getArgumentsParser()
                ->addHandler(function ($script, $argument, $values) {
                    $script
                        ->getRunner()
                        ->addTestsFromDirectory(dirname(__DIR__) . '/tests/units/classes');
                }, array('--test-ext'))
            ;
        }
    }

    public function setRunner(runner $runner)
    {
        return $this;
    }

    public function setTest(test $test)
    {
        $asserter = null;
        $test->getAssertionManager()
            ->setHandler(
                'xml',
                function ($xml = null, $depth = null, $options = null) use ($test, & $asserter) {
                    if ($asserter === null) {
                        $asserter = new atoum\xml\asserters\node($test->getAsserterGenerator());
                    }
                    if (null === $xml) {
                        throw new atoum\exceptions\logic("XML is undefined");
                    }

                    return $asserter
                        ->setWithTest($test)
                        ->setWith($xml, $depth, $options);
                }
            )
            ->setHandler(
                'html',
                function ($html = null, $depth = null, $options = null) use ($test, & $asserter) {
                    if ($asserter === null) {
                        $asserter = new atoum\xml\asserters\node($test->getAsserterGenerator());
                    }
                    if (null === $html) {
                        throw new atoum\exceptions\logic("HTML is undefined");
                    }

                    $internalErrors = libxml_use_internal_errors(true);
                    $disableEntities = libxml_disable_entity_loader(true);
                    $dom = new \DOMDocument('1.0', 'UTF-8');
                    $dom->validateOnParse = true;
                    @$dom->loadHTML($html);
                    libxml_use_internal_errors($internalErrors);
                    libxml_disable_entity_loader($disableEntities);

                    return $asserter
                        ->setWithTest($test)
                        ->setWith($dom, $depth, $options);
                }
            )
        ;
        return $this;
    }

    public function handleEvent($event, observable $observable)
    {
    }
}
