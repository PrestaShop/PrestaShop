<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form class is responsible to create a customer.
 */
class TypeaheadCustomerCollectionType extends CommonAbstractType
{
    protected $customerAdapter;

    /**
     * {@inheritdoc}
     *
     * @param object $customerAdapter
     */
    public function __construct($customerAdapter)
    {
        $this->customerAdapter = $customerAdapter;
    }

    /**
     * {@inheritdoc}
     *
     * Add the vars to the view
     * Inject collection customer
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['placeholder'] = $options['placeholder'];
        $view->vars['remote_url'] = $options['remote_url'];
        $view->vars['mapping_value'] = $options['mapping_value'];
        $view->vars['mapping_name'] = $options['mapping_name'];
        $view->vars['template_collection'] = $options['template_collection'];
        $view->vars['limit'] = $options['limit'];

        // if form is submitted, inject datas to display collection
        if (!empty($view->vars['value']) && !empty($view->vars['value']['data'])) {
            $collection = [];

            $i = 0;
            foreach ($view->vars['value']['data'] as $id) {
                if (!$id) {
                    continue;
                }
                $customer = $this->customerAdapter->getCustomer($id);
                $collection[] = [
                    'id' => $id,
                    'name' => $customer->firstname . ' ' . $customer->lastname . ' - ' . $customer->email,
                ];
                ++$i;

                // if collection length is up to limit, break
                if ($options['limit'] != 0 && $i >= $options['limit']) {
                    break;
                }
            }
            $view->vars['collection'] = $collection;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('data', 'Symfony\Component\Form\Extension\Core\Type\CollectionType', [
            'entry_type' => 'Symfony\Component\Form\Extension\Core\Type\HiddenType',
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'required' => false,
            'prototype' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'remote_url' => '',
            'mapping_value' => 'id',
            'mapping_name' => 'name',
            'placeholder' => '',
            'template_collection' => '',
            'limit' => 0,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'typeahead_customer_collection';
    }
}
