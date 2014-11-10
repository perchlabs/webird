<?php
namespace Webird\Locale;

/**
 *
 */
class Compiler
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
        $localeCacheDir = $options['localeCacheDir'];

        $appMessagesDir   = "{$localeDir}{$locale}/LC_MESSAGES/";
        $cacheMessagesDir = "{$localeCacheDir}{$locale}/LC_MESSAGES/";

        exec("mkdir -p " . escapeshellarg($cacheMessagesDir));

        foreach ($options['domains'] as $domain) {
            $poPathEsc = escapeshellarg("{$appMessagesDir}{$domain}.po");
            $moPathEsc = escapeshellarg("{$cacheMessagesDir}{$domain}.mo");
            $cmd = "msgfmt -c -o $moPathEsc $poPathEsc";
            exec($cmd, $out, $ret);
        }
    }

}
