<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Most FormType have an `empty_data` option, however this option is only used to populate
 * data on submit when it is absent, it doesn't allow to force a default value in the field
 * when it is rendered, and if you use the `data` option it overrides your input data in the
 * form.
 *
 * So if you want to force a default value you can use this transformer.
 * Example:
 *
 * $builder->add('price', MoneyType::class);
 * $builder->get('price')->addViewTransformer(new EmptyDataTransformer(0));
 *
 * This will force the 0 value in your input field.
 *
 * If you want model transformations to be applied (for example if you want the MoneyType
 * default number of decimals to be applied) you can do the same but apply the transformer
 * on model instead:
 *
 * $builder->add('price', MoneyType::class);
 * $builder->get('price')->addModelTransformer(new EmptyDataTransformer(0));
 *
 * This way the input will display 0.00000 in your form (depending on the `scale` you set).
 *
 * This transformer is particularly convenient when dealing with CollectionType because setting
 * the default value in the prototype quite complicated when your collection is empty at first.
 */
class DefaultEmptyDataTransformer implements DataTransformerInterface
{
    /**
     * @var mixed
     */
    private $emptyData;

    /**
     * @var mixed
     */
    private $viewEmptyData;

    /**
     * Be wary that the default data matches the expected type since it will be returned
     * as the data (if a float is expected $emptyData must float, not int).
     *
     * The second parameter $viewEmptyData is optional, it can be used though if you want to use a different value
     * in the form rendering and in the form handling/submitting.
     *
     * example:
     *  new DefaultEmptyDataTransformer(0, null) => the data will be interpreted/replaced by 0 during form handling
     * but null will be used for display, so the input will appear empty
     *
     * @param mixed $emptyData This value will be used in form view and submit in place of empty value
     * @param mixed $viewEmptyData This will be used only on the form view (optional)
     */
    public function __construct($emptyData, $viewEmptyData = null)
    {
        $this->emptyData = $emptyData;

        // We use the second parameter only if provided, default value is the same as emptyData
        // This trick is used to allow setting null value
        $this->viewEmptyData = 2 === func_num_args() ? $viewEmptyData : $emptyData;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        return empty($value) ? $this->viewEmptyData : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        return empty($value) ? $this->emptyData : $value;
    }
}
