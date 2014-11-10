<?php
namespace Webird\Locale;

use Phalcon\DI\Injectable as DIInjectable;

/**
 *
 */
class Locale extends DIInjectable
{
    private $locale,
            $default,
            $map,
            $supported;


    /**
     * Class constructor.
     *
     */
    public function __construct($di, $default, $supported, $map)
    {
        $this->setDI($di);
        $this->default = $default;
        $this->supported = $supported;
        $this->map = $map;
    }

    public function getSupportedLocales()
    {
        return $this->supported;
    }



    public function isLocaleSupported($locale)
    {
        $supported = $this->getSupportedLocales();
        return (array_key_exists($locale, $supported));
    }



    public function getMap($language)
    {


    }



    public function getBestLocale()
    {
        if (isset($this->locale)) {
            return $this->locale;
        }

        if (php_sapi_name() === 'cli') {
            $localeRaw = $this->default;
        } else {
            // Get the locale from the request headers
            $localeRaw = $this->getDI()->getRequest()->getBestLanguage();
            $localeRaw = str_replace('-', '_', $localeRaw);
        }

        if (strpos($localeRaw, '..') !== false) {
            throw new Exception('Locale has dangerous characters');
        }

        $localeParts = explode('_', $localeRaw);
        $language = $localeParts[0];
        $country = (count($localeParts) > 1) ? '_' . strtoupper($localeParts[1]) : '';

        if ($this->isLocaleSupported("{$language}{$country}")) {
            $locale = "{$language}{$country}";
        } else if (array_key_exists($language, $this->map)) {
            $locale = $this->map[$language];
        } else {
            $locale = $this->localeDefault;
        }

        $this->locale = $locale;

        return $this->locale;
    }


}
