<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class AddContactCommand is responsible for adding the contact data.
 */
class AddContactCommand extends AbstractContactCommand
{
    /**
     * @var string[]
     */
    private $localisedTitles;

    /**
     * @var bool
     */
    private $isMessageSavingEnabled;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string[]
     */
    private $localisedDescription;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param string[] $localisedTitles - sample: [$langId => $title]
     * @param bool $isMessageSavingEnabled
     *
     * @throws ContactConstraintException
     */
    public function __construct(array $localisedTitles, $isMessageSavingEnabled)
    {
        $this->assertIsLocalisedTitleValid($localisedTitles);

        $this->localisedTitles = $localisedTitles;
        $this->isMessageSavingEnabled = $isMessageSavingEnabled;
    }

    /**
     * @return string[]
     */
    public function getLocalisedTitles()
    {
        return $this->localisedTitles;
    }

    /**
     * @return bool
     */
    public function isMessageSavingEnabled()
    {
        return $this->isMessageSavingEnabled;
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

    /**
     * @param array $localisedTitles
     *
     * @throws ContactConstraintException
     */
    private function assertIsLocalisedTitleValid(array $localisedTitles)
    {
        if (!$this->assertIsNotEmptyAndContainsAllNonEmptyStringValues($localisedTitles)) {
            throw new ContactConstraintException(sprintf('Expected to have not empty titles array but received %s', var_export($localisedTitles, true)), ContactConstraintException::INVALID_TITLE);
        }

        foreach ($localisedTitles as $title) {
            if (!$this->assertIsGenericName($title)) {
                throw new ContactConstraintException(sprintf('Expected value %s to match given regex /^[^<>{}]*$/u but failed', var_export($title, true)), ContactConstraintException::INVALID_TITLE);
            }
        }
    }
}
