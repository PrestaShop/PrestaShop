<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * This controller is used to preview mail templates from new theme. It uses the
 * same service used by the generator so it is a handy way to debug them.
 */
final class MailLayoutController extends FrameworkBundleAdminController
{
    /**
     * @param string $theme
     * @param string $layout
     * @param string $type
     * @param string $locale
     * @param string $module
     *
     * @return Response
     */
    public function previewAction($theme, $layout, $type, $locale = '', $module = '')
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale, $module);

        return new Response($renderedLayout);
    }

    /**
     * @param string $theme
     * @param string $layout
     * @param string $type
     * @param string $locale
     *
     * @return Response
     */
    public function rawAction($theme, $layout, $type, $locale = '')
    {
        $renderedLayout = $this->renderLayout($theme, $layout, $type, $locale);

        $response = new Response($renderedLayout, 200, [
            'Content-Type' => 'text/plain',
        ]);

        return $response;
    }

    /**
     * @param string $themeName
     * @param string $layoutName
     * @param string $type
     * @param string $locale
     * @param string $module
     *
     * @return string
     */
    private function renderLayout($themeName, $layoutName, $type, $locale = '', $module = '')
    {
        /** @var ThemeCatalogInterface $themeCatalog */
        $themeCatalog = $this->get('prestashop.core.mail_template.theme_catalog');
        /** @var ThemeInterface $theme */
        $theme = $themeCatalog->getByName($themeName);

        /** @var LayoutInterface $layout */
        $layout = null;
        /* @var LayoutInterface $layoutInterface */
        foreach ($theme->getLayouts() as $layoutInterface) {
            if ($layoutInterface->getName() == $layoutName
                && (empty($module) || $layoutInterface->getModuleName() == $module)
            ) {
                $layout = $layoutInterface;
                break;
            }
        }

        if (null === $layout) {
            throw new FileNotFoundException(sprintf(
                'Could not find layout %s%s in theme %s',
                empty($module) ? '' : $module . ':',
                $layoutName,
                $themeName
            ));
        }

        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = $this->get('prestashop.core.admin.lang.repository');
        if (empty($locale)) {
            $locale = $this->getContext()->language->locale;
        }
        /** @var LanguageInterface $language */
        $language = $languageRepository->getByLocale($locale);
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Could not find Language with locale %s', $locale));
        }

        /** @var MailTemplateRendererInterface $renderer */
        $renderer = $this->get('prestashop.core.mail_template.mail_template_renderer');

        switch ($type) {
            case MailTemplateInterface::HTML_TYPE:
                $renderedLayout = $renderer->renderHtml($layout, $language);
                break;
            case MailTemplateInterface::TXT_TYPE:
                $renderedLayout = $renderer->renderTxt($layout, $language);
                break;
            default:
                throw new NotFoundHttpException(sprintf(
                    'Requested type %s is not managed, please use one of these: %s',
                    $type,
                    implode(',', [MailTemplateInterface::HTML_TYPE, MailTemplateInterface::TXT_TYPE])
                ));
                break;
        }

        return $renderedLayout;
    }
}
