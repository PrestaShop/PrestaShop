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

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\Exception\CustomerException;

/**
 * Searchers for customers by phrases matching customer's first name, last name, email and id
 */
class SearchCustomers
{
    /**
     * @var string[]
     */
    private $phrases;

    /**
     * @param string[] $phrases
     */
    public function __construct(array $phrases)
    {
        $this->assertPhrasesAreNotEmpty($phrases);

        $this->phrases = $phrases;
    }

    /**
     * @return string[]
     */
    public function getPhrases()
    {
        return $this->phrases;
    }

    /**
     * @param string[] $phrases
     */
    private function assertPhrasesAreNotEmpty(array $phrases)
    {
        if (empty($phrases)) {
            throw new CustomerException('Phrases cannot be empty when searching customers.');
        }
    }
}
