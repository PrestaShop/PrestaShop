<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;
use Symfony\Contracts\Translation\TranslatorInterface;

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

    /**
     * @return TermsAndConditions
     */
    private function getDefaultTermsAndConditions()
    {
        $cms = new CMS((int) Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite);

        $termsAndConditions = new TermsAndConditions();
        $termsAndConditions
            ->setText(
                $this->translator->trans('I agree to the [terms of service] and will adhere to them unconditionally.', [], 'Shop.Theme.Checkout'),
                $link
            )
            ->setIdentifier('terms-and-conditions');

        return $termsAndConditions;
    }

    private function getConditionsToApprove()
    {
        $allConditions = [];

        // An array [module_name => module_output] will be returned
        $hookedConditions = Hook::exec('termsAndConditions', [], null, true);
        if (!is_array($hookedConditions)) {
            $hookedConditions = [];
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
        $reducedConditions = [];
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
