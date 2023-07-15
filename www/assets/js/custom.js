$(document).ready(function(){
    const flashTimeout = setTimeout(flashMessage, 4200);
    function flashMessage() {
        $(".flash-message-alert").alert('close');
    }

    $(`.edit-acquisition-icon`).click(function () {
        let acquisitionId = $(this).attr('data-acquisition-id');

        $(`.acquisition-name-` + acquisitionId).toggle();
        $(`.acquisition-code-` + acquisitionId).toggle();

        $(`.acquisition-name-text-` + acquisitionId).toggle();
        $(`.acquisition-code-text-` + acquisitionId).toggle();
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

        $(`.location-name-` + locationId).toggle();
        $(`.location-code-` + locationId).toggle();

        $(`.location-name-text-` + locationId).toggle();
        $(`.location-code-text-` + locationId).toggle();
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

        $(`.place-name-` + placeId).toggle();
        $(`.place-code-` + placeId).toggle();
        $(`.place-location-` + placeId).toggle();

        $(`.place-name-text-` + placeId).toggle();
        $(`.place-code-text-` + placeId).toggle();
        $(`.place-location-text-` + placeId).toggle();
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

