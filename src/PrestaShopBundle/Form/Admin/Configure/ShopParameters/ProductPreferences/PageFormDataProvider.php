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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Adapter\Product\PageConfiguration;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PageFormDataProvider implements FormDataProviderInterface
{
    public const ERROR_MUST_BE_NUMERIC_EQUAL_TO_ZERO_OR_HIGHER = 2;

    /**
     * @var PageConfiguration
     */
    private $configuration;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        PageConfiguration $configuration,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->configuration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->validate($data);

        return $this->configuration->updateConfiguration($data);
    }

    /**
     * Perform validation on form data before saving it.
     *
     * @param array $data
     *
     * @return void
     *
     * @throws DataProviderException
     * @throws TypeException
     */
    protected function validate(array $data): void
    {
        $errorCollection = new InvalidConfigurationDataErrorCollection();
        if (isset($data[PageType::FIELD_DISPLAY_LAST_QUANTITIES])) {
            $displayLastQuantities = $data[PageType::FIELD_DISPLAY_LAST_QUANTITIES];
            if (!is_numeric($displayLastQuantities) || 0 >= $displayLastQuantities) {
                $errorCollection->add(
                    new InvalidConfigurationDataError(
                        static::ERROR_MUST_BE_NUMERIC_EQUAL_TO_ZERO_OR_HIGHER,
                        PageType::FIELD_DISPLAY_LAST_QUANTITIES
                    )
                );
            }
        }

        if (!$errorCollection->isEmpty()) {
            throw new DataProviderException('Invalid product preferences page form', 0, null, $errorCollection);
        }
    }
}
