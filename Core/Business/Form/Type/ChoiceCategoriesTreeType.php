<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Business\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * This form class is risponsible to create a nested category selector
 */
class ChoiceCategoriesTreeType extends AbstractType
{
    private $label;
    private $list;
    private $validList;
    private $multiple;

    /**
     * Constructor
     *
     * @param string $label The field label
     * @param array $list The nested array categories
     */
    public function __construct($label = '', $list = array(), $multiple = true)
    {
        $this->label = $label;
        $this->list = $list;
        $this->multiple = $multiple;
        $this->formatValidList($list);
    }

    /**
     * Create and format a valid array keys categories that can be validate by the choice SF2 cform component
     *
     * @param array $list The nested array categories
     */
    protected function formatValidList($list)
    {
        foreach ($list as $item) {
            $this->validList[$item['id_category']] = $item['name'];

            if (isset($item['children'])) {
                $this->formatValidList($item['children']);
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add the var choices to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices'] = $this->list;
        $view->vars['multiple'] = $this->multiple;

        //if form is submitted, inject categories values array to check or not each field
        if (!empty($view->vars['value']) && !empty($view->vars['value']['tree'])) {
            $view->vars['submitted_values'] = array_flip($view->vars['value']['tree']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds the form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('tree', 'choice', array(
            'label' => false,
            'choices' => $this->validList,
            'required' => false,
            'multiple'  => true,
            'expanded'  => true,
            'error_bubbling'  => true,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'choice_tree';
    }
}
