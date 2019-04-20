<?php
namespace Webird\Auth;

use Phalcon\DI;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Storage\Exception\TokenNotFoundException;
use OAuth\Common\Storage\Exception\AuthorizationStateNotFoundException;

/**
 * Stores a token in a PHP session.
 */
class OauthSessionStorage implements TokenStorageInterface
{
    /**
     * @var string
     */
    protected $sessionVariableName;

    /**
     * @var string
     */
    protected $stateVariableName;

    /**
     * @param bool $startSession Whether or not to start the session upon construction.
     * @param string $sessionVariableName the variable name to use within the _SESSION superglobal
     * @param string $stateVariableName
     */
    public function __construct(
        $sessionVariableName = 'oauth_token',
        $stateVariableName = 'oauth_state'
    ) {
        $session = DI::getDefault()->getSession();

        $this->sessionVariableName = $sessionVariableName;
        $this->stateVariableName = $stateVariableName;
        if (!isset($session[$sessionVariableName])) {
            $session[$sessionVariableName] = [];
        }
        if (!isset($session[$stateVariableName])) {
            $session[$stateVariableName] = [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAccessToken($service)
    {
        $session = DI::getDefault()->getSession();

        if ($this->hasAccessToken($service)) {
            return unserialize($session[$this->sessionVariableName][$service]);
        }

        throw new TokenNotFoundException('Token not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function storeAccessToken($service, TokenInterface $token)
    {
        $session = DI::getDefault()->getSession();

        $serializedToken = serialize($token);

        if (isset($session[$this->sessionVariableName])
            && is_array($session[$this->sessionVariableName])
        ) {
            $session[$this->sessionVariableName][$service] = $serializedToken;
        } else {
            $session[$this->sessionVariableName] = [
                $service => $serializedToken,
            ];
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAccessToken($service)
    {
        $session = DI::getDefault()->getSession();

        return isset($session[$this->sessionVariableName], $session[$this->sessionVariableName][$service]);
    }

    /**
     * {@inheritDoc}
     */
    public function clearToken($service)
    {
        $session = DI::getDefault()->getSession();

        if (array_key_exists($service, $session[$this->sessionVariableName])) {
            unset($session[$this->sessionVariableName][$service]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllTokens()
    {
        $session = DI::getDefault()->getSession();

        unset($session[$this->sessionVariableName]);

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function storeAuthorizationState($service, $state)
    {
        $session = DI::getDefault()->getSession();

        if (isset($session[$this->stateVariableName])
            && is_array($session[$this->stateVariableName])
        ) {
            $session[$this->stateVariableName][$service] = $state;
        } else {
            $session[$this->stateVariableName] = [
                $service => $state,
            ];
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function hasAuthorizationState($service)
    {
        $session = DI::getDefault()->getSession();

        return isset($session[$this->stateVariableName], $session[$this->stateVariableName][$service]);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieveAuthorizationState($service)
    {
        $session = DI::getDefault()->getSession();

        if ($this->hasAuthorizationState($service)) {
            return $session[$this->stateVariableName][$service];
        }

        throw new AuthorizationStateNotFoundException('State not found in session, are you sure you stored it?');
    }

    /**
     * {@inheritDoc}
     */
    public function clearAuthorizationState($service)
    {
        $session = DI::getDefault()->getSession();

        if (array_key_exists($service, $session[$this->stateVariableName])) {
            unset($session[$this->stateVariableName][$service]);
        }

        // allow chaining
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function clearAllAuthorizationStates()
    {
        $session = DI::getDefault()->getSession();

        unset($session[$this->stateVariableName]);

        // allow chaining
        return $this;
    }

    public function __destruct()
    {
#        session_write_close();
    }
}
