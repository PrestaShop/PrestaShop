import $ from 'jquery';

export default function() {
  $(document).ready(function() {
    let $jsCombinationsList = $('.js-combinations-list');
    let idsProductAttribute = $jsCombinationsList.data('ids-product-attribute').split(',');
    let idsCount = idsProductAttribute.length;
    let currentCount = 0;
    let step = 5;

    let combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');

    let getCombinations = () => {
      $.get(combinationUrl).then(function (resp) {
        $jsCombinationsList.append(resp);
        currentCount += step;
        combinationUrl = $jsCombinationsList.data('combinations-url') + '/' + idsProductAttribute.slice(currentCount, currentCount+step).join('-');
        if (currentCount <= idsCount) {
          getCombinations();
        }
      });
    };
    getCombinations();
  });
}
