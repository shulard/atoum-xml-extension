<?php

namespace mageekguy\atoum\xml\asserters;

use
    mageekguy\atoum\asserter,
    mageekguy\atoum\exceptions
;

class node extends asserter
{
    protected $data;
    protected $isSet = false;

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
        $this->isSet = true;

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

            case 'attributes':
                return $this->generator->__call('phpArray', array($this->valueIsSet()->data['@attributes']));

            default:
                return $this->getNodesAsserter($this->valueIsSet()->data->children($asserter));
        }
    }

    public function xpath($path)
    {
        return $this->getNodesAsserter($this->valueIsSet()->data->xpath($path));
    }

    public function withNamespace($prefix, $uri)
    {
        $this->valueIsSet()->data->registerXPathNamespace($prefix, $uri);

        return $this;
    }

    public function hasNamespace($prefix, $uri)
    {
        $ns = $this->valueIsSet()->data->getNamespaces();

        if(isset($ns[$prefix]) && $ns[$prefix] === $uri)
        {
            $this->pass();
        }
        else
        {
            $this->fail(sprintf($this->getLocale()->_('%s namespace does not exists with URI: %s'), $prefix, $uri));
        }

        return $this;
    }

    protected function valueIsSet($message = 'Xml is undefined')
    {
        if ($this->isSet === false)
        {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function getNodesAsserter($data)
    {
        return $this->generator->__call('mageekguy\atoum\xml\asserters\nodes', array($data));
    }
}
