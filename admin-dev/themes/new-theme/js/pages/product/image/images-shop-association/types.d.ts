/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface ProductShop {
  shopId: number;
  shopName: string;
}

interface ProductImage {
  thumbnailUrl: string;
  imageId: number;
  associations: ProductShopImage[];
}
interface ProductShopImage {
  shopId: number;
  isAssociated: boolean;
  isCover: boolean;
}
