<?php

namespace mageekguy\atoum\xml\asserters;

use
    mageekguy\atoum\asserter,
    mageekguy\atoum\exceptions
;

class node extends asserter
{
    protected $data;

    public function setWith($value)
    {
        parent::setWith($value);
        if ($value instanceof \SimpleXMLElement)
        {
            $this->data = $value;
        }
        elseif (false === $this->data = @simplexml_load_string($value))
        {
            $this->fail(sprintf($this->getLocale()->_('%s is not a valid XML'), $value));
        }

        return $this;
    }

    public function __get($asserter)
    {
        switch (strtolower($asserter))
        {
            case 'children':
                return $this->getNodesAsserter($this->valueIsSet()->data->children());

            case 'size':
                return $this->generator->__call('integer', array($this->valueIsSet()->data->count()));

            case 'nodevalue':
                return $this->generator->__call('phpString', array((string)$this->valueIsSet()->data));

            case 'nodename':
                return $this->generator->__call('phpString', array((string)$this->valueIsSet()->data->getName()));

            case 'xml':
                return $this->generator->__call('phpString', array($this->valueIsSet()->data->asXML()));

            default:
                return $this->getNodesAsserter($this->valueIsSet()->data->children($asserter));
        }
    }

    public function xpath($path)
    {
        return $this->getNodesAsserter($this->valueIsSet()->data->xpath($path));
    }

    public function attributes($namespace = '')
    {
        $attributes = (array)$this->valueIsSet()->data->attributes($namespace, filter_var($namespace, FILTER_VALIDATE_URL)===false);
        return $this->generator->__call('phpArray', array(isset($attributes['@attributes'])?$attributes['@attributes']:array()));
    }

    public function withNamespace($prefix, $uri)
    {
        $this->valueIsSet()->data->registerXPathNamespace($prefix, $uri);

        return $this;
    }

    public function hasNamespace($prefix, $uri, $failMessage = '%s document namespace does not exists with URI: %s')
    {
        $ns = $this->valueIsSet()->data->getDocNamespaces();
        return $this->checkNamespace($prefix, $uri, $ns, $failMessage);
    }

    public function isUsedNamespace($prefix, $uri, $failMessage = '%s namespace does not exists with URI: %s')
    {
        $ns = $this->valueIsSet()->data->getNamespaces(true);
        return $this->checkNamespace($prefix, $uri, $ns, $failMessage);
    }

    protected function checkNamespace($prefix, $uri, $ns, $failMessage)
    {
        if(isset($ns[$prefix]) && $ns[$prefix] === $uri)
        {
            $this->pass();
        }
        else
        {
            $this->fail(sprintf($this->getLocale()->_($failMessage), $prefix, $uri));
        }

        return $this;
    }

    public function validateWithDtd($dtd = null, $failMessage = "Can't validate document using the given DTD")
    {
        $xml = $this->valueIsSet()->data->asXML();
        $impl = new \DOMImplementation;
        $dom = null;
        if($dtd instanceof \DOMDocumentType) {
            $dom = $impl->createDocument(null, null, $dtd);
        } elseif(false !== filter_var($dtd, FILTER_VALIDATE_URL) || is_file($dtd)) {
            if(func_num_args() < 2) {
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
            if(false === $docType) {
                throw new exceptions\logic(
                    sprintf($this->getLocale()->_('Can\'t build a DOMDocumentType using given data: %s:%s'), $rootName, $dtd)
                );
            }
            $dom = $impl->createDocument(
                null, null,
                $impl->createDocumentType($rootName, null, $dtd)
            );
        }

        if (null === $dom) {
            $dom = new \DOMDocument;
            $dom->loadXML($xml);
        } else {
            $tmp = new \DOMDocument;
            $tmp->loadXML($xml);

            $dom->appendChild($dom->importNode($tmp->documentElement, true));
        }

        $useError = libxml_use_internal_errors(true);
        if(@$dom->validate())
        {
            $this->pass();
        }
        else
        {
            $message = $this->getLocale()->_($failMessage);
            foreach(libxml_get_errors() as $error) {
                $message .= PHP_EOL . sprintf('[%d] at line %s: %s', $error->level, $error->line, $error->message);
            }

            $this->fail($message);
        }
        libxml_use_internal_errors($useError);

        return $this;
    }

    public function validateWithSchema($schema = null, $failMessage = "Can't validate document using the given Schema")
    {
        $xml = $this->valueIsSet()->data->asXML();
        $dom = new \DOMDocument;
        $dom->loadXML($xml);

        if(!is_file($schema)) {
            throw new exceptions\logic(
                sprintf($this->getLocale()->_('Given schema is not a valid file : %s'), $schema)
            );
        }

        $useError = libxml_use_internal_errors(true);
        if(@$dom->schemaValidate($schema))
        {
            $this->pass();
        }
        else
        {
            $message = $this->getLocale()->_($failMessage);
            foreach(libxml_get_errors() as $error) {
                $message .= PHP_EOL . sprintf('[%d] at line %s: %s', $error->level, $error->line, $error->message);
            }

            $this->fail($message);
        }
        libxml_use_internal_errors($useError);

        return $this;
    }

    protected function valueIsSet($message = 'Xml is undefined')
    {
        if ($this->data === null)
        {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function getNodesAsserter($data)
    {
        $asserter = $this->generator->__call('mageekguy\atoum\xml\asserters\nodes', array($data));
        $asserter->setFrom($this);
        return $asserter;
    }
}
