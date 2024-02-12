export default function() {
    const selectorPrefix = '.js-page-movements';
    if ($(selectorPrefix).length === 0) {
        return;
    }

    $(`.edit-movement-button`).click(function (event) {
        event.preventDefault();
        let movementId = $(this).attr('data-movement-id');
        let description = $(`.movement-description-` + movementId).val();
        let date = $(`.movement-date-` + movementId).val();
        let accDebited = $(`.movement-accDebited-` + movementId).val();
        let accCredited = $(`.movement-accCredited-` + movementId).val();

        $(`.form-movement-id`).val(movementId);
        $(`.form-movement-description`).val(description);
        $(`.form-movement-date`).val(date);
        $(`.form-movement-accDebited`).val(accDebited);
        $(`.form-movement-accCredited`).val(accCredited);
        $(`.edit-movement-form`).submit();
    });
}