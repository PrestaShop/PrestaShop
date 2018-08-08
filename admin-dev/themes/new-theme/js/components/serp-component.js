/* 
 * 2007-2018 PrestaShop
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

import Vue from 'vue';
  
class SerpComponent {
    constructor() {
        this.startVueComponent();
    }

    /**
     * Start Vue app for displaying search engine result preview
     */
    startVueComponent() {
        var data = {
            title: '',
            url: '',
            description: '',
            default: {
                url: 'http://example.com/',
            }
        };

        this.app = new Vue({
            data: data,
            el: "#serp",
            methods: {
                setTitle(title) {
                    //var title = this.$refs.title.value;
                    if(title.length > 70) {
                        title = title.substring(0, 70) + ' ...';
                    }
                    this.title = title;
                },
                setUrl(url) {
                    //this.url = this.$refs.url.value || this.default.url;
                    this.url = url || this.default.url;
                },
                setDescription(description) {
                    //let description = this.$refs.description.value;
                    if(description.length > 150) {
                        description = description.substring(0, 150) + ' ...';
                    }
                    this.description = description;
                }
            }
        });
    }
}

export default SerpComponent;
