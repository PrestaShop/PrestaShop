<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactException;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class EditContactCommand is responsible for editing contact data.
 */
class EditContactCommand extends AbstractContactCommand
{
    /**
     * @var ContactId
     */
    private $contactId;

    /**
     * @var string[]
     */
    private $localisedTitles;

    /**
     * @var Email
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
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param int $contactId
     *
     * @throws ContactException
     */
    public function __construct($contactId)
    {
        $this->contactId = new ContactId($contactId);
    }

    /**
     * @return ContactId
     */
    public function getContactId()
    {
        return $this->contactId;
    }

    /**
     * @return string[]
     */
    public function getLocalisedTitles()
    {
        return $this->localisedTitles;
    }

    /**
     * @param string[] $localisedTitles
     *
     * @return self
     *
     * @throws ContactConstraintException
     */
    public function setLocalisedTitles(array $localisedTitles)
    {
        if (!$this->assertIsNotEmptyAndContainsAllNonEmptyStringValues($localisedTitles)) {
            throw new ContactConstraintException(sprintf('Expected to have not empty titles array but received %s', var_export($localisedTitles, true)), ContactConstraintException::INVALID_TITLE);
        }

        foreach ($localisedTitles as $title) {
            if (!$this->assertIsGenericName($title)) {
                throw new ContactConstraintException(sprintf('Expected value %s to match given regex /^[^<>{}]*$/u but failed', var_export($title, true)), ContactConstraintException::INVALID_TITLE);
            }
        }

        $this->localisedTitles = $localisedTitles;

        return $this;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     *
     * @throws DomainConstraintException
     */
    public function setEmail($email)
    {
        $this->email = new Email($email);

        return $this;
    }

    /**
     * @return bool
     */
    public function isMessagesSavingEnabled()
    {
        return $this->isMessagesSavingEnabled;
    }

    /**
     * @param bool $isMessagesSavingEnabled
     *
     * @return self
     */
    public function setIsMessagesSavingEnabled($isMessagesSavingEnabled)
    {
        $this->isMessagesSavingEnabled = $isMessagesSavingEnabled;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @param string[] $localisedDescription
     *
     * @return self
     */
    public function setLocalisedDescription(array $localisedDescription)
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     *
     * @throws ContactConstraintException
     */
    public function setShopAssociation(array $shopAssociation)
    {
        if (!$this->assertArrayContainsAllIntegerValues($shopAssociation)) {
            throw new ContactConstraintException(sprintf('Given shop association %s must contain all integer values', var_export($shopAssociation, true)), ContactConstraintException::INVALID_SHOP_ASSOCIATION);
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
