<?php
namespace Webird\Auth;

use Phalcon\Mvc\User\Component,
    OAuth\OAuth2\Service\Google,
    OAuth\Common\Http\Exception\TokenResponseException,
    OAuth\Common\Consumer\Credentials,
    Webird\Models\Users,
    Webird\Models\RememberTokens,
    Webird\Models\SuccessSignins,
    Webird\Models\FailedSignins;

/**
 * Webird\Auth\Auth
 * Manages Authentication/Identity Management in Webird
 */
class Auth extends Component
{

    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolean
     */
    public function check($credentials)
    {
        // Check if the user exist
        $user = Users::findFirstByEmail($credentials['email']);
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new AuthException('Wrong email/password combination');
        }

        // Check the password
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->registerUserThrottling($user->id);
            throw new AuthException('Wrong email/password combination');
        }

        // Check if the user was flagged
        $this->checkUserFlags($user);

        // Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }

        // Register the successful signin
        $this->saveSuccessSignin($user, 'password');
    }

    /**
     * Checks the OAuth callback for final authentication.
     * Throws an exception on fail
     *
     * @param string $provider
     * @param string $code
     */
    public function checkOauth($provider, $code)
    {
        $t = $this->translate;

        if (!isset($this->config->services[$provider])) {
            throw new AuthException($t->gettext('OAuth signin error'));
        }
        $configProvider = $this->config->services[$provider];

        // This provider scope and end point url is subject to time as providers revise their systems
        switch ($provider) {
            case 'google':
                // $scope = Google::SCOPE_EMAIL;
                // $scopeUrl = 'https://www.googleapis.com/oauth2/v1/userinfo';
                $scope = Google::SCOPE_EMAIL;
                $scopeUrl = 'https://www.googleapis.com/plus/v1/people/me';
                break;
            // case 'microsoft':
            //     // TODO:
            //     // $scope = \OAuth\OAuth2\Service\Microsoft::SCOPE_BASIC;
            //     // $scopeUrl = \OAuth\OAuth2\Service\Microsoft::SCOPE_BASIC;
            //     break;
            default:
                throw new AuthException('Invalid oauth provider');
                break;
        }

        $redirectUrl = $this->di->getUrl()->get("signin/oauth/{$provider}");
        // The web server will send these credentials to the Oauth provider
        $credentials = new Credentials(
            $configProvider->clientId,
            $configProvider->clientSecret,
            $redirectUrl
        );

        // Session storage
        $storage = new OauthSessionStorage();
        $service = (new \OAuth\ServiceFactory())
            ->createService($provider, $credentials, $storage, [$scope]);

        // There is a possibility that the access token could not be received
        try {
            $token = $service->requestAccessToken($code);
        } catch (\Exception $e) {
            // error_log($e->getMessage());
            throw new AuthException($t->gettext('OAuth signin error'));
        }

        try {
            $json = $service->request($scopeUrl);
        } catch (\Exception $e) {
            // error_log('The Oauth request failed.');
            throw new AuthException($t->gettext('OAuth signin error'));
        }

        $result = json_decode($json, true);
        if ($result === false) {
            // error_log('The Oauth response did not contain valid JSON.');
            throw new AuthException($t->gettext('OAuth signin error'));
        }

        switch ($provider) {
            case 'google':
                if (! isset($result['emails'][0]['value']) || filter_var($result['emails'][0]['value'], FILTER_VALIDATE_EMAIL) === false) {
                    // error_log('The Oauth response email is invalid.');
                    throw new AuthException($t->gettext('Oauth authentication failed'));
                }
                $email = $result['emails'][0]['value'];
                break;
            case 'microsoft':
            default:
                if (! isset($result['email']) || filter_var($result['email'], FILTER_VALIDATE_EMAIL) === false) {
                    // error_log('The Oauth response email is invalid.');
                    throw new AuthException($t->gettext('Oauth authentication failed'));
                }
                if (! isset($result['verified_email']) || $result['verified_email'] !== true) {
                    throw new AuthException($t->gettext('The email could not be verified.'));
                }
                $email = $result['email'];
                break;
        }

        $user = Users::findFirstByEmail($email);
        if ($user == false) {
            $this->registerUserThrottling(0);
            throw new AuthException($t->gettext('This email is not registered in the system.'));
        }

        // Check if the user was flagged
        $this->checkUserFlags($user);

        // Register the successful signin
        $this->saveSuccessSignin($user, 'oauth');

        return $user;
    }

    /**
     * Creates the OAuth redirect url
     *
     * @param string $provider
     * @return boolean
     */
    public function getRedirectOauthUrl($provider)
    {
        $t = $this->translate;

        $redirectUrl = $this->di->getUrl()->get("signin/oauth/{$provider}");

        $configProvider = $this->config->services[$provider];
        // Setup the credentials for the requests
        $credentials = new Credentials(
            $configProvider->clientId,
            $configProvider->clientSecret,
            $redirectUrl
        );

        switch ($provider) {
            case 'google':
                // $scope = 'userinfo_email';
                $scope = 'email';
                break;
            // case 'microsoft':
            //     $scope = 'basic';
            //     break;
            default:
                throw new AuthException($t->gettext('Invalid oauth provider'));
                break;
        }

        // Session storage
        $storage = new OauthSessionStorage();

        // Instantiate the service using the credentials, http client and storage mechanism for the token
        $service = (new \OAuth\ServiceFactory())
            ->createService($provider, $credentials, $storage, [$scope]);

        $authUri = $service->getAuthorizationUri();

        return $authUri;
    }

    /**
     * Saves the successful signin
     *
     * @param Webird\Models\Users $user
     */
    public function saveSuccessSignin($user, $method)
    {
        $successSignin = new SuccessSignins();
        $successSignin->usersId = $user->id;
        $successSignin->ipAddress = $this->request->getClientAddress();
        $successSignin->method = $method;
        $successSignin->userAgent = $this->request->getUserAgent();
        if (!$successSignin->save()) {
            $messages = $successSignin->getMessages();
            throw new AuthException($messages[0]);
        }

        $this->session->set('auth-identity', [
            'id'    => $user->id,
            'email' => $user->email,
            'role'  => $user->role->name
        ]);

        if ($user->mustChangePassword == 'Y') {
            $this->session->set('must-change-password', true);
            throw new AuthMustChangePasswordException();
        }
    }

    public function doesNeedToChangePassword()
    {
        return ($this->session->has('must-change-password'));
    }

    public function clearNeedToChangePassword()
    {
        if ($this->session->has('must-change-password')) {
            $this->session->remove('must-change-password');
        }
    }

    /**
     * Implements signin throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedSignin = new FailedSignins();
        $failedSignin->usersId = $userId;
        $failedSignin->ipAddress = $this->request->getClientAddress();
        $failedSignin->attempted = time();
        $failedSignin->save();

        $attempts = FailedSignins::count([
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => [
                $this->request->getClientAddress(),
                time() - 3600 * 6
            ]
        ]);

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Webird\Models\Users $user
     */
    public function createRememberEnviroment(Users $user)
    {
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);

        $remember = new RememberTokens();
        $remember->usersId = $user->id;
        $remember->token = $token;
        $remember->userAgent = $userAgent;

        if ($remember->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }
    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Signin using the information in the coookies
     *
     * @return Phalcon\Http\Response
     */
    public function signinWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();

        $user = Users::findFirstById($userId);
        if ($user) {

            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);

            if ($cookieToken == $token) {

                $remember = RememberTokens::findFirst([
                    'usersId = ?0 AND token = ?1',
                    'bind' => [
                        $user->id,
                        $token
                    ]
                ]);
                if ($remember) {

                    // Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        // Check if the user was flagged
                        $this->checkUserFlags($user);

                        // Register the successful signin
                        $this->saveSuccessSignin($user, 'remember');

                        return true;
                    }
                }
            }
        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        throw new AuthRememberMeException();
    }

    /**
     * Checks if the user is banned/inactive
     *
     * @param Webird\Models\Users $user
     */
    public function checkUserFlags(Users $user)
    {
        if ($user->banned != 'N') {
            throw new AuthBannedUserException('The user is disabled');
        }
        if ($user->active != 'Y') {
            throw new AuthInactiveUserException('The user is inactive');
        }
    }

    /**
     * Return if an identity is available
     *
     * @return array
     */
    public function hasIdentity()
    {
        $identity = $this->session->get('auth-identity');
        $hasIdentity = is_array($identity);
        return $hasIdentity;
    }

    /**
     * Returns the id of user
     *
     * @return string
     */
    public function getId()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['id'];
    }

    /**
     * Returns the email of user
     *
     * @return string
     */
    public function getEmail()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['email'];
    }

    /**
     * Returns the role name of user
     *
     * @return string
     */
    public function getRole()
    {
        $identity = $this->session->get('auth-identity');
        return $identity['role'];
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        try {
            if ($this->cookies->has('RMU')) {
                $this->cookies->get('RMU')->delete();
            }
            if ($this->cookies->has('RMT')) {
                $this->cookies->get('RMT')->delete();
            }

            $this->session->remove('auth-identity');
            $this->session->destroy();
        } catch (\Exception $e) {
            throw new AuthException('There was a problem closing the session.');
        }
    }

    /**
     * Auths the user by their id
     *
     * @param int $id
     */
    public function authUserById($id, $method)
    {
        if (!is_string($method)) {
            throw new AuthException('The auth method must be a string');
        }

        $user = Users::findFirstById($id);
        if ($user == false) {
            throw new AuthException('The user does not exist');
        }

        $this->checkUserFlags($user);

        // Register the successful signin
        $this->saveSuccessSignin($user, $method);
    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return \Webird\Models\Users
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-identity');
        if (isset($identity['id'])) {

            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new AuthException('The user does not exist');
            }

            return $user;
        }

        return false;
    }
}
