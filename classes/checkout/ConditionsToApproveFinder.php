<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;

class ConditionsToApproveFinderCore
{
    private $translator;
    private $context;

    public function __construct(
        Context $context,
        TranslatorInterface $translator
    ) {
        $this->context = $context;
        $this->translator = $translator;
    }

    private function getDefaultTermsAndConditions()
    {
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool) Configuration::get('PS_SSL_ENABLED'));

        $termsAndConditions = new TermsAndConditions();
        $termsAndConditions
            ->setText(
                $this->translator->trans('I agree to the [terms of service] and will adhere to them unconditionally.', array(), 'Shop.Theme.Checkout'),
                $link
            )
            ->setIdentifier('terms-and-conditions')
        ;

        return $termsAndConditions;
    }

    private function getConditionsToApprove()
    {
        $allConditions = array();
        $hookedConditions = Hook::exec('termsAndConditions', array(), null, true);
        if (!is_array($hookedConditions)) {
            $hookedConditions = array();
        }
        foreach ($hookedConditions as $hookedCondition) {
            if ($hookedCondition instanceof TermsAndConditions) {
                $allConditions[] = $hookedCondition;
            } elseif (is_array($hookedCondition)) {
                foreach ($hookedCondition as $hookedConditionObject) {
                    if ($hookedConditionObject instanceof TermsAndConditions) {
                        $allConditions[] = $hookedConditionObject;
                    }
                }
            }
        }

        if (Configuration::get('PS_CONDITIONS')) {
            array_unshift($allConditions, $this->getDefaultTermsAndConditions());
        }

        /*
         * If two TermsAndConditions objects have the same identifier,
         * the one at the end of the list overrides the first one.
         * This allows a module to override the default checkbox
         * in a consistent manner.
         */
        $reducedConditions = array();
        foreach ($allConditions as $condition) {
            if ($condition instanceof TermsAndConditions) {
                $reducedConditions[$condition->getIdentifier()] = $condition;
            }
        }

        return $reducedConditions;
    }

    public function getConditionsToApproveForTemplate()
    {
        return array_map(function (TermsAndConditions $condition) {
            return $condition->format();
        }, $this->getConditionsToApprove());
    }
}
