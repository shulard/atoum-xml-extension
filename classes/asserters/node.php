<?php

namespace mageekguy\atoum\xml\asserters;

use mageekguy\atoum\asserter;
use mageekguy\atoum\exceptions;

class node extends asserter
{
    protected $data;

    public function setWith($value)
    {
        parent::setWith($value);
        if ($value instanceof \SimpleXMLElement) {
            $this->data = $value;
        } elseif (false === $this->data = @simplexml_load_string($value)) {
            $this->fail(sprintf($this->getLocale()->_('%s is not a valid XML'), $value));
        }

        return $this;
    }

    public function __get($asserter)
    {
        switch (strtolower($asserter)) {
            case 'children':
                return $this->getNodesAsserter((array)$this->valueIsSet()->data->xpath('./*'));

            case 'size':
                return $this->generator->__call('integer', array($this->valueIsSet()->data->count()));

            case 'nodevalue':
                return $this->generator->__call('phpString', array((string)$this->valueIsSet()->data));

            case 'nodename':
                return $this->generator->__call('phpString', array((string)$this->valueIsSet()->data->getName()));

            case 'xml':
                return $this->generator->__call('phpString', array($this->valueIsSet()->data->asXML()));

            case 'isvalidagainstschema':
            case 'validate':
                return $this->generator
                    ->__call('mageekguy\atoum\xml\asserters\schema', array($this->valueIsSet()->data->asXML()))
                    ->setFrom($this);

            default:
                return $this->getNodesAsserter($this->valueIsSet()->data->xpath('./'.$asserter));
        }
    }

    public function xpath($path)
    {
        return $this->getNodesAsserter($this->valueIsSet()->data->xpath($path));
    }

    public function attributes($namespace = '')
    {
        $attributes = (array)$this->valueIsSet()->data
            ->attributes($namespace, filter_var($namespace, FILTER_VALIDATE_URL)===false);
        return $this->generator->__call(
            'phpArray',
            array(isset($attributes['@attributes'])?$attributes['@attributes']:array())
        );
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
        if (isset($ns[$prefix]) && $ns[$prefix] === $uri) {
            $this->pass();
        } else {
            $this->fail(sprintf($this->getLocale()->_($failMessage), $prefix, $uri));
        }

        return $this;
    }

    protected function valueIsSet($message = 'Xml is undefined')
    {
        if ($this->data === null) {
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
