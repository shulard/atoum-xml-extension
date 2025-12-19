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

// cela permet de conserver le support des premières versions d'atoum
$aliases = [
    'atoum\atoum\extension' => 'mageekguy\atoum\extension',
    'atoum\atoum\runner' => 'mageekguy\atoum\runner',
    'atoum\atoum\test' => 'mageekguy\atoum\test',
    'atoum\atoum\observable' => 'mageekguy\atoum\observable',
    'atoum\atoum\configurator' => 'mageekguy\atoum\configurator',
    'atoum\atoum\asserter' => 'mageekguy\atoum\asserter',
    'atoum\atoum\exception' => 'mageekguy\atoum\exception',
    'atoum\atoum\exceptions\logic' => 'mageekguy\atoum\exceptions\logic',
];

foreach ($aliases as $alias => $original) {
    if (!class_exists($alias)) {
        class_alias($original, $alias);
    }
}
