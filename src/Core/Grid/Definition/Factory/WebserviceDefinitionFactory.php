<?php


namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;

/**
 * Class WebserviceDefinitionFactory is responsible for creating grid definition for Webservice grid
 */
class WebserviceDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $statusChoices;

    /**
     * @var string
     */
    private $resetActionUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    public function __construct(
        FormChoiceProviderInterface $statusChoices,
        $resetActionUrl,
        $redirectionUrl
    ) {
        $this->statusChoices = $statusChoices;
        $this->resetActionUrl = $resetActionUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'webservice';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Webservice', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_field' => 'id_webservice_account'
                ])
            )
            ->add((new DataColumn('key'))
                ->setName($this->trans('Key', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'key'
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Key description', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'description'
                ])
            )
            ->add((new DataColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active'
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
            );
    }

    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('key', TextType::class))
                ->setTypeOptions([
                    'required' => false
                ])
                ->setAssociatedColumn('key')
            )
            ->add((new Filter('description', TextType::class))
                ->setTypeOptions([
                    'required' => false
                ])
                ->setAssociatedColumn('description')
            )
            ->add((new Filter('active', ChoiceType::class))
                ->setTypeOptions([
                    'required' => false,
                    'choices' => $this->statusChoices->getChoices(),
                    'choice_translation_domain' => false,
                ])
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'attr' => [
                        'data-url' => $this->resetActionUrl,
                        'data-redirect' => $this->redirectionUrl
                    ]
                ])
                ->setAssociatedColumn('actions')
            );
    }
}
