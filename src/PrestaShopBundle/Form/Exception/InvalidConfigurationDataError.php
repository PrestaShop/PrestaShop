<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Exception;

class InvalidConfigurationDataError
{
    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var int|null
     */
    private $languageId;

    /**
     * InvalidConfigurationDataError constructor.
     *
     * @param int $errorCode
     * @param string $fieldName
     * @param int|null $languageId
     */
    public function __construct(int $errorCode, string $fieldName, ?int $languageId = null)
    {
        $this->errorCode = $errorCode;
        $this->fieldName = $fieldName;
        $this->languageId = $languageId;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @return int|null
     */
    public function getLanguageId(): ?int
    {
        return $this->languageId;
    }
}
