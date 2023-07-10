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
});