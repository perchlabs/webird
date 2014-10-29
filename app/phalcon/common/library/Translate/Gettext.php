<?php
namespace Webird\Translate;

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
     * @throws \Phalcon\Translate\Exception
     */
    public function __construct($options)
    {
        if (!is_array($options)) {
            throw new Exception('Invalid options');
        }

        if (!isset($options['locale'])) {
            throw new Exception('Parameter "locale" is required');
        }

        if (!isset($options['domains']) || !is_array($options['domains'])) {
            throw new Exception('"domains" must be specified and it must be an array.');
        }

        putenv("LC_ALL=" . $options['locale']);
        setlocale(LC_ALL, $options['locale']);

        foreach ($options['domains'] as $domain => $dir) {
            bindtextdomain($domain, $dir);
            bind_textdomain_codeset($domain, 'UTF-8');
        }
        // set the first domain as default
        reset($options['domains']);
        $this->defaultDomain = key($options['domains']);
        // save list of domains
        $this->domains = array_keys($options['domains']);

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
