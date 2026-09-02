<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Class Email is responsible for providing valid email value.
 */
class Email
{
    /**
     * @var int Maximum allowed length for email
     */
    public const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    public function __construct($email)
    {
        $this->assertEmailIsString($email);
        $this->assertEmailIsNotEmpty($email);
        $this->assertEmailDoesNotExceedAllowedLength($email);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->email;
    }

    /**
     * Check if given email is the same as current
     *
     * @param Email $email
     *
     * @return bool
     */
    public function isEqualTo(Email $email)
    {
        return strtolower($email->getValue()) === strtolower($this->getValue());
    }

    /**
     * Check that email is not an empty string
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    public function assertEmailIsNotEmpty($email)
    {
        if (0 === strlen($email)) {
            throw new DomainConstraintException('Email must not be empty', DomainConstraintException::INVALID_EMAIL);
        }
    }

    /**
     * Assert that email length does not exceed allowed value
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    private function assertEmailDoesNotExceedAllowedLength($email)
    {
        $email = html_entity_decode($email, ENT_COMPAT, 'UTF-8');

        if (self::MAX_LENGTH < mb_strlen($email, 'UTF-8')) {
            throw new DomainConstraintException(sprintf('Email is too long. Max allowed length is %s', self::MAX_LENGTH), DomainConstraintException::INVALID_EMAIL);
        }
    }

    /**
     * Assert email is of type string
     *
     * @param string $email
     *
     * @throws DomainConstraintException
     */
    private function assertEmailIsString($email)
    {
        if (!is_string($email)) {
            throw new DomainConstraintException('Email must be of type string', DomainConstraintException::INVALID_EMAIL);
        }
    }
}
