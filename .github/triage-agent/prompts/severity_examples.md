# Worked examples

Real issues from this repository, with the severity the maintainers
actually applied. Use them to calibrate the boundaries - especially
Critical vs Major, where the workaround question decides it.

## Critical — BO - After an upgrade to 8.0.0 - an exception is displayed when adding/editing an employee

### Describe the bug and add screenshots Previously reported here: https://github.com/PrestaShop/PrestaShop/pull/28127#pullrequestreview-996320973 sixth issue After an upgrade from a previous version to 8.0.0 using the autoupgrade v4.15.0. An exception is displayed on the employee page (Add/Edit) ### Expected behavior _No response_ ### Steps to reproduce 1. Install a previous version for example PS1787 2. Uninstall those modules (welcome mbo metrics facebook psxmarketingwithgoogle, checkout) 3. Instal the autoupgrade module v4.15.0 4. Upgrade to 8.0.0 using a local archive 5. After upgrade >Cl

## Critical — [BO] - An exception thrown when you try to access "edit your profile"

#### Describe the bug When you try to edit your profile as an admin, an exception will be thrown #### Expected behavior No exception #### Steps to Reproduce 1. **BO** > **CONFIGURE** > **Advanced Parameters** > **Team** 2. Try to edit your profile (employee) 3. See error => an exception is thrown 4. Try to edit another admin profile or any employee profile => no error 5. Try to edit your profile from **employee-dropdown** in the header (see attached screenshot n°1) 6. See error => an exception is thrown (see attached screenshot n°2) **Screenshot n°1** [screenshot] **Screenshot n°2** [screensho

## Critical — Error installing PrestaShop: Module not installed : ps_accounts

### Describe the bug and add screenshots I am getting an error when try to install any 1.7.x version of PrestaShop. Error: "Module not installed : ps_accounts". It doesn' matter if I try to install this using a docker image, or download the package from the website and using the installer. <img width="714" alt="Captura de pantalla 2021-12-02 a las 13 51 21" src="https://user-images.githubusercontent.com/64795062/144425696-e4efd945-7e45-4b45-b039-edcb1ead9de1.png"> <img width="1440" alt="Captura de pantalla 2021-12-02 a las 13 53 01" src="https://user-images.githubusercontent.com/64795062/14442

## Critical — Post install fails when fixture installation is disabled

### Describe the bug and add screenshots When you disable the "Install demonstration data" option, <img width="738" alt="2021-12-06 at 4 08 PM" src="https://user-images.githubusercontent.com/793712/144870393-0b2873b0-8013-4741-9d2f-96c282e9b17a.png"> the post install step fails <img width="677" alt="2021-12-06 at 4 12 PM" src="https://user-images.githubusercontent.com/793712/144871050-7eb48312-98f9-4de3-8b78-7fe43cab0162.png"> The code validates the installation of the demo data, which didn't happen. That's why the response comes unexpected and the error occurs. I'll submit a PR to fix this. #

## Critical — Inaccessible product if redirected to a deleted product

### Describe the bug and add attachments If you redirect a product with the seo settings and then delete the product that you have redirected to, the old product will not be accessible any more. ### Expected behavior If a product is deleted, products that where redirected to this product should still be accessible. ### Steps to reproduce 1. Go to a product 19 (demo_14) in Backoffice 2. Open SEO tab, set this to redirect to another product (302), select product 18 (demo_10) 3. save changes 4. Go back to product list. 5. Delete product 18 from the shop. 6. open product 19 ### PrestaShop version(

## Critical — [BO] Can't access view customer page

#### Describe the bug A clear and concise description of what went wrong. #### Expected behavior Explain what you expected to happen instead. #### Steps to Reproduce Steps to reproduce the behavior: 1. Go to BO customers page 2. Create new customer 3. Click on view customer 4. See error **Screenshots** [screenshot] #### Additional information * PrestaShop version: develop branch * PHP version: 7.4 (works on 7.3)

## Major — FO - Missing 404 for disabled category page

#### Describe the bug On the FO, when trying to access a disabled category, an error/exception is displayed instead of a 404 page (exception in debug mode, error message without debug mode). #### Expected behavior When accessing any disabled category page on FO we should have a 404 page and not an error. #### Steps to Reproduce Steps to reproduce the behavior: 1. Enable debug mode 2. Create a category 3. Disable the category 4. In FO, try to access the disabled category by URL : The following exception is displayed : [screenshot] On 1.7.7 we are redirected to the 404 page : [URL]/fr/index.php?

## Major — Generation of nightlies is broken on `develop` branch

### Describe the bug and add screenshots Docker image `prestashop/prestashop:nightly` cannot start properly at the moment, because there is no nightly release to download and install. It seems the workflow generating these images is broken for 17 days, and the bucket storing the artifacts has an automatic cleaning process that removes files older than 15(?) days. ### Expected behavior _No response_ ### Steps to reproduce * Run `php tools/build/CreateRelease.php --destination-dir=/tmp/ps-release` from the root folder of PrestaShop * The version won't be found and the generation will fail. ### P

## Major — BO - An exception is thrown when import a file with non .csv extension and download the csv created file 

### Describe the bug and add screenshots An exception is thrown when import a file with non .csv extension and download the csv created file [screenshot] 1786 and 1778 Uploading exception when download csv file .mp4… ### Expected behavior Only the uploaded files by the user are listed in the 'Choose from history / FTP'. The files generated from the import system are not listed in the 'Choose from history / FTP' and can't be downloadable. ### Steps to reproduce 1. Go to CONFIGURE > Advanced Parameters > Import 2. Choose a **non .csv** (eg.odt, xsl, xlst, etc) file by clicking in select a file t

## Major — BO - Orders page - Cannot add a voucher - the Add button is disabled

#### Describe the bug In the BO > Orders > Order details page, when we try to Add a discount, the Add button is disabled [screenshot] #### Steps to Reproduce Steps to reproduce the behavior: 1. Go to BO > Orders > Orders page 2. Open an order 3. Click Add discount 4. If it is Free shipping Fill the fields 5. See error: the Add button is always disabled 6. If It is a Percent Type or an Amount Type 7. See error: the Value Field is disabled [screenshot] #### Additional information * PrestaShop version: develop * PHP version: 7.4, 7.2

## Major — Legacy email templates broken - bad path

### Describe the bug and add attachments There is an issue in https://github.com/PrestaShop/PrestaShop/pull/39886. Missing slash, so the PR is not working. Not sure how @PrestaShop/qa-functional tested it. ### Expected behavior _No response_ ### Steps to reproduce 1. Create `aaa.html` and `aaa.txt` in `mails/fr`. 2. Edit any order status in BO. 3. See the template is mising. ### PrestaShop version(s) where the bug happened 9.0.x nightly ### How you installed PrestaShop _No response_ ### PHP version(s) where the bug happened _No response_ ### If your bug is related to a module, specify its name

## Major — Endpoint `POST /admin-api/attributes/attribute` : Unusable endpoint

### Describe the bug and add attachments I request the endpoint with expected data (check ⬇️) : <img width="718" height="508" alt="Image" src="https://github.com/user-attachments/assets/ff367a74-acb8-4f80-88ce-a2f20b076c73" /> The HTTP Code for the response is 500. I have this output : ```js { type: 'https://tools.ietf.org/html/rfc2616#section-10', title: 'An error occurred', status: 500, detail: 'Expected argument of type "array", "null" given', class: 'Symfony\\Component\\Validator\\Exception\\UnexpectedTypeException', trace: [ { namespace: '', short_class: '', class: '', type: '', function:

## Minor — Can't download a virtual product because "Expiration date has passed, you cannot download this product."

### Describe the bug and add attachments With Prestashop 8.1.1, I created a new virtual product and attached a file to download. The problem is that it won't download. https://github.com/PrestaShop/PrestaShop/assets/16019289/e414ffce-1c93-4d5d-934c-d1f58881e37d Here's the configuration of the virtual attached file : <img width="796" alt="Screenshot 2024-01-03 at 14 38 23" src="https://github.com/PrestaShop/PrestaShop/assets/16019289/2dadf42b-fbe2-4897-afc1-4c3aee7a63a3"> It only downloads correctly if I don't add the following : - Number of allowed downloads - Expiration date - Number of days 

## Minor — Cannot change the settings in the Performance section for the context of single-store

#### Describe the bug We are not able to change settings in Adv. Preferences -> Performance for the single store. That means that we cannot set, for example, different media servers. Page was working completely fine on the 1.7.7 version. For people using CDN servers or additional cache, it's a no-go for 1.7.8.0. IMO there's no reason to block it, ok, we don't have checkboxes, but the page should be fully functional anyway. #### Expected behavior We should be able to change these settings in the context of a single store. #### Steps to Reproduce Steps to reproduce the behavior: 1. Set up multi-

## Minor — BO - Multistore - PPV2 -  Some issues when duplicating a product for all stores by Build actions 

### Describe the bug and add attachments & expected behavior the wording :ok: the product should be duplicated to all the stores . ### Steps to reproduce 1. Go to BO > Shop parameters > General > Enable multistore 2. Go to BO > Advanced parameters > Multistore > Add two new shops (shop1) , (shop2) 3. go to catalog >products>shop1 >create a new product . 4. go to products ALL SHOPS > select the product created , click on Duplicate selection for all stores on BUILD ACTIONS > see the first issue : _name store have only the parent shop name_ :x: 5. go to shop2 > see the second issue : _there is no

## Minor — BO - multistore : bulk actions shops does not work

### Describe the bug and add attachments [Untitled_ Sep 22 2023 9_41 AM.webm](https://github.com/PrestaShop/PrestaShop/assets/111756615/3a5cf29d-1dcd-4947-b52b-f5291a693afc) ### Expected behavior [screenshot] ### Steps to reproduce Go to Advanced Parameters >Multistore Add new store and a new URL GO to multistore page and click on bulk actions see error : bulk action does not work and check box not displayed [screenshot] ### PrestaShop version(s) where the bug happened 8.1.1 & develop ### PHP version(s) where the bug happened 8.1 ### If your bug is related to a module, specify its name and its

## Minor — [Seo-url] New shops have wrong page titles and friendly URLs

### Describe the bug and add attachments I'm not sure it's the bug with the shop installation or only with translations (polish in my case). The problem is every new shop needs default urls fixed every time on **Preferences (Shop Parameters) -> Traffic**. Images below show: id - page_name - title - friendly url: 1. Brands are not translated [screenshot] 2. Sitemap uses title as the friendly url. [screenshot] 3. Authentication has wrong title (`Nazwa użytkownika` means `User's name`) [screenshot] 4. Non-ASCII character used in url (notice url is `zamówienie` instead of `zamowienie`) [screenshot

## Minor — On the order detail page, the hook displayOrderDetail can't access to the general variables like $urls

### Describe the bug and add screenshots When you use a module with the `displayOrderDetail` hook, you can't use general purpose variables as `$urls`. For example, you can use them inside the displayHome hook. ### Expected behavior Be able to access to theses variables. ### Steps to reproduce 1. Add a module with `displayOrderDetail` hook, fetching a template calling this : {$urls.base_url} --- 1. Install the attached module below 2. Go to `/index.php?controller=order-detail&id_order=1` 3. See this kind of error : `Warning: Undefined array key "urls" in /var/cache/dev/smarty/compile/37/8b/af/3

## Trivial — BO - the version of PrestaShop in the toolbar symfony is not correct

### Describe the bug and add screenshots In the BO > Migrated page, when the debug mode is enabled. The version of PrestaShop in the toolbar is incorrect. [screenshot] This issue is reported by @boubkerbribri ### Expected behavior It should be 8.0.0 ### Steps to reproduce 1. Enable debug mode 2. Go to BO > Any migrated page 3. In the toolbar Symfony 4. check the version PrestaShop ### PrestaShop version(s) where the bug happened develop ### PHP version(s) where the bug happened 7.3 ### If your bug is related to a module, specify its name and its version _No response_

## Trivial — Slow loading of the product edit page in a shop with a lot of duplicate names in the categories

### Describe the bug and add attachments I have a shop with many categories (over 40,000) and many duplicate names. When I go to the admin to edit a product, the system displays the product categories. To help us discriminate categories with a duplicate name, PrestaShop loads a breadcrumb to display the category name (so it displays "Peugeot > 108" and not just "108"). However, this system is too slow with so many categories with duplicate names. In my case, the product edit page took over 600 seconds to load. The issue comes from here: https://github.com/PrestaShop/PrestaShop/blob/23c159ccd62

## Trivial — Autoupgrade - New UI - Can't go on a minor/major version when you have local file

### Describe the bug and add attachments When you want to do an upgrade to minor/major version and you have some files on your folder download, it 'll not allow you to clic on the next step as you can see : https://github.com/user-attachments/assets/dbfabed1-83d3-4466-b1ea-63088bbbda78 ### Expected behavior I wish I could update to major/minor version when I have some files on download ### Steps to reproduce 1. Install a 8.0.4 2. Install the new version of Autoupgrade 3. Go to update assistant 4. Put some zip/archive in your [yourShopFolder]/admin-dev/autoupgrade/download 5. Clic on Update you

## Trivial — BO - WS - Text helper is confusing

### Describe the bug and add attachments The grey text helper should not mention "at least" as the length of the webservice can only be 32 characters. <img width="1100" alt="Screenshot 2023-02-14 at 17 57 31" src="https://user-images.githubusercontent.com/16019289/219675222-cd0e7b98-3be8-465a-80c0-cf9d12c099c1.png"> @l-delin what do you think ? ^^ ### Expected behavior The helptext displays "The key must be 32 characters long." ### Steps to reproduce 1. Go to BO > Advanced Parameters > Webservices 2. Add a new webservice key 3. See text helper ### PrestaShop version(s) where the bug happened 8

## Trivial — BO - bad display on Theme & Logo > Pages configuration

#### Describe the bug BO - bad display on Theme & Logo > Pages configuration #### Steps to Reproduce Steps to reproduce the behavior: 1. Go to admin > design > Theme & Logo > Pages configuration 2. See error **Screenshots** Using PrestaShop 1.7.7.x [screenshot] Using PrestaShop 1.7.8.x and Develop [screenshot] #### Additional information * PrestaShop version: 1.7.8.x and Develop * PHP version: 7.1

## Trivial — Chrome - FO - Quantity increment +1 when to we change quantity to 0 in cart with press enter key

#### Describe the bug In the shopping cart, when we change the quantity to 0 with **press enter Key** using Chrome. We have the quantity changes to 1 then to 2. And I remark that i put a quantity she's multiplied by 2. Is possible bug to js ? https://drive.google.com/file/d/1dNzXFy-HEoM3BxJGJ2-snIjId08ZYzbu/view?usp=sharing #### Expected behavior We have checked on the official demonstration website prestashop and we have found the same issue. #### Additional information * PrestaShop version: last version
