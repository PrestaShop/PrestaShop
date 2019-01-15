<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Domain\Contact\DTO;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;

/**
 * Class EditableContact
 */
class EditableContact
{
    /**
     * @var ContactId
     */
    private $contactId;

    /**
     * @var array|string[]
     */
    private $localisedTitles;

    /**
     * @var string
     */
    private $email;

    /**
     * @var bool
     */
    private $isMessagesSavingEnabled;

    /**
     * @var string[]
     */
    private $localisedDescription;

    /**
     * @param int $contactId
     * @param string[] $localisedTitles
     * @param string $email
     * @param bool $isMessagesSavingEnabled
     * @param string[] $localisedDescription
     *
     * @throws ContactException
     */
    public function __construct(
        $contactId,
        array $localisedTitles,
        $email,
        $isMessagesSavingEnabled,
        $localisedDescription
    ) {
        $this->contactId = new ContactId($contactId);
        $this->localisedTitles = $localisedTitles;
//        todo: email value object?
        $this->email = $email;
        $this->isMessagesSavingEnabled = $isMessagesSavingEnabled;
        $this->localisedDescription = $localisedDescription;
    }

    /**
     * @return ContactId
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @return array|string[]
     */
    public function getLocalisedTitles()
    {
        return $this->localisedTitles;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isMessagesSavingEnabled()
    {
        return $this->isMessagesSavingEnabled;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }
}
