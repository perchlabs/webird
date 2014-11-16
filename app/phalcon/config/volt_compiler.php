<?php

/*
 * Pass in the voltService compiler or instantiate a fresh compiler in the requiring code.
 * This allows the compiler to be used on the CLI.
*/

if (!isset($compiler)) {
    throw \Exception('The compiler must be set in the current scope to have it configured');
}

$compiler->addFunction('round', 'round');
$compiler->addFunction('number_format', 'number_format');

$compiler->addFunction('print_r', function($val) {
    return 'print_r(' . $val . ', true)';
});

$compiler->addFunction('varexport', function($val) {
    return 'var_export(' . $val . ', true)';
});


// Support angular since '{{' and '}}' conflict between Volt and Angular
$compiler->addFunction('ng', function($input) {
    return '"{{".' . $input . '."}}"';
});


$compiler->addFunction('_', function ($resolvedArgs, $exprArgs) {
    $count = count($exprArgs);
    if ($count === 1) {
        $str = sprintf('$this->translate->gettext(\'%s\')', $exprArgs[0]['expr']['value']);
    } else if ($count === 3) {
        $val1 = $exprArgs[0]['expr']['value'];
        $val2 = $exprArgs[1]['expr']['value'];
        $val3 = $exprArgs[2]['expr']['value'];
        $str = sprintf('$this->translate->ngettext(\'%s\', \'%s\', %d)', $val1, $val2, $val3);
    } else {
        $str = 'Volt Compiler Error for translation function.';
        error_log('Volt Compiler Error for translation function.');
    }

    return $str;
});





// FIXME: Support for this is waiting on issue #2651 https://github.com/phalcon/cphalcon/issues/2651

// $compiler->addFilter('tr', function($resolvedArgs, $exprArgs) {
//     error_log('resolvedArgs: ' . var_export($resolvedArgs, true));
//     error_log('exprArgs: ' . var_export($exprArgs, true));
//
//
//     if (is_string($resolvedArgs)) {
//         error_log('resolvedArgs is_string: ' . var_export($resolvedArgs, true));
//
//         $str = sprintf('$this->translate->gettext(\'%s\')', $resolvedArgs);
//         // $str = '$this->translate->gettext(' . $resolvedArgs . ')';
//     } else if (is_array($resolveArgs)) {
//         error_log('resolvedArgs is_array: ' . var_export($resolvedArgs, true));
//
//         $count = count($exprArgs);
//         if ($count === 1) {
//             $val1 = $resolvedArgs[0];
//             $str = sprintf('$this->translate->gettext(\'%s\')', $val1);
//         } else if ($count === 3) {
//             $val1 = $resolvedArgs[0];
//             $val2 = $resolvedArgs[1];
//             $val3 = $resolvedArgs[2];
//             $str = sprintf('$this->translate->ngettext(\'%s\', \'%s\', %d)', $val1, $val2, $val3);
//         } else {
//             $str = 'Volt Compiler Error for translation function.';
//             error_log('Volt Compiler Error for translation function.');
//         }
//
//     } else {
//         $str = 'Volt Compiler Error for translation filter.';
//         error_log('Volt Compiler Error for translation filter.  Invalid .');
//     }
//
//     $str = '$this->translate->gettext(' . $resolvedArgs . ')';
//
//     return $str;
// });
