export default function() {
    const selectorPrefix = '.js-page-depreciations';
    if ($(selectorPrefix).length === 0) {
        return;
    }

    $(`.js-edit-start-tax`).click(function () {
        let recordId = $(this).attr('data-record-id');
        $(`.js-edit-text-tax`).show();
        $(`.js-edit-input-tax`).hide();
        $(`.js-input-tax-` + recordId).show();
        $(`.js-text-tax-` + recordId).hide();
        $(this).toggle();
        $(this).siblings(".js-edit-input-tax").toggle();
    });

    $(`.js-edit-start-accounting`).click(function () {
        let recordId = $(this).attr('data-record-id');
        $(`.js-edit-text-accounting`).show();
        $(`.js-edit-input-accounting`).hide();
        $(`.js-input-accounting-` + recordId).show();
        $(`.js-text-accounting-` + recordId).hide();
        $(this).toggle();
        $(this).siblings(".js-edit-input-accounting").toggle();
    });

    $(`.edit-depreciationTax-button`).click(function (event) {
        event.preventDefault();
        let depreciationId = $(this).attr('data-depreciation-id');
        let depreciationAmount = $(`.depreciationTax-amount-` + depreciationId).val();
        let percentage = $(`.depreciationTax-percentage-` + depreciationId).val();
        let executable = $(`.depreciationTax-executable-` + depreciationId).is(':checked');

        $(`.form-tax-depreciation-id`).val(depreciationId);
        $(`.form-tax-depreciation-amount`).val(depreciationAmount);
        $(`.form-tax-depreciation-percentage`).val(percentage);
        if (executable === true) {
            $(`.form-tax-depreciation-executable`).prop("checked", true)
        } else {
            $(`.form-tax-depreciation-executable`).prop("checked", false)
        }
        $(`.edit-tax-depreciation-form`).submit();
    });

    $(`.edit-depreciationAccounting-button`).click(function (event) {
        event.preventDefault();
        let depreciationId = $(this).attr('data-depreciation-id');

        let depreciationAmount = $(`.depreciationAccounting-amount-` + depreciationId).val();
        let percentage = $(`.depreciationAccounting-percentage-` + depreciationId).val();
        let executable = $(`.depreciationAccounting-executable-` + depreciationId).is(':checked');

        $(`.form-accounting-depreciation-id`).val(depreciationId);
        $(`.form-accounting-depreciation-amount`).val(depreciationAmount);
        $(`.form-accounting-depreciation-percentage`).val(percentage);

        if (executable === true) {
            $(`.form-accounting-depreciation-executable`).prop("checked", true)
        } else {
            $(`.form-accounting-depreciation-executable`).prop("checked", false)
        }
        $(`.edit-accounting-depreciation-form`).submit();
    });

    setInterval(function () {
        $(`.js-amount-input-tax`).each(function () {
            if ($(this).is(':focus')) {
                changePercentageByAmountTax($(this));
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-percentage-input-tax`).each(function () {
            if ($(this).is(':focus')) {
                changeAmountByPercentageTax($(this));
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-amount-input-accounting`).each(function () {
            if ($(this).is(':focus')) {
                changePercentageByAmountAccounting($(this));
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-percentage-input-accounting`).each(function () {
            if ($(this).is(':focus')) {
                changeAmountByPercentageAccounting($(this));
            }
        })
    }, 200)

    $(`.js-amount-input-tax`).change(function () {
        changePercentageByAmountTax($(this));
    });

    $(`.js-percentage-input-tax`).change(function () {
        changeAmountByPercentageTax($(this));
    });

    $(`.js-amount-input-accounting`).change(function () {
        changePercentageByAmountAccounting($(this));
    });

    $(`.js-percentage-input-accounting`).change(function () {
        changeAmountByPercentageAccounting($(this));
    });

    function changePercentageByAmountTax(e) {
        let depreciationId = e.attr('data-depreciation-id');
        let percentageInput = $(`.depreciationTax-percentage-` + depreciationId);
        changePercentageByAmount(e, percentageInput);
    }

    function changeAmountByPercentageTax(e) {
        let depreciationId = e.attr('data-depreciation-id');
        let baseAmount = e.attr('data-base-amount');
        let newAmount = Math.round(e.val() * baseAmount) / 100;
        $(`.depreciationTax-amount-` + depreciationId).val(newAmount);
    }

    function changePercentageByAmountAccounting(e) {
        let depreciationId = e.attr('data-depreciation-id');
        let percentageInput = $(`.depreciationAccounting-percentage-` + depreciationId);
        changePercentageByAmount(e, percentageInput);
    }

    function changeAmountByPercentageAccounting(e) {
        let depreciationId = e.attr('data-depreciation-id');
        let baseAmount = e.attr('data-base-amount');
        let newAmount = Math.round(e.val() * baseAmount) / 100;
        $(`.depreciationAccounting-amount-` + depreciationId).val(newAmount);
    }

    function changePercentageByAmount(e, percentageInput) {
        let baseAmount = parseInt(e.attr('data-base-amount'));
        let newAmount = e.val();
        if (baseAmount === 0) {
            percentageInput.val(100);
            return;
        }
        let percentage = (newAmount / baseAmount * 100).toFixed(4);
        percentageInput.val(percentage);
    }

    $(`.js-execute-depreciations-button`).click(function () {
        let year = parseInt($(this).attr('data-execute-year'));
        $(`.js-modal-year`).text(year);
    });

    $(`.js-modal-execute-depreciations-confirm`).click(function () {
        $(`.js-execute-depreciations-form`).submit();
    });

    $(`.js-cancel-execution-button`).click(function () {
        let year = parseInt($(this).attr('data-execute-year'));
        $(`.js-modal-year`).text(year);
    });

    $(`.js-modal-cancel-execution-confirm`).click(function () {
        $(`.js-cancel-execution-form`).submit();
    });
};
