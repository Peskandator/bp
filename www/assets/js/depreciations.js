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
                let depreciationId = $(this).attr('data-depreciation-id');
                let baseAmount = $(this).attr('data-base-amount');
                let newAmount = $(this).val();

                let percentage = (newAmount / baseAmount * 100).toFixed(4);
                $(`.depreciationTax-percentage-` + depreciationId).val(percentage);
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-percentage-input-tax`).each(function () {
            if ($(this).is(':focus')) {
                let depreciationId = $(this).attr('data-depreciation-id');
                let baseAmount = $(this).attr('data-base-amount');
                let newAmount = Math.round($(this).val() * baseAmount) / 100;

                $(`.depreciationTax-amount-` + depreciationId).val(newAmount);
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-amount-input-accounting`).each(function () {
            if ($(this).is(':focus')) {
                let depreciationId = $(this).attr('data-depreciation-id');
                let baseAmount = $(this).attr('data-base-amount');
                let newAmount = $(this).val();

                let percentage = (newAmount / baseAmount * 100).toFixed(4);
                $(`.depreciationAccounting-percentage-` + depreciationId).val(percentage);
            }
        })
    }, 200)

    setInterval(function () {
        $(`.js-percentage-input-accounting`).each(function () {
            if ($(this).is(':focus')) {
                let depreciationId = $(this).attr('data-depreciation-id');
                let baseAmount = $(this).attr('data-base-amount');
                let newAmount = Math.round($(this).val() * baseAmount) / 100;

                $(`.depreciationAccounting-amount-` + depreciationId).val(newAmount);
            }
        })
    }, 200)

    $(`.js-amount-input-tax`).change(function () {
        let depreciationId = $(this).attr('data-depreciation-id');
        let baseAmount = $(this).attr('data-base-amount');
        let newAmount = $(this).val();

        let percentage = (newAmount / baseAmount * 100).toFixed(4) ;

        $(`.depreciationTax-percentage-` + depreciationId).val(percentage);
    });

    $(`.js-percentage-input-tax`).change(function () {
        let depreciationId = $(this).attr('data-depreciation-id');
        let baseAmount = $(this).attr('data-base-amount');
        let newAmount = Math.round($(this).val() * baseAmount) / 100;

        $(`.depreciationTax-amount-` + depreciationId).val(newAmount);
    });

    $(`.js-amount-input-accounting`).change(function () {
        let depreciationId = $(this).attr('data-depreciation-id');
        let baseAmount = $(this).attr('data-base-amount');
        let newAmount = $(this).val();
        let percentage = (newAmount / baseAmount * 100).toFixed(4) ;

        $(`.depreciationAccounting-percentage-` + depreciationId).val(percentage);
    });

    $(`.js-percentage-input-accounting`).change(function () {
        let depreciationId = $(this).attr('data-depreciation-id');
        let baseAmount = $(this).attr('data-base-amount');
        let newAmount = Math.round($(this).val() * baseAmount) / 100;

        $(`.depreciationAccounting-amount-` + depreciationId).val(newAmount);
    });

    // TODO
    // function changePercentageByAmount(e) {
    //     let depreciationId = $(this).attr('data-depreciation-id');
    //     let baseAmount = $(this).attr('data-base-amount');
    //     let newAmount = $(this).val();
    //     let percentage = (newAmount / baseAmount * 100).toFixed(4) ;
    //
    //     $(`.depreciationAccounting-percentage-` + depreciationId).val(percentage);
    //
    // }
};
