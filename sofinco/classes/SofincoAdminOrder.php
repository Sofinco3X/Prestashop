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
*  @version   3.0.11
*  @author    BM Services <contact@bm-services.com>
*  @copyright 2012-2016 Sofinco
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__).'/SofincoAbstractAdmin.php';

/**
 * Admin order details page helper
 */
class SofincoAdminOrder extends SofincoAbstractAdmin
{
    private function _writeDetails(SofincoHtmlWriter $w, array $details)
    {
        $this->_writeCommonDetails($w, $details);
        $this->_writeEndDetails($w, $details);
    }

    private function _writeCommonDetails(SofincoHtmlWriter $w, array $details)
    {
        $label = $this->l('Payment details');
        $w->blockStart('sofinco_details', $label, $this->getImagePath().'sofinco-xs.png');

        $w->helpWidget($this->l('Help'), $this->l('See the documentation for help'), Configuration::get('SOFINCO_DOC_URL'));

        $tpl = '<p><strong>%s</strong> %s</p>';

        $w->html('<div class="row"><div class="col-lg-3">');

        // Transaction id
        $w->html(sprintf(
            $tpl,
            $this->l('Ref.:'),
            $w->escape($details['id_transaction'])
        ));

        // Payment mean
        $img = $this->getModule()->getMethodImageUrl(strtoupper($details['carte']));
        $text = sprintf('<img style="vertical-align:middle;" src="%s" />', $img);
        if (isset($details['method']) && 'WALLET' == $details['method']) {
            $img = $this->getModule()->getMethodImageUrl('PAYLIB');
            $text = sprintf('<img style="vertical-align:middle;" src="%s" />', $img).$text;
        }
        $w->html(sprintf(
            $tpl,
            $this->l('Payment Method:'),
            $text
        ));

        // Card Number
        $text = !empty($details['carte_num']) ? $w->escape($details['carte_num']) : $this->l('Unknown');
        $w->html(sprintf(
            $tpl,
            $this->l('Card number:'),
            $text
        ));

        // Card country
        $text = !empty($details['pays']) ? $w->escape($details['pays']) : $this->l('Unknown');
        $w->html(sprintf(
            $tpl,
            $this->l('Country card:'),
            $text
        ));

        // IP country
        $text = !empty($details['ip']) ? $w->escape($details['ip']) : $this->l('Unknown');
        $w->html(sprintf(
            $tpl,
            $this->l('Pays IP:'),
            $text
        ));

        // 3-D Secure status
        if (!empty($details['secure'])) {
            $text = $details['secure'] == 'O' ? $this->l('Yes') : $this->l('No');
            $text = $w->badge($text, ($details['secure'] == 'O' ? true : false));
            $w->html(sprintf(
                $tpl,
                $this->l('Warranty 3D-Secure:'),
                $text
            ));
        }

        // Processing date
        $text = $w->escape(preg_replace('/^([0-9]{2})([0-9]{2})([0-9]{4})$/', '$1/$2/$3', $details['date']));
        $w->html(sprintf(
            $tpl,
            $this->l('Processing date:'),
            $text
        ));

        $w->html('</div>');
        $hasMessages = $this->_writeMessages($w, $details);

        if ($hasMessages) {
            $w->html('<div class="col-lg-4">');
        } else {
            $w->html('<div class="col-lg-9">');
        }
    }

    private function _writeCapturableDetails(SofincoHtmlWriter $w, array $details)
    {
        $this->_writeCommonDetails($w, $details);

        $w->html(sprintf(
            '<p>%s</p>',
            $this->l('The transaction can be charged only once')
        ));
        // Capture all amount
        // [3.0.4] Capture All button only if manual capture is set in configuration
        $stateId = Configuration::get('SOFINCO_WEB_CASH_VALIDATION', '-1');
        if ($stateId == '-1') {
            $w->html('<p class="left">');
            $w->html(sprintf(
                '<form id="sofinco_capture_all" method="post" action="%s">',
                $w->escape($_SERVER['REQUEST_URI'])
            ));
            $w->html(sprintf(
                '<input type="hidden" name="id_order" value="%d" />',
                $details['id_order']
            ));
            $w->html('<input type="hidden" name="order_action" value="capture_all" />');
            $w->button($this->l('Capture total transaction'), 'submit');
            $w->html('</form>');
            $w->html('</p>');
        }

        // Capture amount
        $w->html('<p class="left">');
        $w->html('<form id="sofinco_capture_amount">');
        $w->button($this->l('Capture of an amount'), 'submit');
        $w->html('</form>');
        $w->html('</p>');

        // Capture amount
        $w->html('<p class="left">');
        $w->html(sprintf(
            '<form id="sofinco_capture_amount_input" method="post" action="%s" style="display: none;">',
            $w->escape($_SERVER['REQUEST_URI'])
        ));
        $w->html(sprintf(
            '<input type="hidden" name="id_order" value="%d" />',
            $details['id_order']
        ));
        $w->html('<input type="hidden" name="order_action" value="capture_amount" />');
        $w->html('<input type="text" name="amountCapture" value="" /> ');
        $w->button($this->l('Capture this amount'), 'submit');
        $w->html('</form>');
        $w->html('</p>');
/*
        // Cancel item(s)
        if (Configuration::get('PS_ORDER_RETURN')) {
            $w->html(sprintf(
                '<p>%s</p>',
                $this->l('Canceling a product will not capture the transaction.')
            ));
            $w->html('<p class="left">');
            $w->html(sprintf(
                '<form id="sofinco_cancel_item%s">',
                version_compare(_PS_VERSION_,'1.5','<') ? '14' : ''
            ));
            $w->button($this->l('Cancel a product'), 'submit');
            $w->html('</form>');
            $w->html('</p>');
        }
*/
        $this->_writeEndDetails($w, $details);

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $js = <<<EOF
(function($) {
    $(document).ready(function() {
        $('#sofinco_capture_all, #sofinco_capture_amount_input').submit(function() {
            return confirm(%s);
        });
        $('#sofinco_cancel_item14').submit(function() {
            $('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
            return false;
        });
        $('#sofinco_cancel_item').submit(function() {
            $('#desc-order-standard_refund').click();
            return false;
        });
        $('#sofinco_capture_amount').submit(function() {
            $('#sofinco_capture_amount_input').show('normal');
            return false;
        });
    });
})(jQuery);
EOF;
        } else {
            $js = <<<EOF
(function($) {
    $(document).ready(function() {
        $('#sofinco_capture_all, #sofinco_capture_amount_input').submit(function() {
            return confirm(%s);
        });
        $('#sofinco_cancel_item14').submit(function() {
            $('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
            return false;
        });
        $('#sofinco_cancel_item').submit(function() {
            $('#desc-order-standard_refund').click();
            $('body').animate({scrollTop : $('#refundForm').offset().top - $('body').scrollTop() }, 1000, 'swing');
            return false;
        });
        $('#sofinco_capture_amount').submit(function() {
            $('#sofinco_capture_amount_input').show('normal');
            return false;
        });
    });
})(jQuery);
EOF;
        }
        $w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
    }

    private function _writeMessages(SofincoHtmlWriter $w, array $details)
    {
        $hasMessages = false;
        if (version_compare(_PS_VERSION_, '1.7.1.2', '>=')) {
            $messages = Message::getMessagesByOrderId((int)$details['id_order'], true);

            if (0 < count($messages)) {
                $hasMessages = true;

                $w->html('<div class="col-lg-4">');
                $w->html('  <div class="panel panel-highlighted">');
                $w->html('    <div class="message-item">');

                foreach ($messages as $message) {
                    $pos = Tools::strpos($message['message'], $this->getModule()->getPlatform());
                    if (false !== $pos) {
                        $w->html('<div class="message-avatar"><div class="avatar-md"><i class="icon-comments icon-2x"></i></div></div>');
                        $w->html('<div class="message-body">');
                        $w->html('  <span class="message-date">&nbsp;<i class="icon-calendar"></i> '.Tools::displayDate($message['date_add'], null, true).'</span>');
                        $w->html('  <h4 class="message-item-heading">&nbsp;</h4>');
                        $w->html('  <p class="message-item-text">'.nl2br($message['message']).'</p>');
                        $w->html('</div>');
                    }
                }

                $w->html('    </div>');
                $w->html('  </div>');
                $w->html('</div>');
            }
        }

        if ($hasMessages) {
            $w->html('<div class="col-lg-1"></div>');
        }

        return $hasMessages;
    }

    private function _writeEndDetails(SofincoHtmlWriter $w, array $details)
    {
        $w->html('</div></div>');

        // [2.2.0] Add Sofinco refund checkbox activation + translation
        $w->html('<script type="text/javascript">var refundAvailable = '.(($this->getHelper()->isDirectEnabled()) ? 1 : 0).'; var refundCheckboxText = "'.$this->l('Generate a PaymentPlatform refund').'";</script>');

        $w->blockEnd();
    }

    private function _writeKwixoDetails(SofincoHtmlWriter $w, array $details)
    {
        $this->_writeCommonDetails($w, $details);

        // Information message
        $text = $this->l('please manage your Kwixo transaction in PaymentPlatform interface');
        $w->alertWarn($text);

        $this->_writeEndDetails($w, $details);
    }

    /**
     * Prints Sofinco payment information
     *
     * @version  3.0.11
     * @param    SofincoHtmlWriter $w       [description]
     * @param    array                  $details [description]
     */
    private function _writeRefundableDetails(SofincoHtmlWriter $w, array $details)
    {
        $this->_writeCommonDetails($w, $details);

        switch ($details['payment_by']) {
            case 'Sofinco':
                $this->_writeRefundableStandard($w, $details);
                break;

            case 'SofincoRecurring':
                $this->_writeRefundableRecurring($w, $details);
                break;

            case 'mixed':
                $this->_writeRefundableMixed($w, $details);
                break;
        }

        $this->_writeEndDetails($w, $details);
    }

    private function _writeRefundableRecurring(SofincoHtmlWriter $w, array $details)
    {
        $partialRefund = 0;
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $partialRefund = $this->getHelper()->getAmountPartialRefund($details['id_order']);
        }

        // Refund first payment
        if ($details['refund_amount'] == 0) {
            $w->html('<p class="left">');
            $w->html(sprintf(
                '<form id="sofinco_refund_first" method="post" action="%s">',
                $w->escape($_SERVER['REQUEST_URI'])
            ));
            $w->html(sprintf(
                '<input type="hidden" name="id_order" value="%d" />',
                $details['id_order']
            ));
            $w->html('<input type="hidden" name="order_action" value="refund_first" />');
            $w->button($this->l('Refund the first payment'), 'submit');
            $w->html('</form>');
            $w->html('</p>');
        }

        // Cancel recurring
        if (($details['payment_status'] != 'canceled') && ($details['payment_status'] != 'canceled/refundRecurring')) {
            $w->html('<p class="left">');
            $w->html(sprintf(
                '<form id="sofinco_refund_cancel" method="post" action="%s">',
                $w->escape($_SERVER['REQUEST_URI'])
            ));
            $w->html(sprintf(
                '<input type="hidden" name="id_order" value="%d" />',
                $details['id_order']
            ));
            $w->html('<input type="hidden" name="order_action" value="cancel_recurring" />');
            $w->button($this->l('Cancel the next recurring payment'), 'submit');
            $w->html('</form>');
            $w->html('</p>');
        }


                $js = <<<EOF
(function($) {
    $(document).ready(function() {
        $('#sofinco_refund_first, #sofinco_refund_cancel').submit(function() {
            return confirm(%s);
        });
    });
})(jQuery);
EOF;
        $w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
    }

    private function _writeRefundableStandard(SofincoHtmlWriter $w, array $details)
    {
        $partialRefund = 0;
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $partialRefund = $this->getHelper()->getAmountPartialRefund($details['id_order']);
        }

        $order = new Order(intval($details['id_order']));
        if (Validate::isLoadedObject($order)) {
            $currency = new Currency(intval($order->id_currency));
            $amountScale = $this->getHelper()->getCurrencyScale($order);

            // $possibleRefund = (float)($details['amount'] - $details['refund_amount']) / $amountScale;
            $possibleRefund = (float)($details['amount']) / $amountScale;
            // if ($possibleRefund - $partialRefund > 0) {
            if ($possibleRefund > 0) {
                    $text = $this->l('The transaction can be refund many times');
                    $w->html(sprintf('<p>%s</p>', $text));

                    // [2.2.0] Use of Tools::displayPrice + only $possibleRefund
                    // $tpl = '<p>%s %s %s</p>';
                    $tpl = '<p>%s %s</p>';
        if (version_compare(_PS_VERSION_, '1.7.6', '>=')) {
            $price = Locale::formatPrice($possibleRefund, $currency);
        }else{
            $price = Tools::displayPrice($possibleRefund, $currency);			
		}
					$w->html(sprintf(
                        $tpl,
                        $this->l('It is possible to repay'),
                        $price
                        // (string)($possibleRefund - $partialRefund),
                        // (string)($possibleRefund),
                        // $currency->sign
                    ));
                // }


                // Refund all amount
                    $w->html('<p class="left">');
                    $w->html(sprintf(
                        '<form id="sofinco_refund_all" method="post" action="%s">',
                        $w->escape($_SERVER['REQUEST_URI'])
                    ));
                    $w->html(sprintf(
                        '<input type="hidden" name="id_order" value="%d" />',
                        $details['id_order']
                    ));
                    $w->html('<input type="hidden" name="order_action" value="refund_all" />');
                    $w->button($this->l('Refund total transaction'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

/*
                // Refund item(s)
                if (Configuration::get('PS_ORDER_RETURN')) {
                    $w->html('<p class="left">');
                    $w->html(sprintf(
                        '<form id="sofinco_refund_item%s">',
                        version_compare(_PS_VERSION_,'1.5','<') ? '14' : ''
                    ));
                    $w->button($this->l('Refund an item'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');
                }
*/
                // Refund amount
                    $w->html('<p class="left">');
                    $w->html('<form id="sofinco_refund_amount">');
                    $w->button($this->l('Refund of an amount'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

                // Refund amount form
                    $w->html('<p class="left">');
                    $w->html(sprintf(
                        '<form id="sofinco_refund_amount_input" method="post" action="%s" style="display:none;">',
                        $w->escape($_SERVER['REQUEST_URI'])
                    ));
                    $w->html(sprintf(
                        '<input type="hidden" name="id_order" value="%d" />',
                        $details['id_order']
                    ));
                    $w->html('<input type="hidden" name="order_action" value="refund_amount" />');
                    $w->html('<input type="text" name="amountRefund" value="" /> ');
                    $w->button($this->l('Refund this amount'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

                    $js = <<<EOF
(function($) {
    $(document).ready(function() {
        $('#sofinco_refund_all, #sofinco_refund_amount_input').submit(function() {
            return confirm(%s);
        });
        $('#sofinco_refund_item14').submit(function() {
            $('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
            return false;
        });
EOF;
                    if (Configuration::get('PS_ORDER_RETURN')) {
                        $js .= <<<EOF
        $('#sofinco_refund_item').submit(function() {
            $('#desc-order-standard_refund').click();
            return false;
        });
EOF;
                    }
                    $js .= <<<EOF
        $('#sofinco_refund_amount').submit(function() {
            $('#sofinco_refund_amount_input').show('normal');
            return false;
        });
    });
})(jQuery);
EOF;
                    $w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
            }
        }
    }

    /**
     * Refund form for mixed payment methods
     *
     * @since    3.0.11
     * @version  3.0.11
     * @param    SofincoHtmlWriter $w       [description]
     * @param    array                  $details [description]
     * @return   [type]                          [description]
     */
    private function _writeRefundableMixed(SofincoHtmlWriter $w, array $details)
    {
        $partialRefund = 0;
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $partialRefund = $this->getHelper()->getAmountPartialRefund($details['id_order']);
        }

        $order = new Order(intval($details['id_order']));
        if (Validate::isLoadedObject($order)) {
            $currency = new Currency(intval($order->id_currency));
            $amountScale = $this->getHelper()->getCurrencyScale($order);

            // $possibleRefund = (float)($details['amount'] - $details['refund_amount']) / $amountScale;
            $possibleRefund = (float)($details['amount']) / $amountScale;

            // Get mixed OrderPayment
            $orderPayments = $this->getHelper()->getPSOrderPaymentMixed($order->reference);
            if (false != $orderPayments) {
                $possibleRefund -= $orderPayments['mixed']->amount;

                $refundablSofincoId = $orderPayments['cc']->transaction_id;

                if ($possibleRefund > 0) {
                    $text = $this->l('The transaction can be refund many times');
                    $w->html(sprintf('<p>%s</p>', $text));

                    // [2.2.0] Use of Tools::displayPrice + only $possibleRefund
                    // $tpl = '<p>%s %s %s</p>';
                    $tpl = '<p>%s %s %s</p>';
                    $w->html(sprintf(
                        $tpl,
                        $this->l('It is possible to repay'),
                        Tools::displayPrice($possibleRefund, $currency),
                        $this->l('on bank card')
                        // (string)($possibleRefund - $partialRefund),
                        // (string)($possibleRefund),
                        // $currency->sign
                    ));

                    // Refund all amount
                    $w->html('<p class="left">');
                    $w->html(sprintf(
                        '<form id="sofinco_refund_all" method="post" action="%s">',
                        $w->escape($_SERVER['REQUEST_URI'])
                    ));
                    $w->html(sprintf(
                        '<input type="hidden" name="id_order" value="%d" />',
                        $details['id_order']
                    ));
                    $w->html('<input type="hidden" name="order_action" value="refund_all" />');
                    // Add amount as hidden field
                    $w->html(sprintf('<input type="hidden" name="amountRefund" value="%s" />', $possibleRefund));
                    $w->html(sprintf('<input type="hidden" name="transaction_id" value="%s" />', $refundablSofincoId));
                    $w->button($this->l('Refund total transaction'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

/*
                    // Refund item(s)
                    if (Configuration::get('PS_ORDER_RETURN')) {
                        $w->html('<p class="left">');
                        $w->html(sprintf(
                            '<form id="sofinco_refund_item%s">',
                            version_compare(_PS_VERSION_,'1.5','<') ? '14' : ''
                        ));
                        $w->button($this->l('Refund an item'), 'submit');
                        $w->html('</form>');
                        $w->html('</p>');
                    }
*/
                    // Refund amount
                    $w->html('<p class="left">');
                    $w->html('<form id="sofinco_refund_amount">');
                    $w->button($this->l('Refund of an amount'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

                    // Refund amount form
                    $w->html('<p class="left">');
                    $w->html(sprintf(
                        '<form id="sofinco_refund_amount_input" method="post" action="%s" style="display:none;">',
                        $w->escape($_SERVER['REQUEST_URI'])
                    ));
                    $w->html(sprintf(
                        '<input type="hidden" name="id_order" value="%d" />',
                        $details['id_order']
                    ));
                    $w->html('<input type="hidden" name="order_action" value="refund_amount" />');
                    $w->html(sprintf('<input type="hidden" name="transaction_id" value="%s" />', $refundablSofincoId));
                    $w->html('<input type="text" name="amountRefund" value="" /> ');
                    $w->button($this->l('Refund this amount'), 'submit');
                    $w->html('</form>');
                    $w->html('</p>');

                    $js = <<<EOF
(function($) {
    $(document).ready(function() {
        $('#sofinco_refund_all, #sofinco_refund_amount_input').submit(function() {
            return confirm(%s);
        });
        $('#sofinco_refund_item14').submit(function() {
            $('html, body').animate({ scrollTop:$('#orderProducts').offset().top }, 'slow');
            return false;
        });
EOF;
/*
                    if (Configuration::get('PS_ORDER_RETURN')) {
                        $js .= <<<EOF
        $('#sofinco_refund_item').submit(function() {
            $('#desc-order-standard_refund').click();
            return false;
        });
EOF;
                    }
*/
                    $js .= <<<EOF
        $('#sofinco_refund_amount').submit(function() {
            $('#sofinco_refund_amount_input').show('normal');
            return false;
        });
    });
})(jQuery);
EOF;
                    $w->js(sprintf($js, json_encode($this->l('Are you sure?'))));
                }
            }
        }
    }


    /**
     * [getContent description]
     *
     * 3.0.11 Add mixed control / allow capture and refund
     *
     * @version  3.0.11
     * @param    SofincoHtmlWriter $w      [description]
     * @param    array                  $params [description]
     * @return   [type]                         [description]
     */
    public function getContent(SofincoHtmlWriter $w, array $params)
    {
        $orderId = $params['id_order'];
        $details = $this->getHelper()->getOrderDetails($orderId);

        // Not handled
        if (empty($details)) {
            return null;
        }

        // Retrieve method
        $method = $this->getHelper()->getPaymentMethod($details['carte']);
        if (1 == $method['mixte']) {
            $details['payment_by'] = 'mixed';
        }

        // For Kwixo payment
        if (in_array($details['carte'], array('STANDARD', '1XRNP', 'CREDIT'))) {
            $this->_writeKwixoDetails($w, $details);
        } // Can be refunded?
        elseif ($this->getHelper()->canRefund($orderId) && ('PREPAYEE' != $details['method'] || 'mixed' == $details['payment_by'])) {
            $this->_writeRefundableDetails($w, $details);
        } // Waiting for capture?
        elseif ($this->getHelper()->canCapture($orderId) && 'mixed' != $details['payment_by']) {
            $this->_writeCapturableDetails($w, $details);
        } // All other cases
        else {
            $this->_writeDetails($w, $details);
        }
    }

    public function _processCaptureAll(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when making capture request'));
            return;
        }

        // [3.0.4] Never change order state (previsously, change state if state is defined in automatic capture order state configuration)
        $changestate = false;
        // $changestate = true;
        // $stateId = Configuration::get('SOFINCO_WEB_CASH_VALIDATION', '-1');
        // if ($stateId == '-1') {
        //     $changestate = false;
        // }
        $result = $this->getHelper()->makeCaptureAll($order, $details, $changestate);

        switch ($result) {
            case 0:
                $w->alertConf($this->l('Funds have been captured.'));
                break;
            case 1:
                $w->alertError($this->l('Capture of funds unsuccessful. Please see log message!'));
                break;
            case 2:
                $w->alertError($this->l('Error when making capture'));
                break;
        }
    }

    public function _processCaptureAmount(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when making capture request'));
            return;
        }

        $amount = Tools::getValue('amountCapture');
        if (!$this->getHelper()->isValidAmount($amount, $this->getHelper()->getCurrencyDecimals($order))) {
            $w->alertError($this->l('Error when making capture request').' '.$this->l('Invalid amount:').' '.$amount);
            return false;
        }

        $result = $this->getHelper()->makeCaptureAmount($order, $details, $amount);

        switch ($result) {
            case 0:
                $w->alertConf($this->l('Funds have been captured.'));
                break;
            case 1:
                $w->alertError($this->l('Capture of funds unsuccessful. Please see log message!'));
                break;
            case 2:
                $w->alertError($this->l('Error when making capture'));
                break;
            case 3:
                $w->alertError($this->l('The capture amount is too high.'));
                break;
        }
    }

    public function _processCancelRecurring(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when canceling recurring payment.'));
            return false;
        }

        $result = $this->getHelper()->deleteRecurringPayment($order, $details);

        if ($result === false) {
            $message = $this->l('Unable to cancel recurring payment.')."\r\n";
            $message .= $this->l('For more information logon to the PaymentPlatform Back-Office');
            $w->alertError($message);
        } else {
            $message = $this->l('Recurring payment canceled.');
            $w->alertConf($message);
        }
    }

    /**
     * [_processRefundAmount description]
     *
     * 3.0.11 Transaction Id parameter
     * 3.0.6  Amount validation
     *
     * @version  3.0.11
     * @param    SofincoHtmlWriter $w
     * @param    array                  $details
     */
    public function _processRefundAmount(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when making refund request'));
            return false;
        }

        $amount = Tools::getValue('amountRefund');
        if (!$this->getHelper()->isValidAmount($amount, $this->getHelper()->getCurrencyDecimals($order))) {
            $w->alertError($this->l('Error when making refund request').' '.$this->l('Invalid amount:').' '.$amount);
            return false;
        }

        $transactionId = Tools::getValue('transaction_id');
        if (false !== $transactionId) {
            $details['id_transaction'] = $transactionId;
        }

        // $result = $this->getHelper()->makeRefundAmount($order, $details, Tools::getValue('amountRefund'));
        $result = $this->getHelper()->processPaymentModified($order, $details, $amount);

        if ($result['status'] == 0) {
            if (is_array($result['error'])) {
                foreach ($result['error'] as $error) {
                    $w->alertError($this->l($error));
                }
            } else {
                $w->alertError($this->l($result['error']));
            }
            return false;
        } elseif ($result['status'] == 1) {
            $w->forceReload();
            $w->alertConf($this->l('Please refresh to view the payment modifications'));
        }

        // switch ($result) {
        //     case 0:
        //         $w->alertConf($this->l('Refund has been made.'));
        //         break;
        //     case 1:
        //         $w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
        //         break;
        //     case 2:
        //         $w->alertError($this->l('Error when making refund request'));
        //         break;
        //     case 3:
        //         $w->alertError($this->l('The refund amount is too high.'));
        //         break;

        // }
    }

    public function _processRefundAll(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when making refund request'));
            return false;
        }

        $result = $this->getHelper()->makeRefundAll($order, $details);

        // switch ($result) {
        //     case 0:
        //         $w->alertConf($this->l('Refund has been made.'));
        //         break;
        //     case 1:
        //         $w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
        //         break;
        //     case 2:
        //         $w->alertError($this->l('Error when making refund request'));
        //         break;
        // }

        if ($result['status'] == 0) {
            if (is_array($result['error'])) {
                foreach ($result['error'] as $error) {
                    $w->alertError($this->l($error));
                }
            } else {
                $w->alertError($this->l($result['error']));
            }
            return false;
        } elseif ($result['status'] == 1) {
            $w->forceReload();
            $w->alertConf($this->l('Please refresh to view the payment modifications'));
        }
    }

    public function _processRefundFirst(SofincoHtmlWriter $w, array $details)
    {
        $orderId = $details['id_order'];

        // Load order
        $order = new Order($orderId);
        if (!Validate::isLoadedObject($order)) {
            $w->alertError($this->l('Error when making refund request'));
            return false;
        }

        $result = $this->getHelper()->makeRefundAll($order, $details);

        // switch ($result) {
        //     case 0:
        //         $w->alertConf($this->l('Refund has been made.'));
        //         break;
        //     case 1:
        //         $w->alertError($this->l('Refund request unsuccessful. Please see log message!'));
        //         break;
        //     case 2:
        //         $w->alertError($this->l('Error when making refund request'));
        //         break;
        // }

        if ($result['status'] == 0) {
            if (is_array($result['error'])) {
                foreach ($result['error'] as $error) {
                    $w->alertError($this->l($error));
                }
            } else {
                $w->alertError($this->l($result['error']));
            }
            return false;
        } elseif ($result['status'] == 1) {
            $w->forceReload();
            $w->alertConf($this->l('Please refresh to view the payment modifications'));
        }
    }

    public function processAction(SofincoHtmlWriter $w)
    {
        $details = $this->getHelper()->getOrderDetails(Tools::getValue('id_order'));

        // If handled
        if (!empty($details)) {
            switch (Tools::getValue('order_action')) {
                case 'capture_amount':
                    $this->_processCaptureAmount($w, $details);
                    break;
                case 'capture_all':
                    $this->_processCaptureAll($w, $details);
                    break;
                case 'cancel_recurring':
                    $this->_processCancelRecurring($w, $details);
                    break;
                case 'refund_amount':
                    $this->_processRefundAmount($w, $details);
                    break;
                case 'refund_all':
                    $this->_processRefundAll($w, $details);
                    break;
                case 'refund_first':
                    $this->_processRefundFirst($w, $details);
                    break;
            }
        }
    }
}

