<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/01/2016
 * Time: 09:39
 */

require "Yahoo/YahooOAuthApplication.class.php";

class Inchoo_SocialConnect_Model_Yahoo_Oauth2_Client {


    const REDIRECT_URI_ROUTE = 'socialconnect/yahoo/connect';

    const XML_PATH_ENABLED = 'customer/inchoo_socialconnect_yahoo/enabled';
    const XML_PATH_CLIENT_ID = 'customer/inchoo_socialconnect_yahoo/client_id';
    const XML_PATH_CLIENT_SECRET = 'customer/inchoo_socialconnect_yahoo/client_secret';
    const XML_PATH_APPLICATION_ID = 'customer/inchoo_socialconnect_yahoo/application_id';



    protected $isEnabled = null;
    protected $clientId = null;
    protected $clientSecret = null;
    protected $applicationId = null;
    protected $redirectUri = null;
    protected $state = '';
    protected $scope = array(
        'https://www.googleapis.com/auth/userinfo.profile',
        'https://www.googleapis.com/auth/userinfo.email',
    );

    /**
     * @var YahooOAuthApplication
     */
    protected $yahooOAuthClient;

    protected $token = null;

    public function __construct($params = array())
    {
        if(($this->isEnabled = $this->_isEnabled())) {
            $this->clientId = $this->_getClientId();
            $this->clientSecret = $this->_getClientSecret();
            $this->applicationId = $this->_getApplicationId();
            $this->redirectUri = Mage::getModel('core/url')->sessionUrlVar(
                Mage::getUrl(self::REDIRECT_URI_ROUTE)
            );

            if(!empty($params['scope'])) {
                $this->scope = $params['scope'];
            }

            if(!empty($params['state'])) {
                $this->state = $params['state'];
            }

            if(!empty($params['access'])) {
                $this->access = $params['access'];
            }

            if(!empty($params['prompt'])) {
                $this->prompt = $params['prompt'];
            }

            $this->yahooOAuthClient = new YahooOAuthApplication($this->clientId,$this->clientSecret,$this->applicationId,self::REDIRECT_URI_ROUTE);
        }

    }

    public function setRequestToken($token){
        $this->token = $token;
    }

    public function getRequestToken(){
        if($this->token == null){
            $this->token = $this->yahooOAuthClient->getRequestToken($this->redirectUri);
            Mage::getSingleton('core/session')->setYahooToken(serialize($this->token));
        }

        return $this->token;

    }

    public function getAccessToken($verifier){
        return $this->yahooOAuthClient->getAccessToken($this->getRequestToken(),$verifier);
    }

    public function getAuthUrl(){
        return $this->yahooOAuthClient->getAuthorizationUrl($this->getRequestToken());
    }

    /**
     * @return YahooOAuthApplication
     */
    public function getYahooClient(){
        return $this->yahooOAuthClient;
    }


    public function isEnabled()
    {
        return (bool) $this->isEnabled;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getRedirectUri()
    {
        return $this->redirectUri;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getAccess()
    {
        return $this->access;
    }

    public function getApplicationId(){
        return $this->applicationId;
    }

    protected function _isEnabled()
    {
        return $this->_getStoreConfig(self::XML_PATH_ENABLED);
    }

    protected function _getClientId()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_ID);
    }

    protected function _getClientSecret()
    {
        return $this->_getStoreConfig(self::XML_PATH_CLIENT_SECRET);
    }


    protected function _getApplicationId()
    {
        return $this->_getStoreConfig(self::XML_PATH_APPLICATION_ID);
    }

    protected function _getStoreConfig($xmlPath)
    {
        return Mage::getStoreConfig($xmlPath, Mage::app()->getStore()->getId());
    }

}