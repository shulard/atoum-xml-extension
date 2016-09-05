<?php

namespace mageekguy\atoum\xml;

use mageekguy\atoum;

atoum\autoloader::get()
    ->addNamespaceAlias('atoum\xml', __NAMESPACE__)
    ->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes');
;
