[33mcommit 689ba2e80537af7fbb7d356e36ccd403aa55fb54[m[33m ([m[1;36mHEAD -> [m[1;32mm/product/combination-stock[m[33m, [m[1;31morigin/m/product/combination-stock[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Dec 2 11:45:51 2020 +0200

     WIP introduce UpdateCombinationStockCommand

[33mcommit f016f957e0bded84972cee8aa137ed0ab61688d6[m[33m ([m[1;31mps/develop[m[33m, [m[1;31morigin/develop[m[33m, [m[1;31morigin/HEAD[m[33m, [m[1;32mdevelop[m[33m)[m
Merge: 1e2e5aa981 589a4873d3
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Tue Dec 1 19:47:23 2020 +0100

    Merge pull request #21309 from PierreRambaud/fix/21054
    
    Restore See More & See less for the Module Manager

[33mcommit 1e2e5aa9812e05ae7a4a1d9d6f9db6d27a900dbd[m
Merge: faad4976a2 dc89cfafd1
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Tue Dec 1 16:25:04 2020 +0100

    Merge pull request #21234 from JevgenijVisockij/feature/simplify-customer-preferences
    
    Simplified customer preferences

[33mcommit faad4976a2c907e34f149af8cad272963815421e[m
Merge: 804b1efaee 5f00eab738
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Tue Dec 1 15:51:34 2020 +0100

    Merge pull request #21939 from NeOMakinG/issue21932
    
    Fix number increase and validate button position on stock page

[33mcommit 804b1efaeed464d213e098c437a04045acf55f96[m
Merge: 8449a186f6 3adb714e45
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Tue Dec 1 14:29:16 2020 +0100

    Merge pull request #22132 from zuk3975/m/product/combination-update
    
    Add UpdateCombinationOptionsCommand [product page migration]

[33mcommit 8449a186f6bed01fde863f32669eb6948a1949f7[m
Merge: f973b862a2 9d2709c052
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Tue Dec 1 13:42:50 2020 +0100

    Merge pull request #22102 from NeOMakinG/issue-13061
    
    Fix payment layout broken on small screens

[33mcommit 3adb714e45ea4f49021366c5bee712d78f5ee8d6[m[33m ([m[1;31morigin/m/product/combination-update[m[33m, [m[1;32mm/product/combination-update[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Dec 1 14:12:52 2020 +0200

     rely on StockAvailable and do not fallback + add todo reminder

[33mcommit f973b862a2ae64581ac78f69150bea9912bd852d[m
Merge: 162fbb82a8 04d00852e2
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Tue Dec 1 11:05:11 2020 +0100

    Merge pull request #21972 from NeOMakinG/issue21971
    
    Change wrongs growls used into success one

[33mcommit 593b7c7606112f9e4aa98fa82cdb36581a94db9f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 15:53:21 2020 +0200

     implement new sql query for combination stock_available

[33mcommit a03224e3daab87e950152699916c7f0cc8525e7a[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 15:40:20 2020 +0200

     get combination quantity from stock available

[33mcommit 23117564bf9a10ae241f19d8fb7731973b2c3b11[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 15:27:35 2020 +0200

     remove id reference from error message when adding object model

[33mcommit 859e049199389dcaf7c21515415ab417ae2fe085[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 14:13:47 2020 +0200

     use property accessor to reduce code

[33mcommit e7298a2acac3cd66adfa88d521c9079e704993f7[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 13:39:00 2020 +0200

     rename query handler interface and minor fixes

[33mcommit 943585c12b3250286f86339e65e6654a4f6137aa[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 30 12:14:10 2020 +0200

     clear cache before featureg

[33mcommit 9d2709c0528f05cb661aa2c18208216ce6c6041c[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Mon Nov 30 10:27:54 2020 +0100

    Add overflow scroll on tablet view

[33mcommit c67a0d55d003589198611d7020dc904b2b1ca006[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 16:47:43 2020 +0200

     do not catch exception in behat as its not used

[33mcommit 9691c12b3c5d3835d7783608a79560ebddb8b01f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 16:30:33 2020 +0200

     force default combination to always exist when generating

[33mcommit 2e05e6b614d2a834f416f56710583d9ad67b8b29[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 15:36:55 2020 +0200

     add unit test for Upc

[33mcommit 0a4efc312e294e91e4e9df62af82370a4688e11d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 15:27:50 2020 +0200

     add Reference unit test

[33mcommit fd494e8e954f12715b162b2352ce829462f13284[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 15:20:27 2020 +0200

     unit test for ISBN

[33mcommit 5e15675a133c8dac815afafa3b252d45f1c869b3[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 15:16:10 2020 +0200

     add ean13 unit test

[33mcommit c677b2f93d88bd73b6de560ff4975f818889e491[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 15:02:16 2020 +0200

     implement scenarios for update_combinatio_options

[33mcommit b803f641a2e72fbca8805eefe9574c4c1044c07b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 13:58:50 2020 +0200

     split combination contexts

[33mcommit 0aeb44762c0402f9bed3282d1d807f439beca36b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Sat Nov 28 13:48:45 2020 +0200

     adjust combination listing scenario

[33mcommit 4db436804921e5b53eeb25b671d7cc1dc334b086[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 27 20:04:57 2020 +0200

     implement GetCombinationForEditingHandler

[33mcommit e88debca969f92e3856e6c69376a726b21d39190[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 27 19:55:19 2020 +0200

     introduce CombinationOptions DTO

[33mcommit 438f23debd630cc40cc1e3725226bf4a6d3e7fd1[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 26 21:37:10 2020 +0200

     implement EditableCombinationsForListing and related handler

[33mcommit 162fbb82a8ef80d8db6652d4a1c4769b7a6dc422[m
Merge: 4ceb007c08 56b615857d
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Fri Nov 27 16:02:56 2020 +0100

    Merge pull request #22061 from NeOMakinG/issue22033
    
    Add select2 to the import localization select

[33mcommit 4ceb007c086d92534adc88a7245169af557ca2be[m
Merge: 4494926760 84d55f0a04
Author: Simon G <49909275+SimonGrn@users.noreply.github.com>
Date:   Fri Nov 27 16:02:30 2020 +0100

    Merge pull request #22019 from nesrineabdmouleh/functionalSearchBulkActions
    
    Add test 'Search bulk actions'

[33mcommit 449492676076edc3a90cfc8737e39d3cbacd8143[m
Merge: 2710b48a62 42fa9b32e4
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 26 18:42:29 2020 +0100

    Merge pull request #22027 from NeOMakinG/issue20093
    
    Remove duplicate id on product-list-bottom

[33mcommit baed97a31fe31f45539d09f8bb68cab8654d301a[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 26 18:43:28 2020 +0200

     move combinations behat features to separate dir

[33mcommit e4cd6fdd296d7295f1ae88e1d8df9224ffca86ba[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 26 18:21:02 2020 +0200

     implement UpdateCombinationOptionsHandler

[33mcommit 2710b48a62010ce12a9d6ebfd47ff83fc7b2822b[m
Merge: e47d51cdd3 c0f7ec948d
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Thu Nov 26 14:09:59 2020 +0100

    Merge pull request #20518 from zuk3975/m/product/generate-combo-command
    
    Add GenerateProductCombinationsCommand

[33mcommit e47d51cdd3e943fb2888c17ee049752516ed5201[m
Merge: 93f08c1e24 4c8fbe5c67
Author: atomiix <thomas.baccelli@prestashop.com>
Date:   Thu Nov 26 11:45:06 2020 +0100

    Merge pull request #21582 from okom3pom/okom3pom-patch-13
    
    Mark parameter deprecated for a future version

[33mcommit c0f7ec948dd6ea2239b674c7c0d24c451d701a0e[m[33m ([m[1;31morigin/m/product/generate-combo-command[m[33m, [m[1;32mm/product/generate-combo-command[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 26 11:13:39 2020 +0200

     use false instead of 0

[33mcommit 93f08c1e248ecc4dbbf76a029b8875c5de71312d[m
Merge: a6c0d3ad4d bf04c93edf
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 26 10:02:06 2020 +0100

    Merge pull request #22087 from PierreRambaud/fix/15873
    
    Do not generate the data variable if it's not needed in PaymentModule

[33mcommit 91420bf8af07f56f66e91a5eb42b1b582613afa0[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Wed Nov 25 15:44:35 2020 +0100

    Fix payment layout broken on small screens

[33mcommit d9021abf90775dcf8867db67fe8bf2b997f163a5[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 25 16:22:56 2020 +0200

     use combinationRepository->create instead of add. Remove duplicated assertion of combination existence & add todo for db transaction handling'

[33mcommit 535b73b4af4dfd3ead8808fb4c34ad0210848709[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 25 16:02:39 2020 +0200

     adjust behat after rebase of new multilang fields syntax

[33mcommit 1c21cb50a03f2350deaba3be1c3fd5bea3e8228a[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 25 11:14:12 2020 +0200

     add step to assert that there is no combinations in page

[33mcommit 1fb039ab74e483267e72025203b1125add8d0632[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 19:18:04 2020 +0200

     clear-cache-before-feature

[33mcommit 211cf8189da2af689bdb37787fbfe6db4b13fc0e[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 5 12:08:09 2020 +0200

     remove redundant if stmts

[33mcommit 7351dd7939401eda9cc1b3d282ca98b7b28e4b31[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 5 10:56:15 2020 +0200

     add scenario for pagination

[33mcommit fdadeb8e28f8976df57c24febdc8dfef0729ddca[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 20:35:41 2020 +0200

     assert combination attribute names and ids

[33mcommit 327d71a99346e21465f83067fcd3e6dbfb241ac6[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 19:28:03 2020 +0200

     fix invoke forgotten getter in order to loop through combinations

[33mcommit b03428e43708ceaca10c06cd1b4a3e0ed02063a1[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 19:20:19 2020 +0200

     add missing service argument. Fix sql typo

[33mcommit d2c5ec34bf6ff50d748c718d99339d9f168d8ac1[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 19:12:47 2020 +0200

     assert attribute ids before saving association

[33mcommit ffdb32de915d5b5f5fe5ef12b9b5574486d57afe[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 18:56:00 2020 +0200

     assertCombination exists

[33mcommit 60b8484abd602b4722f2f834a1bb7358ce3f91cd[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 18:38:41 2020 +0200

     fix adding attributesInfo to array instead of overriding it

[33mcommit e78f88af666e6635c64b097720e66ae3dc24362f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 18:11:13 2020 +0200

     assert Product existence before getting combinations. Use ProductId VO in Combination repository

[33mcommit 04a64563f18b1f632629d1a7bbd648d1d183e2fb[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 18:03:46 2020 +0200

     delete created combination from db if associating attributes fails

[33mcommit 64764cd2a0c4b7a5d029151a996854d4bab1d2d5[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 12:30:25 2020 +0200

     fix array psalm notation

[33mcommit 24da74a0b51a90e64833d0c83143587e1fe1655c[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 12:23:23 2020 +0200

     fix array appending

[33mcommit 930f15d70d4b6d673f8a213c6478e80268068334[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 11:54:32 2020 +0200

     remove redundant var

[33mcommit 600893c8f2a33a395f741f388531d96ac948ec35[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 11:48:59 2020 +0200

     move combination queries to repository instead of dataProvider

[33mcommit 3e6b8fb048739b18e082457de1ee25ad2bae7d72[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 11:37:48 2020 +0200

     revert implementing static isValid methods in VO

[33mcommit 7c7722bdcf6b6784561e2191ba5a393816e65168[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 11:33:04 2020 +0200

     format VO list in command

[33mcommit dcc6b4f719c2416b5ec4247823330396ba550852[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 11:06:34 2020 +0200

     fix docblock

[33mcommit 737b769e9930b6fa38d3a7498ff31c66b9124ff5[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 10:59:24 2020 +0200

     add CombinationRepository and CombinationCreator

[33mcommit b7c4b34b575607f0669581bc5fae658b9bb40a33[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 10:26:50 2020 +0200

     revert db transaction in handler

[33mcommit 6138e7abac89c810aa8a625bd7442eaa106a171d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 10:25:41 2020 +0200

     revert transaction methods in db

[33mcommit b23d21f2715ca6cec107c7f145f7c38c02ebc135[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 4 10:01:53 2020 +0200

     cleanup docblock

[33mcommit c9d074079778c55bcdeaea5b7c74fb52a7c16bad[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 31 17:21:25 2020 +0300

     rebase query result dto CombinationListForEditing

[33mcommit 861af3972c17443f0d63a97fffdf3dd14fe79d38[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 31 17:17:45 2020 +0300

     move combination->add() to try-catch. Move product date setup outside loop

[33mcommit e19df96af125760c94949d6dc7ba644b79d3ad33[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 31 17:02:34 2020 +0300

     optimize method

[33mcommit f799dfb20eec0503e0ec6a7d0d26bad418a02f31[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 31 16:58:13 2020 +0300

     add filters argument to endpoints (not yet implemented still)

[33mcommit b4651b1d95c9e00939e6905a8b41501ac0754865[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 15:46:02 2020 +0300

     fix invalid property type

[33mcommit 727d5936c002a039a8b67e0432622d9c2f6456dd[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 15:36:49 2020 +0300

     move methods to existing CombinationDataProvider. Remove additional CombinationDataProvider

[33mcommit 0bf5e6179467c804a519c9dbc681e481256ccc03[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 14:25:54 2020 +0300

     add some comments about specific price rules

[33mcommit d860cf48cdc9163c4af7affa0aefec75fe564273[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 14:21:59 2020 +0300

     move $product->setAvailableDate outside loop

[33mcommit 79e2b658098df32dcec2bc950f78656f8fb05e42[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 13:55:24 2020 +0300

     remove unused method

[33mcommit 6edbc535ca2a4a6d74d878d631661146ef2ae3be[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 13:39:25 2020 +0300

     optional limit and offset

[33mcommit 84c42d313b0eb6691bd0f65c8eec9b89ba414605[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 13:32:20 2020 +0300

     return book on transaction methods

[33mcommit 2ca14523065940b2c5a64c1ed73e70ac89140037[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 13:14:36 2020 +0300

     add transaction methods to db

[33mcommit 6f4f73609f6a251b9a0970056d67a08662dc2d92[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 13:05:45 2020 +0300

     assert attributeIds list in command

[33mcommit d4685aef3a42262d2eba5b0db1227150da7ee60d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 12:44:10 2020 +0300

     use dbInstance transaction instead of doctrine

[33mcommit c6e733536148313992fba269f108108aa405d044[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 11:35:50 2020 +0300

     optional $limit in combination provider

[33mcommit 7c9c9f1d6132ca67737592291485e34d8ac924b1[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Aug 14 11:33:32 2020 +0300

     some code cleaning

[33mcommit a70eb9ce157c49dbda0beb0ff343e6e85c086c25[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 18:19:39 2020 +0300

     add CombinationDataProvider

[33mcommit 9d4e009ca9613be1686168c100b8145316051bd9[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 16:38:44 2020 +0300

     refactor query to have mre loops but with more accurate data to save maemory

[33mcommit 8b01f65938731dec4e709bb9c7f891d0715c5430[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 14:58:11 2020 +0300

     add total combinations count and Additional class for ProductCombinationsForEditing;

[33mcommit 021046a1ceed47f807bebafa7da311b2e6b90283[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 10:39:49 2020 +0300

     some code cleaning

[33mcommit 08631de6557c379afd790c3b3f978f4f72f51839[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 10:16:05 2020 +0300

     add languageId to query

[33mcommit 13c0fca6bc426719ae15e1599b28381bfb80fe84[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 09:54:35 2020 +0300

     use limit in behat assertion

[33mcommit beb3104facfaacaa685e7b93555ae35ae28b0694[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 09:52:05 2020 +0300

     add offset and limit to cqrs query

[33mcommit 051a2e2f5507725a20fbf76f7b748ff454104f0b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Aug 13 09:44:57 2020 +0300

     catch all exception to rollback combo association saving

[33mcommit b1c844b1d122ec346816c5181a6b8e6cd964e131[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 18:06:59 2020 +0300

     mysql query for combinations pagination

[33mcommit af7a7898979f992ffe058afe1ea9c8966d83ca71[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 12:33:28 2020 +0300

     build combination name in constructor

[33mcommit 6dfeccd0e90e5fc01ade3143ee467f453f0aa929[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 12:29:00 2020 +0300

     use psalm array annotation

[33mcommit 44459262faac5b41ce3e8fded998289446e6302b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 12:27:28 2020 +0300

     instantiate Db in constructor

[33mcommit 018c4e41ebb58d0fe6bf228654d5200887140998[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 12:23:40 2020 +0300

     rollback transaction in same block as started

[33mcommit 2d0ae0a36c8f106d3d003b12c70ce4a8270ccb40[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 11:45:52 2020 +0300

     add combinations count and name assertion

[33mcommit 0d93ed58d8f85cec1fa6c2e7e664390ef8607349[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 11:03:02 2020 +0300

     fix combination formatting

[33mcommit a18a46c2e1d8179ae51d6985bccb1d43e77e1801[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Aug 12 10:27:01 2020 +0300

     fix array return type

[33mcommit 4e2a64abfbc64a88f1e08158001449f42392e258[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Aug 11 11:38:52 2020 +0300

     add sql transaction

[33mcommit 5a638ea793feeb3a0a89e00e68d396b31e2dfaf6[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 17:55:55 2020 +0300

     WIP implementing GetProductCombinationsForEditing

[33mcommit c9a72102f465fdf9596dbfdb672e8cfbe5415656[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 17:20:10 2020 +0300

     query result ProductCombination

[33mcommit 3b8d3db90a63caebc765728eaab96287dde6fbd3[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 16:21:07 2020 +0300

     add missing contructor

[33mcommit 8f656a81af838762ce3a6bd9de02bc01003ff01e[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 15:49:03 2020 +0300

     wip implement GetProductCombinationsHandler

[33mcommit 7e037ef478222d1820761d5f60e53c9fc8f52d55[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 14:30:42 2020 +0300

     behat context method to generate combo

[33mcommit 1a2513bfa89a6b18377bc0a23a2de0f7f76b6281[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 14:14:56 2020 +0300

     prepare behat

[33mcommit 3df224592b3b6da5bcc791ad7f1551b126bd81a8[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 14:00:22 2020 +0300

     use langId instead of default

[33mcommit 625438bc50654fcbd57370ad6a62b3e8eae0d313[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 13:58:48 2020 +0300

    add named attribute assertion method

[33mcommit 78d9aa17d5d745a1c5feaaf0e1c84601a2192fd2[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 13:48:35 2020 +0300

     add behat assertion for attribute group

[33mcommit 203322f9c5815623073603896e2f48138221fb3c[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 13:21:09 2020 +0300

     disable SpecificPriceRules before adding combos

[33mcommit 1d5502d21024a0e0182b64942f7a5699c3f07788[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 13:06:48 2020 +0300

     add todo

[33mcommit e8c5661ab1ca98ace68c3e0f2184106449720d71[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 13:02:02 2020 +0300

     throw proper exception

[33mcommit 184a047f066b3b854871bde8e119e2238bb20c09[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 12:51:52 2020 +0300

     basic handler implementation

[33mcommit 3c36734589e07425fbc0d063c9105b43ec65e417[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 11:24:30 2020 +0300

     add todo

[33mcommit b5b64fac339ba180a796f7fa2f1f861e7b3ac7a3[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 11:05:59 2020 +0300

     add and register empty handler

[33mcommit de22f4c1ec1816e9feffb96991c7c0175d27aa85[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 11:05:07 2020 +0300

     register generator service to yml

[33mcommit 00e918e83e4916f17c812956dbe9be68b0803d39[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 10:56:29 2020 +0300

     rename command- add product prefix

[33mcommit 0dbabce01f056382f7d8ff976516ed79ed710145[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Aug 10 10:52:59 2020 +0300

     prepare command GenerateCombinations

[33mcommit a6c0d3ad4dc6917090d8e9cc1326b4ecc49a3502[m
Merge: 1e5c2f136c 52e88841fb
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Wed Nov 25 14:31:32 2020 +0100

    Merge pull request #21993 from zuk3975/improve-behat-multilang
    
    Adjust behats localized fields assertion and add LocalizedArrayTransformContext

[33mcommit 1e5c2f136c59e032356ac0c9de1f8e6edae60287[m
Merge: f88da976bf b53b869190
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Wed Nov 25 12:04:08 2020 +0100

    Merge pull request #22090 from zuk3975/delete-redundant-commands
    
    Remove redundant customization field commands

[33mcommit 52e88841fb50f585086f7f47509a30a6d177f495[m[33m ([m[1;31morigin/improve-behat-multilang[m[33m, [m[1;32mimprove-behat-multilang[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 19:05:53 2020 +0200

     adjust update_prices feature after rebase

[33mcommit 5bd1e184135b7527fefff975eda2cb29b24619d7[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 19:04:11 2020 +0200

     adjust update_seo feature after rebase

[33mcommit eaada6f2a595c19bc227c4c963bf8520e4471036[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 18:23:18 2020 +0200

     remove unused method

[33mcommit 6be666e26b6dfa5aa2f3770b689771e1003a3050[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 17:44:00 2020 +0200

     adjust currency & order % supplier features

[33mcommit 7e418fa1f67b8d209e54b017cc7e3ed0e11ac391[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 19 14:09:11 2020 +0200

     adjust features

[33mcommit 436456ea22a009befd15584779e7620cbad06768[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Thu Nov 19 13:57:29 2020 +0200

     use table transformer for multilang assertions

[33mcommit 19ddd66d01d4faf58cf4f4f8ba5cc7c70aec6849[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 19:38:59 2020 +0200

     add StringToLocalizedArray transformer

[33mcommit f88da976bf21c13054b826f34f3b00e79beab6bf[m
Merge: 64e72caf51 ae78d75808
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Wed Nov 25 10:34:17 2020 +0100

    Merge pull request #22075 from jolelievre/merge-177-11-24-20
    
    Merge 177x to develop 24/11/20

[33mcommit b53b869190bcf8eb07c12ea560c43ab82abc1765[m[33m ([m[1;31morigin/delete-redundant-commands[m[33m, [m[1;32mdelete-redundant-commands[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 25 11:24:02 2020 +0200

     remove AddCustomizationField, UpdateCustomizationField, DeleteCustomizationField commands

[33mcommit bf04c93edf83cf636cce37e77498f5f28644aa6d[m
Author: Pierre RAMBAUD <pierre.rambaud@prestashop.com>
Date:   Wed Nov 25 10:00:17 2020 +0100

    Do not generate the data variable if it's not needed

[33mcommit ae78d7580872d491e8a9c920fffe4253a7bd9b74[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Nov 24 18:22:05 2020 +0100

    Adapt cart context

[33mcommit 83b5988cab0baaa0f4157985ead990bcb96c1626[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Nov 24 16:35:54 2020 +0100

    Run eslint fix

[33mcommit 495148b92df2d7a6b91ff472faaf8ce30424bb52[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Nov 24 16:34:11 2020 +0100

    Apply CS fix

[33mcommit 3cc5f7b33a6d0e27575cea347758614635e4852f[m
Merge: 64e72caf51 9233da02c7
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Nov 24 16:33:36 2020 +0100

    Merge branch '1.7.7.x' into merge-177-11-24-20

[33mcommit 64e72caf510dea1ab1678d1fe0c7e00d42a543ac[m
Merge: 3ea3a56ecb 404455350f
Author: Jonathan Lelievre <jonathan.lelievre@prestashop.com>
Date:   Tue Nov 24 15:55:01 2020 +0100

    Merge pull request #21336 from zuk3975/m/product/prices-rep
    
    Refactor UpdateProductPricesHandler to use ProductRepository [product page migration]

[33mcommit 404455350f44a826b9d4ac89092851a92f8ba9e6[m[33m ([m[1;31morigin/m/product/prices-rep[m[33m, [m[1;32mm/product/prices-rep[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 15:59:18 2020 +0200

     add code for INVALID_UNITY

[33mcommit 6fd939b1ad1279fada6f23d05921cbacea3545e6[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 15:55:48 2020 +0200

     use number_extractor in Filler

[33mcommit c346249825b815e0b12e93d1e57a766279a3f44d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Nov 24 15:45:27 2020 +0200

     remove unused class from constructor

[33mcommit 589a4873d30641b45015067579cb03ca5e7a9c53[m
Author: atomiix <tbaccelli@gmail.com>
Date:   Tue Nov 24 14:43:08 2020 +0100

    JS linter

[33mcommit 84ffb824d8e8df3f457bec220060344e0be8b63f[m
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Tue Nov 24 14:38:44 2020 +0100

    Update admin-dev/themes/new-theme/js/pages/module/controller.js
    
    Co-authored-by: Progi1984 <progi1984@gmail.com>

[33mcommit b775b248648161247b3490c5579000247b1cc2ad[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:51:01 2020 +0200

     move product creation in behat background

[33mcommit 9a5849e7c65519ba9f077e77157fe22f6982a02d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:47:17 2020 +0200

    Revert " save combination by reference instead of re-adding it over and over again"
    
    This reverts commit 46cdf9bdd066e493eb32446118a64ed98be677ea.

[33mcommit 084be918977617f5d6c3d02a2407b95f6bdb0efb[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:35:51 2020 +0200

     save combination by reference instead of re-adding it over and over again

[33mcommit 68c54708fa765087c6d379dc38d2e6a7297f0d4d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:16:15 2020 +0200

     remove duplicated context method

[33mcommit 02e61d511ebf4df9d7e698fdd02670de13a37e99[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:11:33 2020 +0200

     fix constructor argument

[33mcommit 7ebe1901fcd283e197933cbc8dd7b872857a71cf[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 14:49:59 2020 +0200

     fix service name in arguments

[33mcommit 9704f982c813f24f21cfd66e067ba5ea00a72b2f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 14:48:11 2020 +0200

     remove float cast from wholesale price

[33mcommit 0a13649de6b126163a13c5c84016a5489b4c7d82[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 13:30:29 2020 +0200

     move taxRulesGroup existence assertion to Repository

[33mcommit 9c7f9d92e63cf912b38a7e052b74300752a6dc6b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 12:54:46 2020 +0200

     rename ProductPriceProperties filler & method fillWithPrices. Add whole_sale price handling there too

[33mcommit 903417ed50ede9562bcd62ffd71a5b178e8bed6b[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Oct 12 17:56:12 2020 +0300

     fix yml service name

[33mcommit b7e422f755cd2f63f585fc1b28cf586e50d54f23[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 15:37:00 2020 +0300

     fix param annotations to please phpstan

[33mcommit 832c0b096618edc9d855e2a3d0131f95058d0a37[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 15:17:25 2020 +0300

     POC PriceFiller service to wrap domain logic for update

[33mcommit dc845fa81fd1990d1b21e57b93b5d72c558aef27[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 13:04:11 2020 +0300

     move taxRulesGroup assertion to corresponding repository & use in Validator. Adjust behats

[33mcommit 5981bae796d4a55777b8fb206489f6455fd1bc35[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 12:23:07 2020 +0300

     move validations to Validator

[33mcommit 10c589bf18ea9164f13df5efe74f863d37fa4ad0[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 11:50:37 2020 +0300

     refactor UpdateProductPricesHandler - use ProductRepository for update

[33mcommit 3ea3a56ecb3aaddc4bbf4edd32f53039e927d540[m
Merge: 348dff99a1 5773867f8e
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Tue Nov 24 10:35:29 2020 +0100

    Merge pull request #21345 from zuk3975/m/product/seo-rep
    
    Refactor UpdateProductSeoHandler to use ProductRepository

[33mcommit 9233da02c770fc41549d0ded52aa8ee36068d3a2[m
Merge: 58f7fec676 939b866b5e
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Mon Nov 23 18:05:49 2020 +0100

    Merge pull request #22063 from jolelievre/update-trads
    
    Last translation updates

[33mcommit 939b866b5ef6060d034a3838adfc77ce6fba7e43[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Nov 23 17:35:53 2020 +0100

    Last trad updates

[33mcommit 56b615857d142cea5edf98e661faeb65e19e55b0[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Mon Nov 23 16:20:00 2020 +0100

    Run CSFixer

[33mcommit 33ea36374e96808650f68053f79798bf9b9d4462[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Mon Nov 23 16:00:49 2020 +0100

    Add select2 to the import localization select

[33mcommit 58f7fec676649f11a90a4590ecf7326f6d55bda3[m
Merge: ec9fd464e6 e6ef8476e0
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Mon Nov 23 15:49:02 2020 +0100

    Merge pull request #22013 from jolelievre/decrease-gift-product
    
    Handle parallel updates from CartRules when updating a product in Order

[33mcommit ec9fd464e69195783f370b197573ee88393d18c5[m
Merge: ee0996a11d 21bfb21cf7
Author: Simon G <49909275+SimonGrn@users.noreply.github.com>
Date:   Mon Nov 23 15:45:38 2020 +0100

    Merge pull request #22055 from boubkerbribri/fix-nightly-177x-23-11-20
    
    Delete spaces in the end of title name in faker for UI tests

[33mcommit 5773867f8ea475434320649ba4d6bcdd59eb8279[m[33m ([m[1;31morigin/m/product/seo-rep[m[33m, [m[1;32mm/product/seo-rep[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 23 16:28:35 2020 +0200

     apply cs fixer

[33mcommit 348dff99a106a065dcca25837f277dd0253c3226[m
Merge: f6dab7a6f2 c6837af9ac
Author: Simon G <49909275+SimonGrn@users.noreply.github.com>
Date:   Mon Nov 23 14:37:46 2020 +0100

    Merge pull request #22054 from nesrineabdmouleh/fixSortAndPaginationEmails
    
    Add test 'Erase all' emails

[33mcommit 287c20fd3b21f5c8d0a27d59b794726e2a04f2df[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Nov 23 12:39:38 2020 +0200

     fix scenarios formatting

[33mcommit dc8c94b02a432717d29ac1bdf798af72dc8faf70[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 13:33:20 2020 +0200

     add unit test to support forcing NO_REDIRECT_TARGET when 404 type provided

[33mcommit d8874988ee7696780397fecd3f5bbbdad1f5f2da[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 12:51:36 2020 +0200

     add unit test for RedirectOption. Force NO_TARGET when TypeNotFound in VO

[33mcommit 33659133c0c172a74470267872500dc77c1fdfb8[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 12:19:05 2020 +0200

     unit tests for redirectTarget

[33mcommit 2a839ac6e7b9cfece3d9c59bdf633f170344a500[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 12:07:35 2020 +0200

     add unit tests for redirectType

[33mcommit be30875ceb2bc608babaf6659115ca411afb7bde[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 11:54:11 2020 +0200

     wip. unit tests

[33mcommit 58c30e9cc94e6d53bd4f60592516062aad80176f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 17:03:21 2020 +0200

     init products in background, add multi-lang scenarios

[33mcommit 83888581fa5d10a8d5896462e21d26558399a96d[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 16:03:49 2020 +0200

     change some scenario inputs

[33mcommit 6c84d3abcb7c667bbc496041f36796f8f95acf31[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 15:32:18 2020 +0200

     fix yml

[33mcommit 328aa79716903086c408cbd023b6b2f7f2107b97[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 12:26:30 2020 +0200

     rename ProductSeoPropertiesFiller & method fillWithRedirectOption

[33mcommit 5b625edca427f13b00de09324e9528f092bdb826[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Tue Oct 13 14:51:16 2020 +0300

     fix service names

[33mcommit b116c85410a025e70e775c440d2ad803f949308e[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Oct 12 12:02:28 2020 +0300

     do not assert category existence if NoTarget

[33mcommit 5c1a561746f80ec21fd3497e840af849fdb1b0a7[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Mon Oct 12 11:57:56 2020 +0300

     fill updatableFields & remove validation in handler

[33mcommit 90483394b3777ed498d46757f23d213e91fbc01f[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 18:06:38 2020 +0300

     introduce ProductSeoFiller & CategoryRepository. Use SeoFiller in UpdateProductSeoHandler

[33mcommit 81e1265c4917a4b8374b3e7b5bff1d0c43fd9d82[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Oct 7 15:55:22 2020 +0300

     use productRepository in handler

[33mcommit f6dab7a6f2c6ee078863e9413eca26858fea5a00[m
Merge: 532834b8ae 20f3d83f0c
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Mon Nov 23 14:11:21 2020 +0100

    Merge pull request #21103 from jolelievre/update-product-stock
    
    Introduce UpdateProductStockCommand

[33mcommit 21bfb21cf7fac6b3374e5bd83affec9c92a48590[m
Author: Boubker BRIBRI <boubker.bribri@prestashop.com>
Date:   Mon Nov 23 12:37:19 2020 +0100

    Delete spaces in the end of title name

[33mcommit c6837af9ac53e06961019b0ecc3e207d653b5ed3[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Mon Nov 23 12:13:06 2020 +0100

    Add test 'Erase all' emails

[33mcommit 20f3d83f0c4ddb67b4cd261c61fc98890f95f26f[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Nov 23 11:50:18 2020 +0100

    Remove decare strict type

[33mcommit e6ef8476e05dd646badf00695809854777db2df1[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Nov 23 11:23:00 2020 +0100

    Address feedbacks from PR

[33mcommit af8e755e6d2df87f957d07e6589472ddd52e2b57[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 20:28:35 2020 +0100

    Fix OrderProductQuantityUpdater multi invoice switching

[33mcommit 6e691c52e3b6393497e8fb87006dce3c879a2a63[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 20:12:35 2020 +0100

    Update stock movement check into more verbose sentence

[33mcommit c0b43d85095d0b8bc230daccf8577c0e03eb7cdd[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:38:48 2020 +0100

    Build assets

[33mcommit 34d1d8813f040aef490f98e1e605530ae556f1d6[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:37:33 2020 +0100

    Integrate CartProductsComparator into AddProductToOrderHandler this allows to correctly add additional gifted products when a product is added to the order

[33mcommit 863aae0ffeb1791d017033e33839c32de10a1d5c[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:35:53 2020 +0100

    Adapt OrderProductRemover with new comparator functions, and remove gift products

[33mcommit 0a4dad6187d51b3617409fa0775d48b04394cc97[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:35:09 2020 +0100

    Adapt OrderProductQuantityUpdater with new comparator functions

[33mcommit ac7a67eea15e100a2aa4bc5b8590f8aede93ad2d[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:34:15 2020 +0100

    Add new parameter to allow removing gift products

[33mcommit c21e521a15facf25c8df37e39cbff3c0961c6f27[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 19:32:46 2020 +0100

    Change CartProductsComparator to be able to tell creation from pure updates, simplify known updates as parameter to be more stateless and reduce the number of functions

[33mcommit 46504c50d77a9e8ea2cd04acab31e54ffb5949f2[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 15:58:45 2020 +0100

    Remove product from row in BO when response returns empty string

[33mcommit ee0996a11d9b3ef14bc9c9e62a1048ed07ea1c89[m
Merge: 545c9d88cb b8f12216bc
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Fri Nov 20 15:53:53 2020 +0100

    Merge pull request #22018 from matks/handle-zero-ratio-for-order-detail-tax
    
    Order zero ratio for order detail tax

[33mcommit 19a9fb11569c89c46e164707a65bb0f87dbbddf3[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 15:09:19 2020 +0100

    Use + and - for sign

[33mcommit 60574f5974b73f57e770e137cc89e55259badf93[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Nov 20 15:04:07 2020 +0100

    ProductStockUpdater handles the special case for depends_on_stock

[33mcommit d7f70504f2cca1569d92d1829b3cd6806ebdb611[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 20:48:00 2020 +0100

    Improve Product::getDefaultAttribute static cache so that it can be cleared

[33mcommit 2a2a62ed4574aa563a99f5876d8e86d2df9ecfdd[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 18:19:53 2020 +0100

    Rename UpdateProductStockCommand into UpdateProductStockInformationCommand

[33mcommit 1f9a0c69f322f389f48a5af52352a107435c3f7d[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 17:53:28 2020 +0100

    Inject boolean configuration instead of configuration service

[33mcommit a68044b7c81e8af0b20263408f1b61b4ba1253c9[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 13:38:20 2020 +0100

    Improve behat tests again

[33mcommit 87e9a941b8cca2408e09eed357ff61714ed20615[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 13:12:49 2020 +0100

    Rename ProductStock into /ProductStockInformation

[33mcommit 2e207e4005e2c632d60a59e9ccf041f518b94d2e[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 13:07:30 2020 +0100

    Address miscellaneous PR feedback

[33mcommit f6778d9908a933d091ee3b081f405ea94d26f1d6[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 19:26:36 2020 +0100

    Use assertSame so that tests are stricter

[33mcommit df34d092cc4a9306e904c9ce4bff99a3a0a38133[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 18:54:03 2020 +0100

    Product validator is injected with configuration values instead of Configuration service

[33mcommit 7201bb0eab1fdad8ac177bca256900323ce6e990[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 18:40:11 2020 +0100

    Add comment in Fetaure class

[33mcommit 67d136461e2f6cfb50dac866405f10045a03906c[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 18:38:10 2020 +0100

    Add missing PHPDoc

[33mcommit d647e1586c0d8812e466557017bf1131e7e75e16[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 18:37:54 2020 +0100

    Address PR feedback

[33mcommit 713c84f2b31b421a8026d24fc4e354f1a93efe18[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 18:08:56 2020 +0100

    Use int as pack stock type

[33mcommit 535471d20f1dcc703159ba53fb3d4579c43152f5[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 5 17:45:39 2020 +0100

    Use int as out of stock type

[33mcommit 83ddf420f9b5310a27f6082b020b7c3a328eedac[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 15:14:33 2020 +0100

    Last cleaning before review

[33mcommit fa59c34bc260068b77162aea2ea19dd45124a72a[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 14:22:32 2020 +0100

    Document return type

[33mcommit d574cf6c9b5720db27ca1b7b90f1baa0fe114e75[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 14:14:01 2020 +0100

    ProductStockUpdater handles StockAvailable internally

[33mcommit e8d7f5adaec8eadcd572f930a536a6113f4b04f6[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 13:58:38 2020 +0100

    Object models are filled in handler

[33mcommit a4996abae6eb4c2195ece36ec55774b859bd96c9[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 13:18:15 2020 +0100

    Handle more advanced error case in Validator

[33mcommit 37eeba718e415de364db73bee18ef367d282bdfc[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 28 12:14:08 2020 +0100

    Manage product stock fields validation

[33mcommit 73730c56b40252f2084cde444d4464631a4355ab[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Oct 27 20:13:10 2020 +0100

    Remove useless classes

[33mcommit 17e349108a09357962091cd8d17615e27b839326[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Oct 27 20:13:00 2020 +0100

    Set shop to enable stock movement

[33mcommit 2d7ef93f1d22e14fedf36bcdb6ec11dd1dae8b99[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Oct 27 20:12:38 2020 +0100

    Improve message output when exception is caught

[33mcommit b387d55023558a1e26264da9518df638cfbff8a2[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Oct 27 15:22:59 2020 +0100

    Little modif from review

[33mcommit 69a9a443608563ff8d1c1117877dd6bcb6588923[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Oct 23 18:25:12 2020 +0200

    Handle available fields

[33mcommit 57b6d3ba992e441e106ce7425bc5032c356f16ac[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Oct 23 17:46:20 2020 +0200

    Handle not localiazed basic fields

[33mcommit 14e2bcda0ed225b94bd9f4f95b4bd3c0911a9064[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Oct 23 17:03:59 2020 +0200

    Fix stock tests few bugs

[33mcommit 44a562f81e636ae86f5baf0d882bcf4886b281b0[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 21 21:22:02 2020 +0200

    Fix pack bugs because of moved exceptions

[33mcommit b9070400b5ec4deb92fa683282239d5cc8d016ff[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Oct 13 20:29:34 2020 +0200

    Create stock namespace and move expections and VO in it

[33mcommit f62b981213c5d6c523028d0141f254c565b585a3[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 21:04:30 2020 +0200

    Add behat tests for optional argument add_movement

[33mcommit 2bebcb0b1e006a0f2eedffc503ed9078bf0d07eb[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 20:57:32 2020 +0200

    Improve boolean handling in stock behat tests

[33mcommit 20a285f865dc7e6dbcf45714e26c19bd045021d8[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 20:49:11 2020 +0200

    Reintroduce basic filler methods

[33mcommit 15d5113e835761fd7866d28023db40ba3614ea4b[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 20:22:02 2020 +0200

    Refacto ProductStockUpdater to use repositories and introduce AbstractObjectModelFiller

[33mcommit acff69f1c099dd3575471779279c74a72138f165[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 19:52:54 2020 +0200

    Refacto StockAvailableProvider into StockAvailableRepository

[33mcommit 7483bee1014db11724a0c87a54b5640e28a052ec[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Sep 22 13:18:50 2020 +0200

    Manage product quantity, also update stock movement when quantity is changed, add tests

[33mcommit 3da5991072f75156ea222d55dc2a224249f77da4[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Sep 21 19:54:23 2020 +0200

    Manage out of stock update, add VO object to handle it

[33mcommit 45ff2a02cfad3aff2e13e0740b7508ac3c1febf6[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Sep 21 18:57:38 2020 +0200

    Refacto PackStoType VO

[33mcommit d6900541ebc03a0a5ddc7ef3bddb3f1849bceab4[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Sep 18 21:15:02 2020 +0200

    Adapt stock handler code to use new Updater architecture, update behat tests for new context splitting

[33mcommit 70d81a81f33dc23fc66377d182e4508161120dd7[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Jun 30 18:58:00 2020 +0200

    Advanced tests for pack stock type added

[33mcommit d482bb10146bfb56a6a8ed4427268dee82e9481a[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Jun 29 02:42:13 2020 +0200

    Manage pack stock type for classic stock

[33mcommit 71bf0b28c0497880064850828bffb26f5f8e7001[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Sun Jun 28 20:30:55 2020 +0200

    Separate classic and advanced stock management in handler, and test files Managed depends_on_stock

[33mcommit b664616c05a804787e5b7b40fff49fa85d60a99e[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Sun Jun 28 13:53:58 2020 +0200

    Add handler, query and test for field use_advanced_stock_management

[33mcommit 8706c86289874fa32aa98503b97bf05f290e5229[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Jun 26 19:15:52 2020 +0200

    Add missing strict types

[33mcommit f04a97735416074ffced134816cb2dd8f6e88649[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Fri Jun 26 19:15:16 2020 +0200

    Add product stock command

[33mcommit 532834b8aed2a03ce93e57b2d1458170d293fa07[m
Merge: c866ed5460 58ffb6878d
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Fri Nov 20 14:29:14 2020 +0100

    Merge pull request #21946 from nesrineabdmouleh/functionalSortAndPahinationEmailLogs
    
    Add test "Sort and pagination emails table"

[33mcommit c866ed546043b453d577f9923d629203bb264c18[m
Merge: b4d32d6bc1 a4802c806c
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Fri Nov 20 14:28:56 2020 +0100

    Merge pull request #21986 from nesrineabdmouleh/functionalDisableMultistore
    
    Add test "Disable multi store on CRUD shop group test"

[33mcommit b4d32d6bc1f151e46a81b806bd2edd16b1f2503a[m
Merge: 07aecb4978 cfd4504cd9
Author: Jonathan Lelievre <jonathan.lelievre@prestashop.com>
Date:   Fri Nov 20 12:24:22 2020 +0100

    Merge pull request #21510 from zuk3975/m/product/image-add
    
    Introduce ProductImageUploader and AddProductImageCommand [product page migration]

[33mcommit 07aecb49789d00c7a88ea8473ec20b6de1622c01[m
Merge: 68644a8014 aca2ea7d85
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Fri Nov 20 12:00:29 2020 +0100

    Merge pull request #21886 from NeOMakinG/issue21831
    
    Fix radius on some custom components

[33mcommit 42fa9b32e46497e05b3552cea3e8a07103b0c5a7[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Fri Nov 20 11:49:24 2020 +0100

    Remove duplicate id on product-list-bottom

[33mcommit cfd4504cd9be1b3a17c4538704f6048654a83708[m[33m ([m[1;31morigin/m/product/image-add[m[33m, [m[1;32mm/product/image-add[m[33m)[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 12:00:01 2020 +0200

     adjust scenario wording

[33mcommit 68644a80147f4616157b2de31e8a10eaeebebe3f[m
Merge: eba70313ac 84a7398ec1
Author: Mathieu Ferment <mathieu.ferment@prestashop.com>
Date:   Fri Nov 20 10:57:16 2020 +0100

    Merge pull request #21793 from atomiix/fix/21778
    
    Fix Customer view Vouchers & Addresses tables

[33mcommit cee043fef5aa05c257ee68a86084282839bd6dc9[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Fri Nov 20 11:55:34 2020 +0200

     fix licence

[33mcommit b8f12216bcab2f3371f8a2cbe92ecdfed8531e6c[m
Author: matks <mathieu.ferment@prestashop.com>
Date:   Fri Nov 20 10:01:10 2020 +0100

    Apply php-cs-fixer

[33mcommit eba70313acfe8885454dfbf637ab63e24539ba92[m
Merge: d70ebf22fd b58ff65735
Author: Jonathan Lelievre <jonathan.lelievre@prestashop.com>
Date:   Thu Nov 19 19:11:33 2020 +0100

    Merge pull request #21094 from gfilippakis/patch-1
    
    Fixed array parameter processing in Link::getCategoryObject method

[33mcommit 545c9d88cba141a8aa9ea2dbc695e822aea87b6f[m
Merge: 9149a8e6c7 fd4f0349d6
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Thu Nov 19 18:27:14 2020 +0100

    Merge pull request #21981 from PierreRambaud/fix/21979
    
    Make sure favicon, stores_icon and logo are correctly settled for themes

[33mcommit d70ebf22fd8cedbb939ac68d7580ff4df828fa39[m
Merge: 9c45264092 145787ecc7
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Thu Nov 19 18:23:13 2020 +0100

    Merge pull request #20809 from dgonzalez360/dgonzalez360-cookie-language
    
    Update to check Language association to the current store

[33mcommit 4d5635c6e39b12478efc2f0c4a6e05c6ecb96415[m
Author: matks <mathieu.ferment@prestashop.com>
Date:   Thu Nov 19 18:03:48 2020 +0100

    Add Behat test to assert invoice product tax details when empty order receives new product

[33mcommit 373afb933c08677a4100a44222c936c102bf9def[m
Author: matks <mathieu.ferment@prestashop.com>
Date:   Thu Nov 19 16:44:29 2020 +0100

    Order zero ratio for order detail tax

[33mcommit b58ff65735af382392b6002e561ca51c0c9743e2[m
Author: Pierre RAMBAUD <pierre.rambaud@prestashop.com>
Date:   Thu Nov 19 17:37:21 2020 +0100

    Simplify the code

[33mcommit 2638bc413338d9d6cfbd832014c62d108ccc7459[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 17:28:53 2020 +0100

    Fix behat test with new expected status

[33mcommit 2b662eb4f5d1976b97d4d562e079207d2b7c993d[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 17:15:53 2020 +0100

    Add unit tests for CartProductsComparator

[33mcommit 562340a85e11ae64902c514e5100abccc40f4159[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 15:55:19 2020 +0100

    Move CartProductsComparator into Adapter namespace

[33mcommit c5aef3d7f34e17627e0b8f5c2d04da5a020a3f60[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 15:02:58 2020 +0100

    Use CartProductsComparator in update action as well, and rely on CartProductUpdate to apply modification instead of plain arrays

[33mcommit 6b8e372e2628708d5c2091f3ea30802124722b56[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 15:01:54 2020 +0100

    Little modif to avoid division by zero

[33mcommit 0c24d4afcbf30f4506c59ec0321440741eeba0ea[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 12:34:19 2020 +0100

    Add test when gift product is decreased

[33mcommit 52dabccb88a73e36f22ab402e958772f5b331f38[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 12:24:14 2020 +0100

    Integrate CartProductsComparator into OrderProductRemover

[33mcommit 046ec392820bcae73d8690f8352984f69f1e6298[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 12:22:42 2020 +0100

    Introduce new CartProductsComparator to help update order correctly

[33mcommit b6b75bc0431c72bdd53fbd7e39da97bfe7570f0f[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Oct 21 20:36:15 2020 +0200

    Test if $category is strict integer

[33mcommit 87b8b01a553fd76a1e1acac651564b93fbe6106d[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Mon Oct 12 12:37:21 2020 +0200

    Improve Link::getCategoryLink

[33mcommit 59fa8df5ff1bd843df3a1a4b4427a8d27c3a9d97[m
Author: George Filippakis <28734506+gfilippakis@users.noreply.github.com>
Date:   Mon Sep 21 20:37:04 2020 +0300

    CO: fixed Link::getCategoryObject array param

[33mcommit 84d55f0a04559ea88a3b66355ae8e9502e57b39a[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Thu Nov 19 17:12:17 2020 +0100

    Add some fixes

[33mcommit 39c37bb7cda94dcd135dc65d4ca0d52e5f339cf2[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Thu Nov 19 17:04:56 2020 +0100

    Enable/Disable aliases by bulk actions

[33mcommit 9149a8e6c7d1cb8ee192322b6a12e3c24d6e0c65[m
Merge: bab409b2e6 cdd664a4cf
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Thu Nov 19 15:50:08 2020 +0000

    Merge pull request #21781 from sowbiba/fix-add-product-to-cart-with-gift
    
    Remove gifted quantity from product order quantity

[33mcommit 09f981f58bb3f8f8cea4ae036549ee184029ad1d[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Thu Nov 19 16:25:48 2020 +0100

    Create 2 aliases then delete by bulk actions

[33mcommit 9c452640921c38e155a1bf2f7e6b0383c50e35fb[m
Merge: 8292154fb4 c16df965dd
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 19 16:24:41 2020 +0100

    Merge pull request #21985 from NeOMakinG/issue21983
    
    Remove wrong div endblock on order page view after the 177 merge

[33mcommit 8292154fb439d9fe6dabf14dc841a1c442e93f68[m
Merge: fc5fa1df93 892c611be7
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 19 16:01:13 2020 +0100

    Merge pull request #21947 from NeOMakinG/issue21943
    
    Adjust modal position when wrong used with a form

[33mcommit bab409b2e631dd1775480e0df22d25793e85bf40[m
Merge: 5735e79cdc 67f3da8568
Author: Krystian Podemski <kpodemski@users.noreply.github.com>
Date:   Thu Nov 19 15:53:54 2020 +0100

    Merge pull request #21994 from jolelievre/remove-cancel-all-products
    
    Remove or cancel all products

[33mcommit 5735e79cdcf282faccf2cf3a18fdf565bd26e789[m
Merge: 4277adc598 3da0b1f253
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 19 14:47:54 2020 +0100

    Merge pull request #21975 from PierreRambaud/fix/21948
    
    Correctly substring fields before update, remove duplicates and add missing sql queries

[33mcommit fc5fa1df93b3c6633b5344874087313fa95c3dac[m
Merge: d9dcd43981 cf326fd1fa
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 19 13:57:03 2020 +0100

    Merge pull request #21855 from comxd/patch-2
    
    Add missing SQL row for actionFrontControllerSetVariables hook

[33mcommit 67f3da85686df0fd42e7566c6fbcec5a8ffed490[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 19:15:45 2020 +0100

    Add tests when we remove or cancel all products from order

[33mcommit 0f54b5e567038347079dbde6d0f23da504d6e3fd[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Thu Nov 19 12:44:11 2020 +0100

    Remove cancel status update in OrderProductRemover because it must only be done in CancelOrderProductHandler

[33mcommit dbee32518d0f45f92aff8847def36ffb55213fde[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Wed Nov 18 19:14:56 2020 +0100

    Compute invoice totals even when it has no more products

[33mcommit 58ffb6878d9873097415ca1a0aacd47c84afc702[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Thu Nov 19 12:04:03 2020 +0100

    Fix typo error

[33mcommit aca2ea7d853b89d38440863759131a7433a39817[m
Author: SZCZUPAK Valentin <valentin.szczupak@prestashop.com>
Date:   Thu Nov 19 11:47:27 2020 +0100

    Update admin-dev/themes/new-theme/scss/config/_settings.scss
    
    Co-authored-by: atomiix <tbaccelli@gmail.com>

[33mcommit cf326fd1fae55d9ec71c4b87e784ba345681b8f2[m
Author: Pierre RAMBAUD <pierre.rambaud@prestashop.com>
Date:   Thu Nov 19 11:31:23 2020 +0100

    Missing actionFrontControllerSetVariables in hook.xml

[33mcommit d9dcd4398176e04531199bcffefa773b50841a11[m
Merge: e9624dc1b4 0046bf590f
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Thu Nov 19 11:19:58 2020 +0100

    Merge pull request #19776 from NeOMakinG/issue19408
    
    Allow developers to use their own tinymce config

[33mcommit e9624dc1b4744e92d62680cd9d7e3193357b1c16[m
Merge: f5a8bd1434 f3f7976a35
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Thu Nov 19 10:43:20 2020 +0100

    Merge pull request #22001 from matks/fix-some-typos
    
    Fix some typos in admin filemanager

[33mcommit 13257590249989b076622826c5e6084ac7e2c3d1[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Thu Nov 19 09:54:15 2020 +0100

    Lint

[33mcommit f3f7976a35e077bb591ae4a8e9277f33ef8c66dc[m
Author: matks <mathieu.ferment@prestashop.com>
Date:   Thu Nov 19 09:48:54 2020 +0100

    Fix some typos in filemanager

[33mcommit 4c8fbe5c67597054cae473097088a6af26eb0470[m
Author: okom3pom <contact@okom3pom.com>
Date:   Thu Nov 19 08:19:14 2020 +0100

    Update Product.php

[33mcommit 06b3cae9d2824d533c4f6fba5691bbfc4a501c43[m
Author: okom3pom <contact@okom3pom.com>
Date:   Thu Nov 19 08:16:36 2020 +0100

    null to true

[33mcommit f5a8bd143412653c0e28e2a3231515f1c7e04c57[m
Merge: c190ae18ff 6f593373b4
Author: Krystian Podemski <kpodemski@users.noreply.github.com>
Date:   Wed Nov 18 20:30:49 2020 +0100

    Merge pull request #21984 from PululuK/improve-vars-name
    
    Improve Link::getProductLink : Avoid short variable names

[33mcommit c190ae18ffa3d879574d986cc32251c695bea68f[m
Merge: b13f02eb40 a643436b14
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Wed Nov 18 18:40:52 2020 +0100

    Merge pull request #21628 from sylwit/patch-3
    
    Stream downloaded file for HTTP/2

[33mcommit fd4f0349d64db4587c3495c32fa8165660f146ab[m
Author: Pierre RAMBAUD <pierre.rambaud@prestashop.com>
Date:   Wed Nov 18 18:18:45 2020 +0100

    Use class cache to don't rebuild the template var urls each time the method is called

[33mcommit cdd664a4cf0575d9e966f349eb62f031a35f8191[m
Author: Ibrahima SOW <sowbiba@hotmail.com>
Date:   Wed Nov 18 16:59:03 2020 +0000

    Update src/Core/Domain/Cart/Query/GetCartForOrderCreation.php
    
    Co-authored-by: Pablo Borowicz <pablo.borowicz@prestashop.com>

[33mcommit 521685a40d408041dc972c62651f61a2b090f72a[m
Author: Ibrahima SOW <sowbiba@hotmail.com>
Date:   Wed Nov 18 16:45:58 2020 +0000

    Update src/Adapter/Cart/QueryHandler/GetCartForOrderCreationHandler.php
    
    Co-authored-by: Pablo Borowicz <pablo.borowicz@prestashop.com>

[33mcommit a39877cc588de71cb7fafa830213184b93fcea0d[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Wed Nov 18 16:33:22 2020 +0000

    Move class CartDeliveryOption, fix typo, add test for hideDiscount

[33mcommit 738dee688fce0e13c262490a3eb34118fc2b5f9b[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Wed Nov 18 15:54:41 2020 +0000

    Use correct verb in behat when adding product to cart

[33mcommit e3618fc3c02abad2a06183027a2d9811a8672435[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Wed Nov 18 15:31:49 2020 +0000

    Use a single attribute to split gift and compute cartRules

[33mcommit 92e890051ee1e07729adfe82f6ceb9bff381a39e[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Tue Nov 17 20:48:53 2020 +0000

    Rename GetCartInformation to GetCartForOrderCreation + split getSummaryDetails of CartCore

[33mcommit 82f58b81074af89ef2aa79c61d194a462aad4365[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Tue Nov 17 15:50:15 2020 +0000

    Test display gift with payed products

[33mcommit 5d31ddd49318def4b27e1f2a50092822ab45fe57[m
Author: Jonathan Lelievre <jo.lelievre@gmail.com>
Date:   Tue Nov 17 16:40:50 2020 +0100

    Move gift cart rule scenario into dedicated file

[33mcommit fa809ea34ab05afc19ddab195827c77f4b154e31[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Tue Nov 17 13:36:44 2020 +0000

    rename variables

[33mcommit ebf7c8c4fab5979864dfeab1bd6579cf1f68690d[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Tue Nov 17 12:20:56 2020 +0000

    Change default value for splitGift

[33mcommit f3cce7e01f298a0b639275f5af13cd7d4654d581[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Tue Nov 17 12:18:20 2020 +0000

    Add option to split gift or not + Do not compute gifts in AddProductToCart and UpdateProductInCart handlers

[33mcommit 83f9270c2bd197dd7c8a1c6441b8120d064f551e[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Thu Nov 12 14:40:06 2020 +0000

    Add behat for addProduct and updateProductQty when cart have a gift

[33mcommit beb9c9e69c60dff8b22f26d05f7ebf6b7b7c3f81[m
Author: Ibrahima SOW <ibrahima.sow@prestashop.com>
Date:   Thu Nov 5 17:33:04 2020 +0000

    Remove gifted quantity from product order quantity

[33mcommit a4802c806c76c1daa09a686aa0c65d8347a67961[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Wed Nov 18 17:15:06 2020 +0100

    Edit it message

[33mcommit 050efc4eeabbb0592a935b8ceb7e302be092f036[m
Author: nesrineabdmouleh <nesrine.abdmouleh.info@gmail.com>
Date:   Wed Nov 18 17:10:40 2020 +0100

    Disable multi store

[33mcommit 4277adc598cd61a4e9432c3c53bae0decd0d7fbf[m
Merge: 2f128ca8a0 7b5e77d048
Author: PululuK <pululuandre@hotmail.com>
Date:   Wed Nov 18 17:08:49 2020 +0100

    Merge pull request #21721 from jolelievre/order-multi-shop
    
    Handle Shop context override in order editing

[33mcommit c16df965dd91bd998b69f4fc13795928fce70aa3[m
Author: Valentin Szczupak <valentin.szczupak@prestashop.com>
Date:   Wed Nov 18 16:36:31 2020 +0100

    Remove wrong div endblock after the 177 merge

[33mcommit b13f02eb40c4725dd22f206e42c7b7310a44b299[m
Merge: 2b0f982445 01f88f3d63
Author: Progi1984 <franck.lefevre@prestashop.com>
Date:   Wed Nov 18 16:29:08 2020 +0100

    Merge pull request #21960 from matthieu-rolland/fix-form-selectors
    
    Fix legacy form selectors

[33mcommit 6f593373b416cbbb81954e0729606d8cf5d84357[m
Author: PululuK <pululuandre@hotmail.com>
Date:   Wed Nov 18 16:15:29 2020 +0100

    Improve var name

[33mcommit f5efdb60e33215c6c5f1a458b21ed9fccc2ca974[m
Author: Pierre RAMBAUD <pierre.rambaud@prestashop.com>
Date:   Wed Nov 18 15:41:08 2020 +0100

    Make sure favicon, stores_icon and logo are accessible without editing the theme

[33mcommit 7bf45d37a3b93dc922be57ee4ad3db5b38699dda[m
Author: Julius Zukauskas <zuk3975@gmail.com>
Date:   Wed Nov 18 16:04:47 2020 +0200

     remove irrelevant todo

[33mcommit 2b0f98244576c1809dc82a07cb587bb29cfde0f1[m
Merge: ca819e95fd c88ae8074e
Author: Simon G <49909275+SimonGrn@users.noreply.github.com>
Date:   Wed Nov 18 14:36:48 2020 +0100

    Merge pull request #21973 from nesrineabdmouleh/functionalCRUDMultiStore
    
    Add test "CRUD multistore"

[33mcommit 5f00eab738a3f922c26bca681df49318636581ec[m
Author: SZCZUPAK Valentin <valentin.szczupak@prestashop.com>
Date:   Wed Nov 18 14:36:04 2020 +0100

    Class attribute shorthand
    
    Co-authored-by: GoT <PierreRambaud@users.noreply.github.com>

[33mcommit ca819e95fdc9cd47bb01cb7bbd7dfa4f94d55db0[m
Merge: 4e089574a3 153daf0ac3
Author: GoT <PierreRambaud@users.noreply.github.com>
Date:   Wed Nov 18 14:29:09 2020 +0100

    Merge pull request #21959 from boubkerbribri/fix-step-identifier-command
    
    Fix step identifier checker command name
