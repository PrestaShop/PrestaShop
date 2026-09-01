<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionShopFilterInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Standalone shop-association form shown on the definition view page — the ONE field of a
 * module-owned definition that remains editable from the BO (see the shop-association
 * carve-out in UpdateExtraPropertyDefinitionHandler).
 *
 * Owns the dynamic help text spelling out the fallback the merchant cannot guess: pass the
 * owning module's technical name via the module_name option and the help lists the module's
 * currently enabled stores; without it, the generic "all stores" wording applies.
 */
class ExtraPropertyDefinitionShopsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ExtraPropertyDefinitionShopFilterInterface $definitionShopFilter,
        private readonly ShopRepository $shopRepository,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('shop_association', ShopChoiceTreeType::class, [
            'label' => $this->trans('Store association', 'Admin.Global'),
            'help' => $this->resolveHelp($options['module_name']),
            'required' => false,
            // Override the type's context-shops default: an untouched field must submit
            // as "no restriction", never as a silent restriction to the current context.
            'default_empty_data' => [],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Technical name of the definition's owning module; null for core-owned definitions.
            'module_name' => null,
        ]);
        $resolver->setAllowedTypes('module_name', ['string', 'null']);
    }

    /**
     * The fallback applied when the association is left empty cannot be guessed from the
     * page, so the help text spells it out — including the module's current stores, the
     * live input of the module fallback (see ExtraPropertyDefinition::isAvailableForShops()).
     */
    private function resolveHelp(?string $moduleName): string
    {
        if (null === $moduleName || '' === $moduleName) {
            return $this->trans('Leave empty to make this property available in all stores.', 'Admin.Advparameters.Help');
        }

        $moduleShopIds = $this->definitionShopFilter->getModuleEnabledShopIds($moduleName);
        if ([] === $moduleShopIds) {
            return $this->trans('Leave empty to follow the module\'s store association.', 'Admin.Advparameters.Help');
        }

        return $this->trans(
            'Leave empty to follow the module\'s store association (currently: %stores%).',
            'Admin.Advparameters.Help',
            ['%stores%' => implode(', ', array_map(
                fn (int $shopId): string => $this->shopRepository->getShopName(new ShopId($shopId)),
                $moduleShopIds
            ))]
        );
    }
}
