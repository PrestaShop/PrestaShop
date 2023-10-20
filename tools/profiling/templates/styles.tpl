{**
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 *}
<style>
  #prestashop-profiling {
    max-width: 100%;
    padding: 20px;
  }

  .ps_back-office.page-sidebar #prestashop-profiling {
    margin-left: 210px;
  }
  .ps_back-office.page-sidebar-closed #prestashop-profiling {
    margin-left: 50px;
  }
  .ps_back-office #prestashop-profiling {
    clear: both;
    padding: 10px;
    margin-bottom: 50px;
  }

  #prestashop-profiling * {
    box-sizing:border-box;
    -moz-box-sizing:border-box;
  }

  #prestashop-profiling .table {
    width: 100%;
  }

  #prestashop-profiling .table td,
  #prestashop-profiling .table th {
    padding: 6.4px;
    padding: .4rem;
    vertical-align: top;
    border-top: 1px solid #bbcdd2;
    vertical-align: middle;
    text-align: left;
  }
  #prestashop-profiling .table th {
    padding-top: 0.625rem;
    padding-bottom: 0.625rem;
  }
  #prestashop-profiling .table thead th {
    border-bottom: .125rem solid #25b9d7;
  }

  #prestashop-profiling .table tfoot th {
    border-top: .125rem solid #25b9d7;
  }

  #prestashop-profiling .sortable thead th {
    cursor:pointer;
  }

  #prestashop-profiling .table td .pre {
    padding: 6px;
    margin-right: 10px;
    overflow: auto;
    display: block;
    color: #777;
    font-size: 12px;
    line-height: 1.42857;
    word-break: break-all;
    word-wrap: break-word;
    background-color: whitesmoke;
    border: 1px solid #cccccc;
    max-width: 960px;
  }

  #prestashop-profiling .row {
    clear: both;
    margin-bottom: 20px;
  }

  #prestashop-profiling .col-4 {
    float: left;
    padding: 0 10px;
    width: 33%;
  }

  @media (max-width: 1200px) {
    #prestashop-profiling .col-4 {
      width: 50%;
    }
  }
  @media (max-width: 600px) {
    #prestashop-profiling .col-4 {
      width: 100%;
    }
  }

  #prestashop-profiling .col-5 {
    float: left;
    padding: 0 10px;
    width: 40%;
  }

  @media (max-width: 1200px) {
    #prestashop-profiling .col-5 {
      width: 50%;
    }
  }
  @media (max-width: 600px) {
    #prestashop-profiling .col-5 {
      width: 100%;
    }
  }

  #prestashop-profiling .col-2 {
    float: left;
    padding: 0 10px;
    width: 20%;
  }

  @media (max-width: 1200px) {
    #prestashop-profiling .col-2 {
      width: 20%;
    }
  }
  @media (max-width: 600px) {
    #prestashop-profiling .col-2 {
      width: 100%;
    }
  }

  #profiling-links {
    display: flex;
    width: fit-content;
    position: fixed;
    top: 0;
    right: 0;
    transform: translateX(calc(100% - 38px));
    padding: 10px;
    background: #EEE;
    z-index: 10000;
    transition: transform ease-in-out .2s;
  }

  #profiling-links:hover {
    left: unset;
    transform: translateX(0);
  }

  #profiling-links span {
    writing-mode: vertical-rl;
    text-orientation: mixed;
  }

  #profiling-links ol {
    padding-left: 25px;
  }

  .success {
    color: green;
  }
  .danger {
    color: red;
  }
  .warning {
    color: #EF8B00;
  }
</style>

<script type="text/javascript" src="https://cdn.rawgit.com/drvic10k/bootstrap-sortable/1.11.2/Scripts/bootstrap-sortable.js"></script>
