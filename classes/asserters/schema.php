<?php

namespace mageekguy\atoum\xml\asserters;

use mageekguy\atoum\asserter;
use mageekguy\atoum\exceptions;

class schema extends asserter
{
    protected $from;
    protected $data;

    public function setFrom(node $node)
    {
        $this->from = $node;
        return $this;
    }

    public function setWith($value)
    {
        parent::setWith($value);
        if (!is_string($value)) {
            $this->fail(sprintf($this->getLocale()->_('%s is not a valid string'), var_export($value, true)));
        }

        $this->data = new \DOMDocument;

        $this->failOnLibXmlErrors(function() use ($value) {
            return @$this->data->loadXML($value);
        }, 'Invalid XML string given');

        return $this;
    }

    public function __get($asserter)
    {
        switch (strtolower($asserter)) {
            case 'node':
                return $this->fromIsSet()->from;

            default:
                throw new exceptions\logic(sprintf($this->getLocale()->_('Invalid asserter name %s'), $asserter));
        }
    }

    protected function fromIsSet($message = 'Node source is undefined')
    {
        if ($this->from === null) {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function valueIsSet($message = 'XML document is undefined')
    {
        if ($this->data === null) {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    public function dtd($dtd, $failMessage = "Can't validate document using the given DTD")
    {
        $impl = new \DOMImplementation;
        $dom = null;
        if ($dtd instanceof \DOMDocumentType) {
            $dom = $impl->createDocument(null, null, $dtd);
        } elseif (false !== filter_var($dtd, FILTER_VALIDATE_URL) || is_file($dtd)) {
            if (func_num_args() < 2) {
                throw new exceptions\logic(
                    sprintf($this->getLocale()->_('You must give an URL + a root node name to valid with external DTD'))
                );
            }
            $args = func_get_args();
            $rootName = $args[1];
            $failMessage = isset($args[2])?$args[2]:"Can't validate document using the given DTD";

            if (is_file($dtd)) {
                $dtd = 'data://text/plain;base64,'.base64_encode(file_get_contents($dtd));
            }

            $docType = $impl->createDocumentType($rootName, null, $dtd);
            $dom = $impl->createDocument(
                null,
                null,
                $impl->createDocumentType($rootName, null, $dtd)
            );
        }

        if (null === $dom) {
            $dom = $this->valueIsSet()->data;
        } else {
            $dom->appendChild($dom->importNode($this->valueIsSet()->data->documentElement, true));
        }

        $this->failOnLibXmlErrors(function() use ($dom) {
            return @$dom->validate();
        }, $failMessage);

        return $this;
    }

    public function schema($schema, $failMessage = "Can't validate document using the given Schema")
    {
        if (!is_file($schema)) {
            throw new exceptions\logic(
                sprintf($this->getLocale()->_('Given schema is not a valid file : %s'), $schema)
            );
        }

        $this->failOnLibXmlErrors(function() use ($schema) {
            return @$this->valueIsSet()->data->schemaValidate($schema);
        }, $failMessage);

        return $this;
    }

    public function relaxNg($rng, $failMessage = "Can't validate document using the given Schema")
    {
        if (!is_file($rng)) {
            throw new exceptions\logic(
                sprintf($this->getLocale()->_('Given schema is not a valid file : %s'), $rng)
            );
        }

        $this->failOnLibXmlErrors(function() use ($rng) {
            return @$this->valueIsSet()->data->relaxNGValidate($rng);
        }, $failMessage);

        return $this;
    }

    protected function failOnLibXmlErrors(callable $callable, $failMessage)
    {
        $useError = libxml_use_internal_errors(true);

        if(true === call_user_func($callable)) {
            $this->pass();
        } else {
            $message = $this->getLocale()->_($failMessage);
            foreach (libxml_get_errors() as $error) {
                $message .= PHP_EOL . sprintf('[%d] at line %s: %s', $error->level, $error->line, $error->message);
            }

            $this->fail($message);
        }

        libxml_use_internal_errors($useError);
    }
}
