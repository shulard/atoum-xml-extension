<?php

namespace mageekguy\atoum\xml\asserters;

use
    mageekguy\atoum\asserter,
    mageekguy\atoum\exceptions
;

class nodes extends asserter
{
    protected $data;
    protected $isSet = false;

    public function setWith($value)
    {
        parent::setWith($value);
        if (is_array($value) === true || $value instanceof \SimpleXMLElement)
        {
            $count = is_array($value) === true?count($value):$value->count();
            $filtered = array();
            foreach($value as $node) {
                if($node instanceof \SimpleXMLElement) {
                    $filtered[] = $node;
                }
            }

            if (count($filtered) === $count) {
                $this->data = $value;
                $this->isSet = true;
                return $this;
            }
        }

        $this->fail(sprintf($this->getLocale()->_('%s is not a valid array of SimpleXMLElement'), var_export($value, true)));
    }

    public function __get($asserter)
    {
        switch (strtolower($asserter))
        {
            case 'size':
                return $this->generator->__call('integer', array(count($this->valueIsSet()->data)));

            default:
                throw new exceptions\logic(sprintf($this->getLocale()->_('Invalid asserter name %s'), $asserter));
        }
    }

    protected function valueIsSet($message = 'Node collection is undefined')
    {
        if ($this->isSet === false)
        {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    public function first()
    {
        return $this->item(0);
    }

    public function last()
    {
        return $this->item(count($this->valueIsSet()->data)-1);
    }

    public function item($position)
    {
        if (!isset($this->valueIsSet()->data[$position])) {
            throw new exceptions\logic(sprintf($this->getLocale()->_('No item at position %s'), $position));
        }

        $asserter = new node($this->getGenerator(), $this->getAnalyzer(), $this->getLocale());
        return $asserter->setWith($this->data[$position]);
    }

    public function hasSize($size, $failMessage = null)
    {
        if (count($this->valueIsSet()->data) == $size)
        {
            $this->pass();
        }
        else
        {
            $this->fail($failMessage ?: $this->_('%s has size %d, expected size %d', $this, count($this->valueIsSet()->data), $size));
        }

        return $this;
    }
}
