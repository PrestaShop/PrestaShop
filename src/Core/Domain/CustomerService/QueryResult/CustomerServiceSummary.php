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

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Carries data about customer service summary: total threads and view url
 */
class CustomerServiceSummary
{
    /**
     * @var int
     */
    private $contactId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $totalThreads;

    /**
     * @var string
     */
    private $viewUrl;

    /**
     * @param int $contactId
     * @param string $name
     * @param string $description
     * @param int $totalThreads
     * @param string $viewUrl
     */
    public function __construct(
        int    $contactId,
        string $name,
        string $description,
        int    $totalThreads = 0,
        string $viewUrl = ''
    ) {
        $this->contactId = $contactId;
        $this->name = $name;
        $this->description = $description;
        $this->totalThreads = $totalThreads;
        $this->viewUrl = $viewUrl;
    }

    /**
     * @return int
     */
    public function getContactId(): int
    {
        return $this->contactId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getTotalThreads(): int
    {
        return $this->totalThreads;
    }

    /**
     * @return string
     */
    public function getViewUrl(): string
    {
        return $this->viewUrl;
    }

}
