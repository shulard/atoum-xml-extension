<?php

namespace atoum\atoum\xml;

if (class_exists("\atoum\atoum\autoloader")) {
    $classname = "\atoum\atoum\autoloader";
} else {
    $classname = "\mageekguy\atoum\autoloader";
}

$autoloader = call_user_func($classname . '::get')
    ->addNamespaceAlias('atoum\xml', __NAMESPACE__)
    ->addDirectory(__NAMESPACE__, __DIR__ . DIRECTORY_SEPARATOR . 'classes');
;
