<?php

use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Core\Business\Checkout\TermsAndConditions;

class ConditionsToApproveFinderCore
{
    private $transator;
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
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));

        $termsAndConditions = new TermsAndConditions;
        $termsAndConditions
            ->setText(
                $this->translator->trans('I agree to the [terms of service] and will adhere to them unconditionally.', [], 'Checkout'),
                $link
            )
            ->setIdentifier('terms-and-conditions')
        ;

        return $termsAndConditions;
    }

    private function getConditionsToApprove()
    {
        $allConditions = Hook::exec('termsAndConditions', [], null, true);
        if (!is_array($allConditions)) {
            $allConditions = [];
        }

        array_unshift($allConditions, $this->getDefaultTermsAndConditions());

        /**
         * If two TermsAndConditions objects have the same identifier,
         * the one at the end of the list overrides the first one.
         * This allows a module to override the default checkbox
         * in a consistent manner.
         */
        $reducedConditions = [];
        foreach ($allConditions as $condition) {
            $reducedConditions[$condition->getIdentifier()] = $condition;
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
