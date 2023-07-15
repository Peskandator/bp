$(document).ready(function(){
    $(`.modify-entity-user`).click(function (e) {
        let entityUserId = $(this).attr('data-entity-user-id');
        $(`.entity-user-id`).val(entityUserId);

        let entityUserName = $(this).attr('data-user-name');
        $(`.js-entity-user-name`).text(entityUserName);
    });
    $(`#deleteEntityUserConfirm`).click(function (e) {
        $(`.delete-entity-user-form`).submit();
    });
    $(`#appointEntityAdminConfirm`).click(function (e) {
        $(`.appoint-entity-admin-form`).submit();
    });

    $(`.delete-acquisition`).click(function (e) {
        let acquisitionId = $(this).attr('data-acquisition-id');
        $(`.acquisition-id`).val(acquisitionId);

        let acquisitionName = $(this).attr('data-acquisition-name');
        $(`.js-acquisition-name`).text(acquisitionName);
    });
    $(`.delete-location`).click(function (e) {
        let locationId = $(this).attr('data-location-id');
        $(`.location-id`).val(locationId);

        let locationName = $(this).attr('data-location-name');
        $(`.js-location-name`).text(locationName);
    });
    $(`.delete-place`).click(function (e) {
        let placeId = $(this).attr('data-place-id');
        $(`.place-id`).val(placeId);

        let placeName = $(this).attr('data-place-name');
        $(`.js-place-name`).text(placeName);
    });


    $(`#deleteAcquisitionConfirm`).click(function (e) {
        $(`.delete-acquisition-form`).submit();
    });
    $(`#deleteLocationConfirm`).click(function (e) {
        $(`.delete-location-form`).submit();
    });
    $(`#deletePlaceConfirm`).click(function (e) {
        $(`.delete-place-form`).submit();
    });

});