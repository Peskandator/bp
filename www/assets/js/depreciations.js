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

        $(`.form-depreciation-id`).val(depreciationId);
        $(`.form-depreciation-amount`).val(depreciationAmount);
        $(`.form-depreciation-percentage`).val(percentage);
        if (executable === true) {
            $(`.form-depreciation-executable`).prop("checked", true)
        } else {
            $(`.form-depreciation-executable`).prop("checked", false)
        }
        $(`.form-depreciation-isAccounting`).prop("checked", false);
        $(`.edit-depreciation-form`).submit();
    });

    $(`.edit-depreciationAccounting-button`).click(function (event) {
        event.preventDefault();
        let depreciationId = $(this).attr('data-depreciation-id');

        let depreciationAmount = $(`.depreciationAccounting-amount-` + depreciationId).val();
        let percentage = $(`.depreciationAccounting-percentage-` + depreciationId).val();
        let executable = $(`.depreciationAccounting-executable-` + depreciationId).is(':checked');

        $(`.form-depreciation-id`).val(depreciationId);
        $(`.form-depreciation-amount`).val(depreciationAmount);
        $(`.form-depreciation-percentage`).val(percentage);

        if (executable === true) {
            $(`.form-depreciation-executable`).prop("checked", true)
        } else {
            $(`.form-depreciation-executable`).prop("checked", false)
        }
        $(`.form-depreciation-isAccounting`).prop("checked", true);
        $(`.edit-depreciation-form`).submit();
    });
};
