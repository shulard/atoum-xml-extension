<?php

namespace mageekguy\atoum\xml\asserters;

use atoum\atoum\asserter;
use atoum\atoum\exceptions;

class nodes extends asserter
{
    protected $from;
    protected $data;
    protected $isSet = false;

    public function setFrom(node $node)
    {
        $this->from = $node;
        return $this;
    }

    public function setWith($value)
    {
        parent::setWith($value);
        if ($value instanceof \SimpleXMLElement) {
            $count = $value->count();
            $collection = $value->children();
        } elseif (is_array($value) === true) {
            $count = count($value);
            $collection = $value;
        } else {
            $this->fail(sprintf(
                $this->getLocale()->_('%s is not a valid array or SimpleXMLElement'),
                var_export($value, true)
            ));
        }

        if (isset($count)) {
            $filtered = array();
        }
        foreach ($collection as $node) {
            if ($node instanceof \SimpleXMLElement) {
                $filtered[] = $node;
            }
        }

        if (count($filtered) !== $count) {
            $this->fail(sprintf(
                $this->getLocale()->_('%s Collection does not only contains SimpleXMLElement'),
                var_export($value, true)
            ));
        }

        $this->data = $filtered;
        $this->isSet = true;

        return $this;
    }

    public function __get($asserter)
    {
        switch (strtolower($asserter)) {
            case 'size':
                return $this->generator->__call('integer', array(count($this->valueIsSet()->data)));

            case 'parent':
                return $this->parent();

            default:
                throw new exceptions\logic(sprintf($this->getLocale()->_('Invalid asserter name %s'), $asserter));
        }
    }

    protected function valueIsSet($message = 'Node collection is undefined')
    {
        if ($this->isSet === false) {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function fromIsSet($message = 'Node source is undefined')
    {
        if ($this->from === null) {
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

    public function hasSize($size, $failMessage = '%s has size %d, expected size %d')
    {
        if (count($this->valueIsSet()->data) == $size) {
            $this->pass();
        } else {
            $this->fail(
                $this->_($failMessage, get_class($this), count($this->valueIsSet()->data), $size)
            );
        }

        return $this;
    }

    public function parent()
    {
        return $this->fromIsSet()->from;
    }
}
