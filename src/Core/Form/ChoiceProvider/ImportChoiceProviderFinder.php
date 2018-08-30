<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class ImportChoiceProviderFinder finds the responsible import choice provider
 */
final class ImportChoiceProviderFinder implements FormChoiceProviderInterface
{
    /**
     * @var int the numeric representation of import entity (0-8)
     */
    private $importEntity;

    /**
     * @var array
     */
    private $importChoiceProviders;

    /**
     * @param int $importEntity
     * @param array $importChoiceProviders
     */
    public function __construct($importEntity, array $importChoiceProviders)
    {
        $this->importEntity = $importEntity;
        $this->importChoiceProviders = $importChoiceProviders;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return $this->findChoiceProvider()->getChoices();
    }

    /**
     * Finds the choice provider for the import entity
     *
     * @return FormChoiceProviderInterface
     */
    private function findChoiceProvider()
    {
        if (!isset($this->importChoiceProviders[$this->importEntity])) {
            throw new InvalidArgumentException("Choice provider does not exist for entity $this->importEntity.");
        }

        return $this->importChoiceProviders[$this->importEntity];
    }
}
