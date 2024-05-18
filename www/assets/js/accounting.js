export default function() {
    const selectorPrefix = '.js-accounting-page';
    if ($(selectorPrefix).length === 0) {
        return;
    }

    $(`#regenerateDataConfirm`).click(function (e) {
        $(`.js-regenerate-form`).submit();
    });
}