document.addEventListener('readystatechange', function () {
    function listenForTermsAndConditionsApprovalChange() {
        var conditionsForm = document.getElementById('conditions-to-approve');

        var submitButton = document.querySelectorAll('#conditions-to-approve input[type="submit"]')[0];
        if (!submitButton) {
            return;
        }

        submitButton.style.display = 'none';
        var checkboxes = document.querySelectorAll('#conditions-to-approve input[type="checkbox"]');

        function submitForm () {
            conditionsForm.submit();
        }

        for (var i = 0, len = checkboxes.length; i < len; ++i) {
            var checkbox = checkboxes[i];
            checkbox.addEventListener('change', submitForm);
        }
    }

    if (document.readyState === "interactive") {
        listenForTermsAndConditionsApprovalChange();
    }
});
