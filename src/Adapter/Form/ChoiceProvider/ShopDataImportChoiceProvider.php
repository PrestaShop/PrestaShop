<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Hook;
use Module;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides the data a new shop can import from another shop: shop association tables,
 * plus one entry per module hooked on actionShopDataDuplication.
 */
final class ShopDataImportChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getChoices(): array
    {
        $choices = [
            $this->translator->trans('Attribute groups', [], 'Admin.Advparameters.Feature') => 'attribute_group',
            $this->translator->trans('Available quantities for sale', [], 'Admin.Advparameters.Feature') => 'stock_available',
            $this->translator->trans('Brands', [], 'Admin.Global') => 'manufacturer',
            $this->translator->trans('Carriers', [], 'Admin.Shipping.Feature') => 'carrier',
            $this->translator->trans('Contact information', [], 'Admin.Advparameters.Feature') => 'contact',
            $this->translator->trans('Countries', [], 'Admin.Global') => 'country',
            $this->translator->trans('Currencies', [], 'Admin.Global') => 'currency',
            $this->translator->trans('Customer groups', [], 'Admin.Advparameters.Feature') => 'group',
            $this->translator->trans('Employees', [], 'Admin.Advparameters.Feature') => 'employee',
            $this->translator->trans('Features', [], 'Admin.Global') => 'feature',
            $this->translator->trans('Images', [], 'Admin.Global') => 'image',
            $this->translator->trans('Languages', [], 'Admin.Global') => 'lang',
            $this->translator->trans('Meta information', [], 'Admin.Advparameters.Feature') => 'meta_lang',
            $this->translator->trans('Module hooks', [], 'Admin.Advparameters.Feature') => 'hook_module',
            $this->translator->trans('Modules', [], 'Admin.Global') => 'module',
            $this->translator->trans('Pages', [], 'Admin.Design.Feature') => 'cms',
            $this->translator->trans('Product combinations', [], 'Admin.Advparameters.Feature') => 'product_attribute',
            $this->translator->trans('Products', [], 'Admin.Global') => 'product',
            $this->translator->trans('Stores', [], 'Admin.Global') => 'store',
            $this->translator->trans('Suppliers', [], 'Admin.Global') => 'supplier',
            $this->translator->trans('Tax rules groups', [], 'Admin.Advparameters.Feature') => 'tax_rules_group',
            $this->translator->trans('Webservice accounts', [], 'Admin.Advparameters.Feature') => 'webservice_account',
            $this->translator->trans('Zones', [], 'Admin.International.Feature') => 'zone',
        ];

        foreach (Hook::getHookModuleExecList('actionShopDataDuplication') ?: [] as $module) {
            $choices[Module::getModuleName($module['module'])] = 'Module' . ucfirst($module['module']);
        }
        ksort($choices);

        return $choices;
    }
}
