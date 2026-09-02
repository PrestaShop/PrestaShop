<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Language\Repository;

use Language;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class LanguageRepository extends AbstractObjectModelRepository
{
    public function get(LanguageId $languageId): Language
    {
        /** @var Language $language */
        $language = $this->getObjectModel(
            $languageId->getValue(),
            Language::class,
            LanguageNotFoundException::class
        );

        return $language;
    }
}
