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

namespace PrestaShopBundle\Repository;

use PrestaShop\PrestaShop\Core\Model\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Model\LanguageInterface;
use PrestaShop\PrestaShop\Core\Model\Repository\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\LangRepository as Repository;

class LanguageRepository implements LanguageRepositoryInterface
{
    private const ISO_CODE = 'isoCode';

    public const LOCALE = 'locale';

    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getLanguage(int $id): LanguageInterface
    {
        $language = $this->repository->find($id);

        if (!empty($language)) {
            return $language;
        }

        throw new LanguageNotFoundException(sprintf('Language with ID %d not found', $id));
    }

    public function getLanguageByIsoCode(string $isoCode): LanguageInterface
    {
        $language = $this->repository->searchLanguage(self::ISO_CODE, $isoCode);

        if (!empty($language)) {
            return $language;
        }

        throw new LanguageNotFoundException(sprintf('Language with iso code %s not found', $isoCode));
    }

    public function getLanguageByLocale(string $locale): LanguageInterface
    {
        $language = $this->repository->searchLanguage(self::LOCALE, $locale);

        if (!empty($language)) {
            return $language;
        }

        throw new LanguageNotFoundException(sprintf('Language with locale %s not found', $locale));
    }

    public function findAll(): array
    {
        $languages = $this->repository->findAll();

        if (!empty($languages)) {
            return $languages;
        }

        throw new LanguageNotFoundException('No languages were found');
    }

    public function findBy(array $filters = [], array $sortBy = [], ?int $limit = null, ?int $offset = null): array
    {
        $languages = $this->repository->findBy($filters, $sortBy, $limit, $offset);

        if (!empty($languages)) {
            return $languages;
        }

        throw new LanguageNotFoundException('No languages were found');
    }
}
