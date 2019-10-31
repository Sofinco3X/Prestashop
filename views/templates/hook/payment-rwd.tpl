{*
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
*  @version   3.0.1
*  @author    BM Services <contact@bm-services.com>
*  @copyright 2012-2016 Sofinco
*  @license   http://opensource.org/licenses/OSL-3.0
*  @link      http://www.e-transactions.fr/
*}
{$sofincoCSS}

{if $sofincoReason == 'cancel'}

<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="alert alert-danger" style="margin-left:15px;">
			{l s='Payment canceled.' mod='sofinco'}
		</div>
	</div>
</div>
{/if}

{if $sofincoReason == 'error'}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="alert alert-danger" style="margin-left:15px;">
			{l s='Payment refused by PaymentPlatform.' mod='sofinco'}
		</div>
	</div>
</div>
{/if}

{if !$sofincoProduction}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="alert alert-danger" style="margin-left:15px;">
			{l s='The PaymentPlatform payment is in test mode.' mod='sofinco'}
		</div>
	</div>
</div>
{/if}

{* Standard payment *}
{foreach from=$sofincoCards item=card name=cards}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module sofinco_module">
			<a href="{$card.url|escape:'html'}" style="background-image: url({$card.image})" title="{$card.card}">
				{l s='Pay by' mod='sofinco'} {$card.label}
			</a>
		</p>
	</div>
</div>
{/foreach}

{* Recurring payment *}
{if !empty($sofincoRecurring)}
<div class="row">
	<div class="col-xs-12 col-md-6">
		<p class="payment_module sofinco_3x"  style="background-image: url({$sofincoImagePath}Paiement_3X.png)">
            {foreach from=$sofincoRecurring item=card name=cards}
				<a href="{$card.url|escape:'html'}&amp;recurring=1">
					<img src="{$card.image}" alt="{$card.card}" title="{$card.card}" /> {l s='Pay' mod='sofinco'} {l s='card in 3 times without fees' mod='sofinco'}
				</a>
			{/foreach}			
		</p>
	</div>
</div>
{/if}
