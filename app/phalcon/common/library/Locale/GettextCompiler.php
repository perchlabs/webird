<?php
namespace Webird\Locale;

/**
 *
 */
class GettextCompiler
{

    /**
     * Class constructor.
     *
     */
    public function __construct()
    {
    }

    public function compileLocale($options)
    {
        $locale =  $options['locale'];
        $localeDir = $options['localeDir'];
        $cacheDir = $options['cacheDir'];

        $appMessagesDir   = "{$localeDir}{$locale}/LC_MESSAGES/";
        $cacheMessagesDir = "{$cacheDir}{$locale}/LC_MESSAGES/";

        exec("mkdir -p " . escapeshellarg($cacheMessagesDir));

        foreach ($options['domains'] as $domain) {
            $poPathEsc = escapeshellarg("{$appMessagesDir}{$domain}.po");
            $moPathEsc = escapeshellarg("{$cacheMessagesDir}{$domain}.mo");
            $cmd = "msgfmt -c -o $moPathEsc $poPathEsc";
            exec($cmd, $out, $ret);
        }
    }

}
