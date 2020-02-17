<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CategoryDeleteModeChoiceProvider.
 */
final class CategoryDeleteModeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $associateOnlyLabel = $this->translator->trans(
            'If they have no other category, I want to associate them with the parent category.',
            [],
            'Admin.Catalog.Notification'
        );

        $associateAndDisableLabel = sprintf(
            '%s %s',
            $this->translator->trans(
                'If they have no other category, I want to associate them with the parent category and turn them offline.',
                [],
                'Admin.Catalog.Notification'
            ),
            $this->translator->trans('(Recommended)', [], 'Admin.Catalog.Notification')
        );

        $deleteProductLabel = $this->translator->trans(
            'If they have no other category, I want to delete them as well.',
            [],
            'Admin.Catalog.Notification'
        );

        return [
            $associateAndDisableLabel => CategoryDeleteMode::ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE,
            $associateOnlyLabel => CategoryDeleteMode::ASSOCIATE_PRODUCTS_WITH_PARENT_ONLY,
            $deleteProductLabel => CategoryDeleteMode::REMOVE_ASSOCIATED_PRODUCTS,
        ];
    }
}
