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

namespace PrestaShop\PrestaShop\Core\QuickAccess;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\Hashing;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Generator that centralizes the generation/cleaning/fetching of quick accesses, so it can be used th same way in legacy
 * and symfony code.
 */
class QuickAccessGenerator
{
    /**
     * link to new product creation form
     */
    protected const NEW_PRODUCT_LINK = 'index.php/sell/catalog/products/new';

    /**
     * link to new product creation form for product v2
     */
    protected const NEW_PRODUCT_V2_LINK = 'index.php/sell/catalog/products/create';

    public function __construct(
        protected readonly LegacyContext $legacyContext,
        protected readonly LanguageContext $languageContext,
        protected readonly ShopContext $shopContext,
        protected readonly QuickAccessRepositoryInterface $quickAccessRepository,
        protected readonly UserProvider $userProvider,
        protected readonly TabRepository $tabRepository,
        protected readonly CsrfTokenManagerInterface $tokenManager,
        protected readonly EmployeeContext $employeeContext,
        private readonly Hashing $hashing,
        private readonly string $cookieKey,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    /**
     * Clean the saved quick link from base domain, index.ph and token to return its minimal form.
     *
     * @param string $savedUrl
     *
     * @return string
     */
    public function cleanQuickLink(string $savedUrl): string
    {
        $legacyEnvironment = stripos($savedUrl, 'controller');

        $patterns = [
            '#' . $this->legacyContext->getContext()->link->getBaseLink() . '#',
            '#' . basename(_PS_ADMIN_DIR_) . '/#',
            '/index.php/',
            '/_?token=[^&]+/',
        ];

        // If __PS_BASE_URI__ = '/', it destroys urls when is 'product/new' or 'modules/manage' (vhost for example)
        $baseUri = $this->shopContext->getBaseURI();
        if ('/' !== $baseUri) {
            $patterns[] = '#' . $baseUri . '#';
        }

        $url = preg_replace($patterns, '', $savedUrl);
        $url = trim($url, '?&/');

        return 'index.php' . (!empty($legacyEnvironment) ? '?' : '/') . $url;
    }

    public function getTokenizedQuickAccesses(): array
    {
        // Retrieve all quick accesses
        $quickAccesses = $this->quickAccessRepository->fetchAll(new LanguageId($this->languageContext->getId()));
        if (empty($quickAccesses)) {
            return [];
        }

        // Prepare quick accesses to render the component view properly.
        foreach ($quickAccesses as $index => &$quick) {
            // Initialise our Quick Access
            $quick['class'] = '';
            $cleanLink = $this->cleanQuickLink($quick['link']);

            // Special case for product link because it is bound to a modal, however all other links would deserve to be checked for permission
            if ($cleanLink === self::NEW_PRODUCT_LINK || $cleanLink === self::NEW_PRODUCT_V2_LINK) {
                if (!in_array('ROLE_MOD_TAB_ADMINPRODUCTS_CREATE', $this->userProvider->getUser()->getRoles())) {
                    // if employee has no access, we don't show product creation link,
                    // because it causes modal-related issues in product v2
                    unset($quickAccesses[$index]);
                    continue;
                }
                // We create new product v2 modal popup link
                $cleanLink = self::NEW_PRODUCT_V2_LINK;
                $quick['class'] = 'new-product-button';
            }

            // Preparation of the link to display in component view.
            $quick['link'] = '/' . basename(_PS_ADMIN_DIR_) . '/' . $cleanLink;

            // Add token if needed
            $quick['link'] = $this->getTokenizedUrl($quick['link']);
        }

        return $quickAccesses;
    }

    /**
     * Get tokenized url
     */
    protected function getTokenizedUrl(string $baseUrl): string
    {
        $separator = strpos($baseUrl, '?') ? '&' : '?';

        $symfonyLayoutEnabled = $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT);
        if ($symfonyLayoutEnabled && !str_contains('_token', $baseUrl)) {
            $baseUrl .= $separator . '_token=' . $this->tokenManager->getToken($this->userProvider->getUsername())->getValue();
        } else {
            preg_match('/controller=(\w*)/', $baseUrl, $adminTab);

            // If legacy link
            if (isset($adminTab[1]) && !str_contains('token', $baseUrl)) {
                $tokenSeed = $adminTab[1] . $this->tabRepository->findOneIdByClassName($adminTab[1]) . $this->employeeContext->getEmployee()?->getId();
                $baseUrl .= $separator . 'token=' . $this->hashing->hash($tokenSeed, $this->cookieKey);
            }

            // If symfony link
            if (!isset($adminTab[1]) && !str_contains('_token', $baseUrl)) {
                $baseUrl .= $separator . '_token=' . $this->tokenManager->getToken($this->userProvider->getUsername())->getValue();
            }
        }

        return $baseUrl;
    }
}
