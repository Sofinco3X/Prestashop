<?php
/**
* Sofinco PrestaShop Module
*
* Feel free to contact Verifone at support@paybox.com for any
* question.
*
* LICENSE: This source file is subject to the version 3.0 of the Open
* Software License (OSL-3.0) that is available through the world-wide-web
* at the following URI: http://opensource.org/licenses/OSL-3.0. If
* you did not receive a copy of the OSL-3.0 license and are unable
* to obtain it through the web, please send a note to
* support@e-transactions.fr so we can mail you a copy immediately.
*
*  @category  Module / payments_gateways
*  @version   3.0.8
*  @author    BM Services <contact@bm-services.com>
*  @copyright 2012-2016 Sofinco
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Module configuration
 */
class SofincoConfig
{
    private $_defaults = array(
        'SOFINCO_3DS'                            => 1,
        'SOFINCO_3DS_MIN_AMOUNT'                 => '',
        'SOFINCO_3DS_MAX_AMOUNT'                 => '',
        'SOFINCO_DEBUG_MODE'                     => 'FALSE',
        'SOFINCO_HASH'                           => 'SHA512',
        'SOFINCO_IDENTIFIANT'                    => '259207933',
        'SOFINCO_KEYTEST'                        => '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF',
        'SOFINCO_PASS'                           => 'SOFINCOACTIONS',
        'SOFINCO_PRODUCTION'                     => 0,
        'SOFINCO_RANG'                           => '071',
        'SOFINCO_SITE'                           => '8888872',
        'SOFINCO_WEB_CASH_DIFF_DAY'              => 0,
        'SOFINCO_WEB_CASH_TYPE'                  => 'immediate',
        'SOFINCO_AUTORIZE_WALLET_CARD'           => 'CB,VISA,EUROCARD_MASTERCARD',
        'SOFINCO_WEB_CASH_ENABLE'                => 1,
        'SOFINCO_WEB_CASH_VALIDATION'            => '',
        'SOFINCO_WEB_CASH_STATE'                 => 2,
        'SOFINCO_WEB_CASH_DIRECT'                => 1,
        'SOFINCO_RECURRING_ENABLE'               => '',
        'SOFINCO_RECURRING_NUMBER'               => '0',
        'SOFINCO_RECURRING_PERIODICITY'          => '',
        'SOFINCO_RECURRING_ADVANCE'              => '',
        'SOFINCO_RECURRING_MIN_AMOUNT'           => '',
        'SOFINCO_RECURRING_MODE'                 => 'NX',
        'SOFINCO_LAST_STATE_NX'                  => 2,
        'SOFINCO_MIDDLE_STATE_NX'                => '',
        'SOFINCO_SUBSCRIBE_NUMBER'               => '0',
        'SOFINCO_SUBSCRIBE_PERIODICITY'          => '',
        'SOFINCO_SUBSCRIBE_DAY'                  => '1',
        'SOFINCO_SUBSCRIBE_DELAY'                => '0',
        'SOFINCO_DIRECT_ACTION'                  => 'N',
        'SOFINCO_DIRECT_VALIDATION'              => '',
        'SOFINCO_WALLET_ACTION'                  => 'N',
        'SOFINCO_WALLET_PERSONNAL_DATA'          => 0,
        'SOFINCO_DEFAULTCATEGORYID'              => '',
        'SOFINCO_WEB_CASH_ACTION'                => 'N',
        'SOFINCO_BO_ACTIONS'                     => 0,
        'SOFINCO_PAYMENT_DISPLAY'                => 0,
        'SOFINCO_DOC_URL'                        => 'https://www.e-transactions.fr/pages/global.php?page=telechargement',
        'SOFINCO_PAYMENT_METHOD'                 => '3XCB',
        'SOFINCO_PAYMENT_METHOD_LABEL'           => 'Sofinco 3XCB'
        //'SOFINCO_CANCEL_URL'                     => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation.php',
        //'SOFINCO_NOTIFICATION_URL'               => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation.php',
        //'SOFINCO_RETURN_URL'                     => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation.php',
        //'SOFINCO_NOTIFICATION_NX_URL'            => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation_nx.php',
        //'SOFINCO_RETURN_NX_URL'                  => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/validation_nx.php',
    );

    private $_urls = array(
        'system' => array(
            'test' => array(
                'https://preprod-tpeweb.paybox.com/php/'
            ),
            'production' => array(
                'https://tpeweb1.paybox.com/php/',
                'https://tpeweb.paybox.com/php/',
            ),
        ),
        'php' => array(
            'test' => array(
                'https://preprod-tpeweb.paybox.com/php/'
            ),
            'production' => array(
                'https://tpeweb1.paybox.com/php/',
                'https://tpeweb.paybox.com/php/',
            ),
        ),
        'mobile' => array(
            'test' => array(
                'https://preprod-tpeweb.paybox.com/php/'
            ),
            'production' => array(
                'https://tpeweb1.paybox.com/php/',
                'https://tpeweb.paybox.com/php/',
            ),
        ),
        'resabo' => array(
            'test' => array(
                'https://preprod-tpeweb.paybox.com/cgi-bin/ResAbon.cgi'
            ),
            'production' => array(
                'https://tpeweb1.paybox.com/cgi-bin/ResAbon.cgi',
                'https://tpeweb.paybox.com/cgi-bin/ResAbon.cgi',
            ),
        ),
    );

    // Remaining
    // 'SOFINCO_AUTORIZE_WALLET_CARD'           => 'CB,VISA,EUROCARD_MASTERCARD',
    // 'SOFINCO_WEB_CASH_ENABLE'                => 1,
    // 'SOFINCO_WEB_CASH_STATE'                 => 2,
    // 'SOFINCO_WEB_CASH_DIRECT'                => 1,
    // 'SOFINCO_RECURRING_ENABLE'               => '',
    // 'SOFINCO_RECURRING_NUMBER'               => '0',
    // 'SOFINCO_RECURRING_PERIODICITY'          => '',
    // 'SOFINCO_RECURRING_ADVANCE'              => '',
    // 'SOFINCO_RECURRING_MIN_AMOUNT'           => '',
    // 'SOFINCO_RECURRING_MODE'                 => 'NX',
    // 'SOFINCO_LAST_STATE_NX'                  => 2,
    // 'SOFINCO_MIDDLE_STATE_NX'                => '',
    // 'SOFINCO_SUBSCRIBE_NUMBER'               => '0',
    // 'SOFINCO_SUBSCRIBE_PERIODICITY'          => '',
    // 'SOFINCO_SUBSCRIBE_DAY'                  => '1',
    // 'SOFINCO_SUBSCRIBE_DELAY'                => '0',
    // 'SOFINCO_DIRECT_ACTION'                  => 'N',
    // 'SOFINCO_DIRECT_VALIDATION'              => '',
    // 'SOFINCO_WALLET_ACTION'                  => 'N',
    // 'SOFINCO_WALLET_PERSONNAL_DATA'          => 0,
    // 'SOFINCO_DEFAULTCATEGORYID'              => ''

    private function _get($name)
    {
        $value = Configuration::get($name);
        if (is_null($value)) {
            $value = false;
        }

        if (($value === false) || ($name=='SOFINCO_HASH' && $value === '') && isset($this->_defaults[$name])) {
            $value = $this->_defaults[$name];
        }

        return $value;
    }

    public function get3DSEnabled()
    {
        return $this->_get('SOFINCO_3DS');
    }

    public function get3DSAmount()
    {
        return $this->_get('SOFINCO_3DS_MIN_AMOUNT');
    }
    
    public function get3DSMaxAmount()
    {
        return $this->_get('SOFINCO_3DS_MAX_AMOUNT');
    }

    public function getAllowedIps()
    {
        return array('194.2.122.158','195.25.7.166','195.101.99.76','194.2.122.190', '195.25.67.22');
    }

    public function getAutoCaptureState()
    {
        $value = $this->_get('SOFINCO_WEB_CASH_VALIDATION');
        return empty($value) ? -1 : intval($value);
    }

    public function getDebitType()
    {
        return $this->_get('SOFINCO_WEB_CASH_TYPE');
    }

    public function getDefaults()
    {
        return $this->_defaults;
    }

    public function getDelay()
    {
        return $this->_get('SOFINCO_WEB_CASH_DIFF_DAY');
    }

    public function getDeliveryDelay()
    {
        return $this->_get('SOFINCO_NBDELIVERYDAYS');
    }

    public function getHmacAlgo()
    {
        return $this->_get('SOFINCO_HASH');
    }

    public function getHmacKey()
    {
        $value = $this->_get('SOFINCO_KEYTEST');
        $crypt = new SofincoEncrypt();
        $value = $crypt->decrypt($value);

        return $value;
    }

    public function getIdentifier()
    {
        return $this->_get('SOFINCO_IDENTIFIANT');
    }

    public function getKwixoSuccessState()
    {
        return $this->_get('SOFINCO_KWIXO');
    }

    public function getPassword()
    {
        $value = $this->_get('SOFINCO_PASS');
        $crypt = new SofincoEncrypt();
        $value = $crypt->decrypt($value);

        return $value;
    }

    public function getRank()
    {
        return $this->_get('SOFINCO_RANG');
    }

    public function getRecurringMinimalAmount()
    {
        return floatval($this->_get('SOFINCO_RECURRING_MIN_AMOUNT'));
    }

    public function getSite()
    {
        return $this->_get('SOFINCO_SITE');
    }

    public function getSubscription()
    {
        return $this->_get('SOFINCO_WEB_CASH_DIRECT');
    }

    public function getSuccessState()
    {
        return $this->_get('SOFINCO_WEB_CASH_STATE');
    }

    protected function _getUrls($type)
    {
           $environment = $this->isProduction() ? 'production' : 'test';
        if (isset($this->_urls[$type][$environment])) {
            return $this->_urls[$type][$environment];
        }

        return array();
    }

    public function getDirectUrls()
    {
        return $this->_getUrls('direct');
    }

    public function getKwixoUrls()
    {
        return $this->_getUrls('kwixo');
    }

    public function getPHPUrls()
    {
        return $this->_getUrls('php');
    }

    public function getMobileUrls()
    {
        return $this->_getUrls('mobile');
    }

    public function getSystemUrls()
    {
        return $this->_getUrls('system');
    }

    public function getResAboUrls()
    {
        return $this->_getUrls('resabo');
    }

    public function isDebug()
    {
        return $this->_get('SOFINCO_DEBUG_MODE') == 1;
    }

    public function isRecurringEnabled()
    {
        return $this->_get('SOFINCO_RECURRING_ENABLE') == 1;
    }

    public function getDebitTypeForCard()
    {
        $type = $this->getDebitType();
        if ('immediate' === $type) {
            return 'immediat';
        } elseif ('delayed' === $type) {
            return 'differe';
        } elseif ('receive' === $type) {
            return 'expedition';
        } else {
            return $type;
        }
    }

    public function isRecurringCard($method)
    {
        if (in_array($method['type_card'], array('CB', 'VISA', 'EUROCARD_MASTERCARD', 'AMEX'))) {
            return true;
        }

        return false;
    }

    public function isProduction()
    {
        return $this->_get('SOFINCO_PRODUCTION') == 1;
    }
    
    public function getPaymentMethod()
    {
        return $this->_get('SOFINCO_PAYMENT_METHOD');
    }
    
    public function getPaymentMethodLabel()
    {
        return $this->_get('SOFINCO_PAYMENT_METHOD_LABEL');
    }
}
