<?php
namespace Webird\Locale;

use Webird\Locale\Compiler;

/**
 * Wrapper class for gettext translations
 */
class Gettext
{

    /**
     * @var array
     */
    protected $domains = [];

    /**
     * @var string
     */
    protected $defaultDomain;

    /**
     * Class constructor.
     *
     * @param array $options Required options:
     *                       (string) locale
     *                       (array)  domains where keys are domain names
     *
     * @throws \Webird\Locale\TranslateException
     */
    public function __construct()
    {
    }

    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new \Exception('Invalid options');
        }
        if (!isset($options['locale'])) {
            throw new \Exception('Parameter "locale" is required');
        }
        if (strpos($options['locale'], '..') !== false) {
            throw new \Exception('Locale has dangerous characters');
        }

        if (!isset($options['domains'])) {
            throw new \Exception('domains must be specified and it must be an array.');
        }

        if (isset($options['compileAlways']) && $options['compileAlways'] === true) {
            $compiler = new Compiler();
            $compiler->compileLocale([
                'locale'          => $options['locale'],
                'domains'         => $options['domains'],
                'localeDir'       => $options['localeDir'],
                'localeCacheDir'  => $options['localeCacheDir'],
            ]);
        }

        $codeset = 'UTF-8';

        putenv('LANG='.$options['locale'].'.'.$codeset);
        putenv('LANGUAGE='.$options['locale'].'.'.$codeset);
        setlocale(LC_ALL, $options['locale'].'.'.$codeset);

        foreach ($options['domains'] as $domain) {
            bindtextdomain($domain, $options['localeCacheDir']);
            bind_textdomain_codeset($domain, $codeset);
        }

        $this->domains = $options['domains'];
        $this->defaultDomain = reset($options['domains']);

        textdomain($this->defaultDomain);
    }

    /**
     * Accesses the gettext function for a message in singular form
     *
     * @param  string $message
     * @return string
     */
    public function gettext($message)
    {
        $translation = gettext($message);
        return $translation;
    }

    /**
    * Accesses the gettext function for a message in singular form
    *
    * @param  string $message
    * @return string
    */
    public function t($message)
    {
      $translation = gettext($message);
      return $translation;
    }

    /**
     * Access the ngettext function for a message with a plural form
     *
     * @param  string  $msgid1
     * @param  string  $msgid2
     * @param  integer $n
     * @return string
     */
    public function ngettext($msgid1, $msgid2, $n)
    {
        $translation = ngettext($msgid1, $msgid2, $n);
        return $translation;
    }

    /**
    * Access the ngettext function for a message with a plural form
    *
    * @param  string  $msgid1
    * @param  string  $msgid2
    * @param  integer $n
    * @return string
    */
    public function n($msgid1, $msgid2, $n)
    {
      $translation = ngettext($msgid1, $msgid2, $n);
      return $translation;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $index
     * @return boolean
     */
    public function exists($index)
    {
        return gettext($index) !== '';
    }

    /**
     * Changes the current domain (i.e. the translation file). The passed domain
     * must be one of those passed to the constructor.
     *
     * @param  string                    $domain
     * @return string                    Returns the new current domain.
     * @throws \InvalidArgumentException
     */
    public function setDomain($domain)
    {
        if (!in_array($domain, $this->domains)) {
            throw new \InvalidArgumentException($domain . ' is invalid translation domain');
        }

        return textdomain($domain);
    }

    /**
     * Sets the default domain. The default domain is the first item in the array
     * of domains passed to the constructor. Obviously, this method is irrelevant
     * if Gettext was configured with a single domain only.
     *
     * @access public
     * @return string Returns the new current domain.
     */
    public function resetDomain()
    {
        return textdomain($this->defaultDomain);
    }
}
