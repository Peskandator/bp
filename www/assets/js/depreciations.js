export default function() {
    const selectorPrefix = '.js-page-depreciations';
    if ($(selectorPrefix).length === 0) {
        return;
    }

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
};
