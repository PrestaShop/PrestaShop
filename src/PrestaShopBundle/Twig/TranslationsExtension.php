<?php
/**
 * 2007-2016 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

class TranslationsExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    public $translator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    private $container;
    private $router;

    public function __construct(ContainerInterface $container, RouterInterface $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getTranslationsTree', array($this, 'getTranslationsTree')),
            new \Twig_SimpleFunction('getTranslationsForms', array($this, 'getTranslationsForms')),
        );
    }

    /**
     * Returns concatenated edit translation forms.
     *
     * @param array $translationsTree
     *
     * @return string
     */
    public function getTranslationsForms(array $translationsTree)
    {
        $output = '';
        $viewProperties = $this->getSharedEditFormViewProperties();

        foreach ($translationsTree as $topLevelDomain => $tree) {
            $output .= $this->concatenateEditTranslationForm($tree, $viewProperties);
        }

        return $output;
    }

    public function concatenateEditTranslationForm($subtree, $viewProperties)
    {
        $output = '';
        $hasMessages = $this->hasMessages($subtree);

        if ($hasMessages) {
            list($camelizedDomain, $messages) = each($subtree['__messages']);

            foreach ($messages as $translationKey => $translation) {
                $viewProperties['camelized_domain'] = $camelizedDomain;
                $viewProperties['translation_key'] = $translationKey;
                $viewProperties['translation'] = $translation;

                $output .= $this->renderEditTranslationForm($viewProperties);
            }
        } else {
            foreach ($subtree as $tree) {
                $output .= $this->concatenateEditTranslationForm($tree, $viewProperties);
            }
        }

        if ($hasMessages && count($subtree) > 1) {
            unset($subtree['__messages']);
            $output .= $this->concatenateEditTranslationForm($subtree, $viewProperties);
        }

        return $output;
    }

    /**
     * Returns a tree of translations key values.
     *
     * @param array $translationsTree
     *
     * @return string
     */
    public function getTranslationsTree(array $translationsTree)
    {
        $output = '';
        end($translationsTree);
        list($lastTranslationDomain) = each($translationsTree);
        reset($translationTree);

        foreach ($translationsTree as $topLevelDomain => $tree) {
            $output .= $this->concatenateSubtreeHeader($topLevelDomain, $tree);

            if ($lastTranslationDomain !== $topLevelDomain) {
                $output .= '<hr />';
            }
        }

        return $output;
    }

    /**
     * @param $tree
     * @param int $level
     *
     * @return string
     */
    public function makeSubtree($tree, $level = 3)
    {
        $output = '';
        $messagesSubtree = $this->hasMessages($tree);

        if ($messagesSubtree) {
            list($camelizedDomain, $messagesTree) = each($tree['__messages']);

            $formIndex = 0;
            $pageIndex = 1;
            $itemsPerPage = 25;
            $output .= '<div class="page" data-status="active" data-page-index="1">';

            $viewProperties = $this->getSharedEditFormViewProperties();

            foreach ($messagesTree as $translationKey => $translation) {
                $viewProperties['camelized_domain'] = $camelizedDomain;
                $viewProperties['translation_key'] = $translationKey;
                $viewProperties['translation'] = $translation;

                $output .= $this->renderEditTranslationForm($viewProperties);

                $isLastPage = $formIndex + 1 === count($messagesTree);

                if ($isLastPage) {
                    $output .= '</div>';
                } elseif ((0 === $formIndex % $itemsPerPage) && ($formIndex > 0)) {
                    ++$pageIndex;

                    // Close div with page class
                    $output .= '</div>';
                    $output .= '<div class="page hide" data-status="inactive" data-page-index="'.$pageIndex.'">';
                }

                ++$formIndex;
            }

            // Close div with page class when no message is available
            if (count($messagesTree) === 0) {
                $output .= '</div>';
            }
        } else {
            foreach ($tree as $subdomain => $subtree) {
                $output .= $this->concatenateSubtreeHeader($subdomain, $subtree, $level);
            }
        }

        return $output;
    }

    /**
     * @return array
     */
    protected function getSharedEditFormViewProperties()
    {
        return array(
            'action' => $this->router->generate('admin_international_translations_edit'),
            'label_edit' => $this->translator->trans('Update', array(), 'Admin.Actions'),
            'label_reset' => $this->translator->trans('Reset', array(), 'Admin.Actions'),
            'notification_success' => $this->translator->trans('Translation successfully updated', array(),
                'Admin.International.Notification'),
            'notification_error' => $this->translator->trans('Translation unsuccessfully updated', array(),
                'Admin.International.Notification'),
        );
    }

    /**
     * @param $properties
     *
     * @return mixed|string
     */
    protected function renderEditTranslationForm($properties)
    {
        list($domain, $locale) = explode('.', $properties['camelized_domain']);
        $translationValue = $this->getTranslationValue($properties['translation']);
        $defaultTranslationValue = $this->getDefaultTranslationValue($properties['translation_key'], $domain, $locale,
            $translationValue);

        return $this->container->get('templating')->render('PrestaShopBundle:Admin:Translations/include/form-edit-message.html.twig', array(
                'default_translation_value' => $defaultTranslationValue,
                'domain' => $domain,
                'edited_translation_value' => $translationValue,
                'is_translated' => '' !== $translationValue,
                'action' => $properties['action'],
                'label_edit' => $properties['label_edit'],
                'label_reset' => $properties['label_reset'],
                'locale' => $locale,
                'notification_error' => $properties['notification_error'],
                'notification_success' => $properties['notification_success'],
                'translation_key' => $properties['translation_key'],
                'hash' => $this->getTranslationHash($domain, $properties['translation_key']),
            )
        );
    }

    protected function getTranslationHash($domain, $translationKey)
    {
        return md5($domain.$translationKey);
    }

    /**
     * @param $translationKey
     * @param $domain
     * @param $locale
     * @param $translationValue
     *
     * @return array
     */
    protected function getDefaultTranslationValue($translationKey, $domain, $locale, $translationValue)
    {
        $defaultTranslationValue = $this->translator->trans($translationKey, array(), $domain, $locale);

        // Extract default translation value from xliff files for reset
        if (is_array($translationValue)) {
            $defaultTranslationValue = $translationValue['xlf'];
        }

        return $defaultTranslationValue;
    }

    /**
     * @param $translation
     *
     * @return mixed
     */
    protected function getTranslationValue($translation)
    {
        return !empty($translation['db']) ? $translation['db'] : $translation['xlf'];
    }

    /**
     * @param $tree
     *
     * @return bool
     */
    protected function hasMessages($tree)
    {
        return array_key_exists('__messages', $tree);
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_translations_extension';
    }

    /**
     * @param $subdomain
     * @param $subtree
     * @param int $level
     *
     * @return string
     */
    protected function concatenateSubtreeHeader($subdomain, $subtree, $level = 2)
    {
        $hasMessagesSubtree = $this->hasMessages($subtree);
        $subject = $this->makeSubdomainPrefix($level).$subdomain;

        $id = null;
        if ($hasMessagesSubtree) {
            $id = $this->parseDomain($subtree);
        }

        $output = $this->tagSubject($subject, $level, $id);

        if ($hasMessagesSubtree) {
            $output .= $this->render('button-toggle-messages-visibility.html.twig', array(
                'label_show_messages' => $this->translator->trans('Show messages', array(), 'Admin.International.Feature'),
                'label_hide_messages' => $this->translator->trans('Hide messages', array(), 'Admin.International.Feature'),
            ));

            $output .= $this->getNavigation($this->parseDomain($subtree));
        }

        $output .= '<div class="col-offset">';
        $output .= $this->makeSubtree($subtree, $level + 1);
        $output .= '</div>';

        if ($hasMessagesSubtree) {
            $output .= $this->render('button-go-to-pagination-bar.html.twig', array(
                'domain' => $id,
                'label' => $this->translator->trans('Go to previous navigation menu', array(), 'Admin.International.Feature'),
            ));

            // Close div with translation-domain class
            $output .= '</div>';

            // A subtree with messages contains at least a subdomain
            if (count($subtree) > 1) {
                unset($subtree['__messages']);
                $output .= $this->concatenateSubtreeHeader($subdomain, $subtree, $level);
            }
        }

        return $output;
    }

    /**
     * @param $subtree
     *
     * @return mixed
     */
    protected function parseDomain($subtree)
    {
        list($camelizedDomain) = $subtree['__messages'];
        list($domain) = explode('.', $camelizedDomain);

        return $domain;
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function getNavigation($id)
    {
        return $this->render('pagination-bar.html.twig', array('page_id' => $id));
    }

    /**
     * @param $view
     * @param array $parameters
     *
     * @return mixed|string
     */
    protected function render($view, $parameters = array())
    {
        $viewsDirectory = __DIR__.'/../Resources/views/Admin/Translations/include';
        $viewPath = $viewsDirectory.'/'.$view;
        if (!file_exists($viewPath)) {
            $message = sprintf('A view ("%s") does not exist.', $viewPath);
            $this->logger->error($message);
        }

        $view = file_get_contents($viewPath);

        foreach ($parameters as $key => $value) {
            $view = str_replace('{{ '.$key.' }}', $value, $view);
        }

        return $view;
    }

    /**
     * @param $level
     *
     * @return string
     */
    protected function makeSubdomainPrefix($level)
    {
        $subdomainPrefix = '';
        if ($level > 1) {
            $subdomainPrefix = '<span class="separator">'.str_repeat(' > ', $level - 1).' </span>';
        }

        return $subdomainPrefix;
    }

    /**
     * @param $subject
     * @param $level
     * @param null $id
     *
     * @return string
     */
    protected function tagSubject($subject, $level, $id = null)
    {
        $openingTag = '';
        $closingTag = ' ';

        if (2 === $level) {
            $openingTag = '<h2>';
            $closingTag = '</h2>';
        }

        if ($id) {
            $openingTag = '<span id="_'.$id.'">';
            $closingTag = '</span>';

            if (2 === $level) {
                $openingTag = '<h2>'.$openingTag;
                $closingTag = $closingTag.'</h2>';
            }
        }

        return $openingTag.$subject.$closingTag;
    }
}
