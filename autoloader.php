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
$classAliases = [
    'atoum\atoum\runner' => 'mageekguy\atoum\runner',
    'atoum\atoum\test' => 'mageekguy\atoum\test',
    'atoum\atoum\test\assertion\manager' => 'mageekguy\atoum\test\assertion\manager',
    'atoum\atoum\configurator' => 'mageekguy\atoum\configurator',
    'atoum\atoum\scripts\runner' => 'mageekguy\atoum\scripts\runner',
    'atoum\atoum\script\arguments\parser' => 'mageekguy\atoum\script\arguments\parser',
    'atoum\atoum\asserter' => 'mageekguy\atoum\asserter',
    'atoum\atoum\asserter\exception' =>  'mageekguy\atoum\asserter\exception',
    'atoum\atoum\asserters\phpString' => 'mageekguy\atoum\asserters\phpString',
    'atoum\atoum\asserters\integer' => 'mageekguy\atoum\asserters\integer',
    'atoum\atoum\exceptions\logic' => 'mageekguy\atoum\exceptions\logic',
];

foreach ($classAliases as $alias => $original) {
    if (!class_exists($alias)) {
        class_alias($original, $alias);
    }
}

$interfaceAliases = [
    'atoum\atoum\extension' => 'mageekguy\atoum\extension',
    'atoum\atoum\observable' => 'mageekguy\atoum\observable',
    'atoum\atoum\exception' => 'mageekguy\atoum\exception',
];

foreach ($interfaceAliases as $alias => $original) {
    if (!interface_exists($alias)) {
        class_alias($original, $alias);
    }
}
