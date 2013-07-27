<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
include_once(dirname(__FILE__).'../../../ReferralProgramModule.php');
include_once(dirname(__FILE__).'../../../referralprogram.php');
 
class ReferralprogramProgramModuleFrontController extends ModuleFrontController
{
	
	public function init()
	{
		if (!$this->context->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&back=modules/referralprogram/referralprogram-program.php');
		parent::init();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin(array('thickbox', 'idTabs'));
	}

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		
		// get discount value (ready to display)
		$discount_type = (int)(Configuration::get('REFERRAL_DISCOUNT_TYPE'));
		if ($discount_type == 1)
			$discount = Discount::display((float)(Configuration::get('REFERRAL_PERCENTAGE')), $discount_type, new Currency($this->context->currency->id));
		else
			$discount = Discount::display((float)(Configuration::get('REFERRAL_DISCOUNT_VALUE_'.(int)($this->context->currency->id))), $discount_type, new Currency($this->context->currency->id));

		$activeTab = 'sponsor';
		$error = false;
		
		// Mailing invitation to friend sponsor
		$invitation_sent = false;
		$nbInvitation = 0;
		if (Tools::isSubmit('submitSponsorFriends') AND Tools::getValue('friendsEmail') AND sizeof($friendsEmail = Tools::getValue('friendsEmail')) >= 1)
		{
			$activeTab = 'sponsor';
			if (!Tools::getValue('conditionsValided'))
				$error = 'conditions not valided';
			else
			{
				$friendsLastName = Tools::getValue('friendsLastName');
				$friendsFirstName = Tools::getValue('friendsFirstName');
				$mails_exists = array();
				foreach ($friendsEmail AS $key => $friendEmail)
				{
					$friendEmail = strval($friendEmail);
					$friendLastName = strval($friendsLastName[$key]);
					$friendFirstName = strval($friendsFirstName[$key]);
		
					if (empty($friendEmail) AND empty($friendLastName) AND empty($friendFirstName))
						continue;
					elseif (empty($friendEmail) OR !Validate::isEmail($friendEmail))
						$error = 'email invalid';
					elseif (empty($friendFirstName) OR empty($friendLastName) OR !Validate::isName($friendLastName) OR !Validate::isName($friendFirstName))
						$error = 'name invalid';
					elseif (ReferralProgramModule::isEmailExists($friendEmail) OR Customer::customerExists($friendEmail))
						$mails_exists[] = $friendEmail;
					else
					{
						$referralprogram = new ReferralProgramModule();
						$referralprogram->id_sponsor = (int)($this->context->customer->id);
						$referralprogram->firstname = $friendFirstName;
						$referralprogram->lastname = $friendLastName;
						$referralprogram->email = $friendEmail;
						if (!$referralprogram->validateFields(false))
							$error = 'name invalid';
						else
						{
							if ($referralprogram->save())
							{
								if (Configuration::get('PS_CIPHER_ALGORITHM'))
									$cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
								else
									$cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);
								$vars = array(
									'{email}' => strval($this->context->customer->email),
									'{lastname}' => strval($this->context->customer->lastname),
									'{firstname}' => strval($this->context->customer->firstname),
									'{email_friend}' => $friendEmail,
									'{lastname_friend}' => $friendLastName,
									'{firstname_friend}' => $friendFirstName,
									'{link}' => Context::getContext()->link->getPageLink('authentication', true, Context::getContext()->language->id, 'create_account=1&sponsor='.urlencode($cipherTool->encrypt($referralprogram->id.'|'.$referralprogram->email.'|')), false),
									'{discount}' => $discount);
								Mail::Send((int)$this->context->language->id, 'referralprogram-invitation', Mail::l('Referral Program', (int)$this->context->language->id), $vars, $friendEmail, $friendFirstName.' '.$friendLastName, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/../../mails/');
								$invitation_sent = true;
								$nbInvitation++;
								$activeTab = 'pending';
							}
							else
								$error = 'cannot add friends';
						}
					}
					if ($error)
						break;
				}
				if ($nbInvitation > 0)
					unset($_POST);
				//Not to stop the sending of e-mails in case of doubloon
				if(sizeof($mails_exists))
					$error = 'email exists';
			}
		}
		
		// Mailing revive
		$revive_sent = false;
		$nbRevive = 0;
		if (Tools::isSubmit('revive'))
		{
			$activeTab = 'pending';
			if (Tools::getValue('friendChecked') AND sizeof($friendsChecked = Tools::getValue('friendChecked')) >= 1)
			{
				foreach ($friendsChecked as $key => $friendChecked)
				{
					if (ReferralProgramModule::isSponsorFriend((int)($this->context->customer->id), (int)($friendChecked)))
					{
					if (Configuration::get('PS_CIPHER_ALGORITHM'))
						$cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
					else
						$cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);
					$referralprogram = new ReferralProgramModule((int)($key));
					$vars = array(
						'{email}' => $this->context->customer->email,
						'{lastname}' => $this->context->customer->lastname,
						'{firstname}' => $this->context->customer->firstname,
						'{email_friend}' => $referralprogram->email,
						'{lastname_friend}' => $referralprogram->lastname,
						'{firstname_friend}' => $referralprogram->firstname,
						'{link}' => Context::getContext()->link->getPageLink('authentication', true, Context::getContext()->language->id, 'create_account=1&sponsor='.urlencode($cipherTool->encrypt($referralprogram->id.'|'.$referralprogram->email.'|')), false),
						'{discount}' => $discount
					);
					$referralprogram->save();
					Mail::Send((int)$this->context->language->id, 'referralprogram-invitation', Mail::l('Referral Program', (int)$this->context->language->id), $vars, $referralprogram->email, $referralprogram->firstname.' '.$referralprogram->lastname, strval(Configuration::get('PS_SHOP_EMAIL')), strval(Configuration::get('PS_SHOP_NAME')), NULL, NULL, dirname(__FILE__).'/../../mails/');
					$revive_sent = true;
					$nbRevive++;
				}
			}
			}
			else
				$error = 'no revive checked';
		}
		
		$customer = new Customer((int)($this->context->customer->id));
		$stats = $customer->getStats();
		
		$orderQuantity = (int)(Configuration::get('REFERRAL_ORDER_QUANTITY'));
		$canSendInvitations = false;

		if ((int)($stats['nb_orders']) >= $orderQuantity)
			$canSendInvitations = true;
		
		// Smarty display
		$this->context->smarty->assign(array(
			'activeTab' => $activeTab,
			'discount' => $discount,
			'orderQuantity' => $orderQuantity,
			'canSendInvitations' => $canSendInvitations,
			'nbFriends' => (int)(Configuration::get('REFERRAL_NB_FRIENDS')),
			'error' => $error,
			'invitation_sent' => $invitation_sent,
			'nbInvitation' => $nbInvitation,
			'pendingFriends' => ReferralProgramModule::getSponsorFriend((int)($this->context->customer->id), 'pending'),
			'revive_sent' => $revive_sent,
			'nbRevive' => $nbRevive,
			'subscribeFriends' => ReferralProgramModule::getSponsorFriend((int)($this->context->customer->id), 'subscribed'),
			'mails_exists' => (isset($mails_exists) ? $mails_exists : array())
		));
		$this->setTemplate('program.tpl');
	}
}