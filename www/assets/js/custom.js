$(document).ready(function(){
    const flashTimeout = setTimeout(flashMessage, 4200);
    function flashMessage() {
        $(".flash-message-alert").alert('close');
    }

    $(`.edit-acquisition-icon`).click(function () {
        let acquisitionId = $(this).attr('data-acquisition-id');

        $(`.js-edit-text`).show();
        $(`.js-edit-input`).hide();
        $(`.acquisition-name-` + acquisitionId).show();
        $(`.acquisition-code-` + acquisitionId).show();
        $(`.acquisition-name-text-` + acquisitionId).hide();
        $(`.acquisition-code-text-` + acquisitionId).hide();

        $(this).toggle();
        $(this).siblings(".edit-acquisition-button").toggle();
    });

    $(`.edit-acquisition-button`).click(function (event) {
        event.preventDefault();
        let acquisitionId = $(this).attr('data-acquisition-id');
        let selectedName = $(`.acquisition-name-` + acquisitionId).val();
        let selectedCode = $(`.acquisition-code-` + acquisitionId).val();

        $(`.form-acquisition-id`).val(acquisitionId);
        $(`.form-acquisition-name`).val(selectedName);
        $(`.form-acquisition-code`).val(selectedCode);
        $(`.edit-acquisition-form`).submit();
    });

    $(`.edit-location-icon`).click(function () {
        let locationId = $(this).attr('data-location-id');

        $(`.js-edit-text`).show();
        $(`.js-edit-input`).hide();
        $(`.location-name-` + locationId).show();
        $(`.location-code-` + locationId).show();
        $(`.location-name-text-` + locationId).hide();
        $(`.location-code-text-` + locationId).hide();
        $(this).toggle();
        $(this).siblings(".edit-location-button").toggle();
    });

    $(`.edit-location-button`).click(function (event) {
        event.preventDefault();
        let locationId = $(this).attr('data-location-id');
        let selectedName = $(`.location-name-` + locationId).val();
        let selectedCode = $(`.location-code-` + locationId).val();

        $(`.form-location-id`).val(locationId);
        $(`.form-location-name`).val(selectedName);
        $(`.form-location-code`).val(selectedCode);
        $(`.edit-location-form`).submit();
    });

    $(`.edit-place-icon`).click(function () {
        let placeId = $(this).attr('data-place-id');

        $(`.js-edit-text`).show();
        $(`.js-edit-input`).hide();
        $(`.place-name-` + placeId).show();
        $(`.place-code-` + placeId).show();
        $(`.place-location-` + placeId).show();
        $(`.place-name-text-` + placeId).hide();
        $(`.place-code-text-` + placeId).hide();
        $(`.place-location-text-` + placeId).hide();
        $(this).toggle();
        $(this).siblings(".edit-place-button").toggle();
    });

    $(`.edit-place-button`).click(function (event) {
        event.preventDefault();
        let placeId = $(this).attr('data-place-id');
        let selectedName = $(`.place-name-` + placeId).val();
        let selectedCode = $(`.place-code-` + placeId).val();
        let selectedLocation = $(`.place-location-` + placeId).find(":selected").val();

        $(`.form-place-id`).val(placeId);
        $(`.form-place-name`).val(selectedName);
        $(`.form-place-code`).val(selectedCode);
        $(`.form-place-location`).val(selectedLocation);
        $(`.edit-place-form`).submit();
    });
});

