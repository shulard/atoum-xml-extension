<?php

namespace mageekguy\atoum\xml\asserters;

use mageekguy\atoum\asserter;
use mageekguy\atoum\asserters;
use mageekguy\atoum\exceptions;

class xml extends asserters\phpString
{
    protected $data;

    public function setWith($value, $label = null, $charlist = null, $checkType = true)
    {
        parent::setWith($value, $label, $charlist, $checkType);

        if (false === $this->data = @simplexml_load_string($value))
        {
            $this->fail(sprintf($this->getLocale()->_('%s is not a valid XML string'), $this));
        }

        return $this;
    }
}
