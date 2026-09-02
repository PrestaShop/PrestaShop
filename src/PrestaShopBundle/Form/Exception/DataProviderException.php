<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;
use Throwable;

class DataProviderException extends DomainException
{
    /**
     * @var InvalidConfigurationDataErrorCollection
     */
    private $InvalidConfigurationDataErrors;

    public function __construct($message = '', $code = 0, ?Throwable $previous = null, ?InvalidConfigurationDataErrorCollection $InvalidConfigurationDataErrors = null)
    {
        parent::__construct($message, $code, $previous);
        $this->InvalidConfigurationDataErrors = $InvalidConfigurationDataErrors ?: new InvalidConfigurationDataErrorCollection();
    }

    /**
     * @return InvalidConfigurationDataErrorCollection
     */
    public function getInvalidConfigurationDataErrors(): InvalidConfigurationDataErrorCollection
    {
        return $this->InvalidConfigurationDataErrors;
    }
}
