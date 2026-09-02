<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\ExtraModulesType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * This listener dynamically updates the form depending on the product type, like
 * is it virtual, a pack, does it have combinations, ...
 */
class ProductTypeListener implements EventSubscriberInterface
{
    /**
     * @var HookInformationProvider
     */
    private $hookInformationProvider;

    /**
     * @param HookInformationProvider $hookInformationProvider
     */
    public function __construct(HookInformationProvider $hookInformationProvider)
    {
        $this->hookInformationProvider = $hookInformationProvider;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptProductForm',
            FormEvents::PRE_SUBMIT => 'adaptProductForm',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function adaptProductForm(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        // We need both initial and new types because we may need to keep some fields during the transition request
        $productType = $data['header']['type'];
        $initialProductType = $data['header']['initial_type'] ?? $productType;

        $registeredExtraModules = $this->hookInformationProvider->getRegisteredModulesByHookName(ExtraModulesType::HOOK_NAME);
        if (empty($registeredExtraModules)) {
            $form->remove('extra_modules');
        }

        if (ProductType::TYPE_COMBINATIONS === $productType) {
            $this->removeProductSuppliers($form);
            $this->removeStock($form);
        } elseif ($initialProductType !== ProductType::TYPE_COMBINATIONS) {
            $this->removeCombinations($form);
        }

        $optionsField = $form->get('options');

        // As this is also executed on pre-submit, we need to verify that suppliers exist before removing it
        if ($optionsField->has('suppliers')) {
            $suppliers = $optionsField->get('suppliers')->get('supplier_ids')->getConfig()->getOptions()['choices'];

            // Don't display the suppliers part if there are no suppliers
            if (count($suppliers) <= 0) {
                $this->removeSuppliers($form);
                $this->removeProductSuppliers($form);
            }
        }

        if (ProductType::TYPE_PACK !== $productType) {
            $this->removePackStockType($form);
            $this->removePack($form);
        }

        $this->removeStockMovementsIfNecessary($form, $data);

        if (ProductType::TYPE_VIRTUAL === $productType) {
            $this->removeShipping($form);
            // We don't remove the ecotax during the transition request because we could lose the ecotax data
            // and some part of the price with it
            if (ProductType::TYPE_VIRTUAL === $initialProductType) {
                $this->removeEcotax($form);
            }
        } else {
            $this->removeVirtualProduct($form);
        }

        $event->setData($data);
    }

    protected function removeCombinations(FormInterface $form): void
    {
        if ($form->has('options')) {
            $form->remove('combinations');
        }
    }

    protected function removeProductSuppliers(FormInterface $form): void
    {
        if ($form->has('options')) {
            $optionsForm = $form->get('options');

            if ($optionsForm->has('product_suppliers')) {
                $optionsForm->remove('product_suppliers');
            }
        }
    }

    protected function removeSuppliers(FormInterface $form): void
    {
        if ($form->has('options')) {
            $optionsForm = $form->get('options');

            if ($optionsForm->has('suppliers')) {
                $optionsForm->remove('suppliers');
            }
        }
    }

    protected function removeStock(FormInterface $form): void
    {
        $form->remove('stock');
    }

    protected function removePackStockType(FormInterface $form): void
    {
        if (!$form->has('stock')) {
            return;
        }
        $stock = $form->get('stock');
        $stock->remove('pack_stock_type');
    }

    protected function removePack(FormInterface $form): void
    {
        if (!$form->has('stock')) {
            return;
        }
        $stock = $form->get('stock');
        $stock->remove('packed_products');
    }

    protected function removeVirtualProduct(FormInterface $form): void
    {
        if (!$form->has('stock')) {
            return;
        }
        $stock = $form->get('stock');
        $stock->remove('virtual_product_file');
    }

    protected function removeStockMovementsIfNecessary(FormInterface $form, array $data): void
    {
        if (!$form->has('stock')) {
            return;
        }
        $stock = $form->get('stock');
        if (!$stock->has('quantities')) {
            return;
        }
        $quantities = $stock->get('quantities');
        if ($quantities->has('stock_movements') && empty($data['stock']['quantities']['stock_movements'])) {
            $quantities->remove('stock_movements');
        }
    }

    protected function removeShipping(FormInterface $form): void
    {
        $form->remove('shipping');
    }

    protected function removeEcotax(FormInterface $form): void
    {
        if (!$form->has('pricing')) {
            return;
        }
        $pricing = $form->get('pricing');
        if (!$pricing->has('retail_price')) {
            return;
        }
        $retailPrice = $pricing->get('retail_price');
        if ($retailPrice->has('ecotax_tax_excluded')) {
            $retailPrice->remove('ecotax_tax_excluded');
        }

        if ($retailPrice->has('ecotax_tax_included')) {
            $retailPrice->remove('ecotax_tax_included');
        }
    }
}
