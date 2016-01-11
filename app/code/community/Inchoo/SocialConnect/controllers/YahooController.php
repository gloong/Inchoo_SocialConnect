<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/01/2016
 * Time: 11:00
 */

class Inchoo_SocialConnect_YahooController extends Inchoo_SocialConnect_Controller_Abstract{

    protected function _connectCallback() {
        //$params = $this->getRequest()->getParams();
        $errorCode = $this->getRequest()->getParam('error');
        $OAuthToken = $this->getRequest()->getParam('oauth_token');
        $OAuthVerifier = $this->getRequest()->getParam('oauth_verifier');
        if(!($errorCode || $OAuthToken) && !$OAuthVerifier) {
            // Direct route access - deny
            return $this;
        }

        if(!$OAuthVerifier) {
            return $this;
        }

        if($errorCode) {
            // Google API red light - abort
            if($errorCode === 'access_denied') {
                Mage::getSingleton('core/session')
                    ->addNotice(
                        $this->__('Yahoo Connect process aborted.')
                    );

                return $this;
            }

            throw new Exception(
                sprintf(
                    $this->__('Sorry, "%s" error occured. Please try again.'),
                    $errorCode
                )
            );
        }

        if ($OAuthVerifier) {


            // Google API green light - proceed
            $redirectUrl = Mage::getSingleton('core/session')->getSocialRedirectUrl();
            if ($redirectUrl) {
                $redirectUrl = $redirectUrl . '?code=' . $OAuthVerifier."&channel=yahoo&token=".$OAuthToken;
                $response = $this->getResponse();
                $response->setRedirect($redirectUrl);
                Mage::getSingleton('core/session')->setSocialRedirectUrl(null);
                $response->sendResponse();
                exit;
            }


        }
    }
}