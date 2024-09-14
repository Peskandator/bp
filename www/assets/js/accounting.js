export default function() {
    const selectorPrefix = '.js-accounting-page';
    if ($(selectorPrefix).length === 0) {
        return;
    }
    $(`#regenerateDataConfirm`).click(function (e) {
        $(`.js-regenerate-form`).submit();
    });

    $(`#js-edit-accounting-data-form-submit`).click(function (e) {
        $(`#js-accounting-export-input`).val('');
        $(`.js-edit-accounting-data-form`).submit();
    });

    $(`#js-export-excel-button`).click(function (e) {
        $(`#js-accounting-export-input`).val('excel');
        $(`.js-edit-accounting-data-form`).submit();
    });

    $(`#js-export-dbf-button`).click(function (e) {
        $(`#js-accounting-export-input`).val('dbf');
        $(`.js-edit-accounting-data-form`).submit();
    });
}