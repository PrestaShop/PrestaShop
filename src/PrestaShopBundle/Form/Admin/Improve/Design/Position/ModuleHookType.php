<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Position;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Defines Improve > Design > Positions > Transplant | Edit module-hook form
 */
class ModuleHookType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $allCmsCategories;

    /**
     * @var bool
     */
    private $isMultiShopEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $allCmsCategories
     * @param $isMultiShopEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $allCmsCategories,
        $isMultiShopEnabled)
    {
        parent::__construct($translator, $locales);

        $this->allCmsCategories = $allCmsCategories;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page_category_id', MaterialChoiceTreeType::class, [
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ]);
    }
}
