<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language;

use Language;
use PrestaShop\PrestaShop\Core\Foundation\Version;
use PrestaShop\PrestaShop\Core\Language\Pack\LanguagePackInstallerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LanguagePack is responsible for the language pack actions regarding installation.
 */
final class LanguagePackInstaller implements LanguagePackInstallerInterface
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * LanguagePackInstaller constructor.
     *
     * @param TranslatorInterface $translator
     * @param Version $version
     */
    public function __construct(TranslatorInterface $translator, Version $version)
    {
        $this->version = $version;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadAndInstallLanguagePack($iso)
    {
        $freshInstall = empty(Language::getIdByIso($iso));
        $result = Language::downloadAndInstallLanguagePack($iso, $this->version->getSemVersion(), null, $freshInstall);

        if (false === $result) {
            return [
                $this->translator->trans(
                    'Fatal error: ISO code is not correct',
                    [],
                    'Admin.International.Notification'
                ),
            ];
        }

        if (is_array($result) && !empty($result)) {
            return $result;
        }

        return [];
    }
}
