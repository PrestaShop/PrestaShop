/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

interface RendererType {
  setLoading: (toggle: boolean) => void;
  render: (data: Record<string, unknown>) => void;
}

export default RendererType;
