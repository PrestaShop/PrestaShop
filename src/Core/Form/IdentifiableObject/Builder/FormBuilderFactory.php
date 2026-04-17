<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider\FormOptionsProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;

/**
 * Creates new form builders which are used to get forms for identifiable objects.
 */
final class FormBuilderFactory implements FormBuilderFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var FormRegistryInterface
     */
    private $registry;

    /**
     * @param FormFactoryInterface $formFactory
     * @param HookDispatcherInterface $hookDispatcher
     * @param FormRegistryInterface $registry
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        HookDispatcherInterface $hookDispatcher,
        FormRegistryInterface $registry
    ) {
        $this->formFactory = $formFactory;
        $this->hookDispatcher = $hookDispatcher;
        $this->registry = $registry;
    }

    /**
     * @param string $formType
     * @param FormDataProviderInterface $dataProvider
     * @param FormOptionsProviderInterface|null $optionProvider
     *
     * @return FormBuilder
     */
    public function create(
        $formType,
        FormDataProviderInterface $dataProvider,
        ?FormOptionsProviderInterface $optionProvider = null
    ) {
        return new FormBuilder(
            $this->formFactory,
            $this->hookDispatcher,
            $dataProvider,
            $formType,
            $this->registry,
            $optionProvider
        );
    }
}
