<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form type is used to display modules in an extra tab that regroup the modules implementing the displayAdminProductsExtra
 * hook. This is not the recommended way to integrate in the product page anymore but we keep it for backward compatibility.
 */
class ExtraModulesType extends TranslatorAwareType
{
    public const HOOK_NAME = 'displayAdminProductsExtra';

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var ModuleDataProvider
     */
    private $moduleDataProvider;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        HookDispatcherInterface $hookDispatcher,
        ModuleDataProvider $moduleDataProvider,
        ModuleRepository $moduleRepository
    ) {
        parent::__construct($translator, $locales);
        $this->hookDispatcher = $hookDispatcher;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $extraModules = $this->renderHooksArray($options['product_id']);
        $view->vars['extraModules'] = $extraModules;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => $this->trans('Modules', 'Admin.Catalog.Feature'),
                'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/extra_modules.html.twig',
                'required' => false,
            ])
            ->setRequired('product_id')
            ->setAllowedTypes('product_id', 'int')
        ;
    }

    /**
     * This method is here to simulate the previous call of renderhooksarray('displayAdminProductsExtra', { 'id_product': productId })
     * so it is basically a copy (slightly adapted) of the code from HookExtension::renderHooksArray method.
     */
    protected function renderHooksArray(int $productId): array
    {
        // The call to the render of the hooks is encapsulated into a ob management to avoid any call of echo from the
        // modules.
        ob_start();
        $renderedHook = $this->hookDispatcher->dispatchRenderingWithParameters(
            self::HOOK_NAME,
            [
                'id_product' => $productId,
            ]
        );
        $renderedHook->outputContent();
        ob_end_clean();

        $extraModules = [];
        foreach ($renderedHook->getContent() as $module => $hookRender) {
            $moduleAttributes = $this->moduleRepository->getModule($module)->getAttributes();
            $extraModules[] = [
                'id' => $module,
                'name' => $this->moduleDataProvider->getModuleName($module),
                'content' => $hookRender,
                'attributes' => $moduleAttributes->all(),
            ];
        }

        return $extraModules;
    }
}
