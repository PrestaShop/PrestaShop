<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using copy language form
 * in "Improve > International > Translations" page.
 */
final class CopyLanguageDataProvider implements FormDataProviderInterface
{
    /**
     * @var AddonRepositoryInterface
     */
    private $themeRepository;

    public function __construct(AddonRepositoryInterface $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->validate($data);
    }

    /**
     * {@inheritdoc}
     */
    private function validate(array $data)
    {
        $errors = [];

        if (!isset($data['copy_language']['from_language']) || !isset($data['copy_language']['to_language'])) {
            $errors[] =  [
                'key' => 'You must select two languages in order to copy data from one to another.',
                'domain' => 'Admin.International.Notification',
                'parameters' => [],
            ];
        } elseif (!isset($data['copy_language']['from_theme']) || !isset($data['copy_language']['to_theme'])) {
            $errors[] =  [
                'key' => 'You must select two themes in order to copy data from one to another.',
                'domain' => 'Admin.International.Notification',
                'parameters' => [],
            ];
        } elseif (
            $data['copy_language']['from_theme'] === $data['copy_language']['to_theme'] &&
            $data['copy_language']['from_language'] === $data['copy_language']['to_language']
        ) {
            $errors[] =  [
                'key' => 'There is nothing to copy (same language and theme).',
                'domain' => 'Admin.International.Notification',
                'parameters' => [],
            ];
        } else {
            $fromThemeFound = false;
            $toThemeFound = false;

            /** @var Theme $theme */
            foreach ($this->themeRepository->getList() as $theme) {
                // Checking if "From" theme exists by name
                if ($theme->getName() === $data['copy_language']['from_theme']) {
                    $fromThemeFound = true;
                }

                // Checking if "To" theme exists by name
                if ($theme->getName() === $data['copy_language']['to_theme']) {
                    $toThemeFound = true;
                }
            }

            if (!$fromThemeFound || !$toThemeFound) {
                $errors[] =  [
                    'key' => 'Theme(s) not found',
                    'domain' => 'Admin.International.Notification',
                    'parameters' => [],
                ];
            }
        }

        return $errors;
    }
}
