<?php

/*
 * Pass in a volt compiler or instantiate a fresh compiler in the requiring code.
 * This allows the compiler to be used on the CLI.
 */

if (!isset($compiler)) {
    throw \Exception('The compiler must be set in the current scope to have it configured');
}

/*
 *
 */
$compiler->addFunction('round', 'round');

/*
 *
 */
$compiler->addFunction('number_format', 'number_format');

/*
 *
 */
$compiler->addFunction('print_r', function($val) {
    return 'print_r(' . $val . ', true)';
});

/*
 *
 */
$compiler->addFunction('varexport', function($val) {
    return 'var_export(' . $val . ', true)';
});

/*
 *
 */
$compiler->addFunction('common', function($partial) {
    return '$this->partial(\'../../../../common/views/partials/\' . ' . $partial . ')';
});

/*
 *
 */
$compiler->addFunction('t', function ($resolvedArgs, $exprArgs) {
    if (count($exprArgs) === 1) {
        $str = sprintf('$this->translate->gettext(\'%s\')', $exprArgs[0]['expr']['value']);
    } else {
        $str = 'Volt Compiler Error for translation function.';
        error_log('Volt Compiler Error for translation function.');
    }

    return $str;
});

/*
 *
 */
$compiler->addFunction('n', function ($resolvedArgs, $exprArgs) {
    if (count($exprArgs) === 3) {
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
