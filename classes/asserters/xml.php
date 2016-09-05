<?php

namespace mageekguy\atoum\xml\asserters;

use mageekguy\atoum\asserter;

class xml extends asserter
{
    protected $data;

    public function setWith($value, $label = null, $charlist = null, $checkType = true)
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
                $asserter = new self();
                return $asserter->setWith($this->data->children());

            case 'size':
                return $this->generator->__call('integer', array(count($this->data)));

            default:
                $asserter = new self();
                return $asserter->setWith($this->data->children($asserter));
        }
    }
}
