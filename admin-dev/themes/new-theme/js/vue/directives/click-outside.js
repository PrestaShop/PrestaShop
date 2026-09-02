/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
let binded = [];

function handler(e) {
  binded.forEach((el) => {
    // Going through the path is more accurate because the initial target might have been removed
    // from the DOM by the time this handler is reached (ex: click on typeahead research suggestion)
    if (e.path && e.path.length) {
      for (let i = 0; i < e.path.length; i += 1) {
        const ancestor = e.path[i];

        if (ancestor === el.node) {
          return;
        }
      }

      // No ancestors matched el, so the click was outside
      el.callback(e);
    } else if (!el.node.contains(e.target)) {
      el.callback(e);
    }
  });
}

function addListener(node, callback) {
  if (!binded.length) {
    document.addEventListener('click', handler, false);
  }

  binded.push({node, callback});
}

function removeListener(node, callback) {
  binded = binded.filter((el) => {
    if (el.node !== node) {
      return true;
    }

    if (!callback) {
      return false;
    }

    return el.callback !== callback;
  });
  if (!binded.length) {
    document.removeEventListener('click', handler, false);
  }
}

export default {
  created(el, binding) {
    removeListener(el, binding.value);
    if (typeof binding.value === 'function') {
      addListener(el, binding.value);
    }
  },
  updated(el, binding) {
    if (binding.value !== binding.oldValue) {
      removeListener(el, binding.oldValue);
      addListener(el, binding.value);
    }
  },
  unmounted(el, binding) {
    removeListener(el, binding.value);
  },
};
