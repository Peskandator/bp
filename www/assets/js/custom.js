
//Imports
import depreciations from './depreciations.js';
import assetForm from './assetPage.js';

$(document).ready(function(){
    depreciations();
    assetForm();

    setTimeout(flashMessage, 4200);
    function flashMessage() {
        $(".flash-message-alert").alert('close');
    }

    $(`.edit-acquisition-button`).click(function (event) {
        event.preventDefault();
        let acquisitionId = $(this).attr('data-acquisition-id');
        let selectedName = $(`.acquisition-name-` + acquisitionId).val();
        let selectedCode = $(`.acquisition-code-` + acquisitionId).val();

        $(`.form-acquisition-disposal`).prop("checked", false)

        $(`.form-acquisition-id`).val(acquisitionId);
        $(`.form-acquisition-name`).val(selectedName);
        $(`.form-acquisition-code`).val(selectedCode);
        $(`.edit-acquisition-form`).submit();
    });

    $(`.edit-disposal-button`).click(function (event) {
        event.preventDefault();
        let disposalId = $(this).attr('data-disposal-id');
        let selectedName = $(`.disposal-name-` + disposalId).val();
        let selectedCode = $(`.disposal-code-` + disposalId).val();

        $(`.form-acquisition-disposal`).prop("checked", true)

        $(`.form-acquisition-id`).val(disposalId);
        $(`.form-acquisition-name`).val(selectedName);
        $(`.form-acquisition-code`).val(selectedCode);
        $(`.edit-acquisition-form`).submit();
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

    $(`.edit-group-button`).click(function (event) {
        event.preventDefault();
        let groupId = $(this).attr('data-group-id');
        let method = $(`.group-method-` + groupId).find(":selected").val();
        let number = $(`.group-number-` + groupId).val();
        let prefix = $(`.group-prefix-` + groupId).val();
        let years = $(`.group-years-` + groupId).val();
        let months = $(`.group-months-` + groupId).val();
        let coeff = $(`.group-coeff-` + groupId).find(":selected").val();
        let first = $(`.group-first-` + groupId).val();
        let rate = $(`.group-rate-` + groupId).val();
        let increased = $(`.group-increased-` + groupId).val();

        $(`.form-group-id`).val(groupId);
        $(`.form-group-method`).val(method);
        $(`.form-group-number`).val(number);
        $(`.form-group-prefix`).val(prefix);
        $(`.form-group-years`).val(years);
        $(`.form-group-months`).val(months);
        $(`.form-group-coeff`).val(coeff);
        $(`.form-group-first`).val(first);
        $(`.form-group-rate`).val(rate);
        $(`.form-group-increased`).val(increased);
        $(`.edit-group-form`).submit();
    });

    $(`.edit-category-button`).click(function (event) {
        event.preventDefault();
        let categoryId = $(this).attr('data-category-id');
        let code = $(`.category-code-` + categoryId).val();
        let categoryName = $(`.category-name-` + categoryId).val();
        let group = $(`.category-group-` + categoryId).find(":selected").val();
        let accAsset = $(`.category-acc-asset-` + categoryId).val();
        let accDepreciation = $(`.category-acc-depreciation-` + categoryId).val();
        let accRepairs = $(`.category-acc-repairs-` + categoryId).val();
        let isDepreciable = $(`.category-depreciable-` + categoryId).is(':checked');

        $(`.form-category-id`).val(categoryId);
        $(`.form-category-code`).val(code);
        $(`.form-category-name`).val(categoryName);
        $(`.form-category-group`).val(group);
        $(`.form-category-acc-asset`).val(accAsset);
        $(`.form-category-acc-depreciation`).val(accDepreciation);
        $(`.form-category-acc-repairs`).val(accRepairs);
        if (isDepreciable === true) {
            $(`.form-category-depreciable`).prop("checked", true)
        }
        $(`.edit-category-form`).submit();
    });

    $(`.js-depreciable-checkbox`).each(function () {
        checkCheckbox($(this));
        $(this).change(function () {
            checkCheckbox($(this));
        });
    });

    function checkCheckbox(checkBox) {
        const selectorPrefix = '.js-category-form-card';
        if (checkBox.prop("checked") === true) {
            $(selectorPrefix).find('.js-depreciable-true').show();
        } else {
            $(selectorPrefix).find('.js-depreciable-true').hide();
        }
    }

    $(`.js-edit-depreciable-checkbox`).each(function () {
        let categoryId = $(this).attr('data-category-id');

        checkDepreciableCheckbox($(this), categoryId);
        $(this).change(function () {
            checkDepreciableCheckbox($(this), categoryId);
        });
    });

    function checkDepreciableCheckbox(checkBox, categoryId) {
        const selectorPrefix = '.js-edit-category-table';

        if (checkBox.prop("checked") === true) {
            $(selectorPrefix).find(`.js-edit-depreciable-true-` + categoryId).css("visibility","visible");

        } else {
            $(selectorPrefix).find(`.js-edit-depreciable-true-` + categoryId).css("visibility","hidden");
        }
    }

    $(`.js-edit-start`).click(function () {
        let recordId = $(this).attr('data-record-id');
        $(`.js-edit-text`).show();
        $(`.js-edit-input`).hide();
        $(`.js-input-` + recordId).show();
        $(`.js-text-` + recordId).hide();
        $(this).toggle();
        $(this).siblings(".js-edit-input").toggle();
    });

    $(`.edit-assettype-button`).click(function (event) {
        event.preventDefault();
        let id = $(this).attr('data-assettype-id');
        let selectedSeries = $(`.assettype-series-` + id).val();
        let selectedStep = $(`.assettype-step-` + id).val();

        $(`.form-assettype-id`).val(id);
        $(`.form-assettype-series`).val(selectedSeries);
        $(`.form-assettype-step`).val(selectedStep);
        $(`.edit-assettype-form`).submit();
    });

    let coefficientSelect = $('#groupCoeffSelect');
    let groupMethodSelect = $('#groupMethodSelect');
    let ownMethod = coefficientSelect.find(`option[value="2"]`);
    groupMethodSelect.change(changeGroupMethodSelect);
    function changeGroupMethodSelect() {
        let selected = parseInt(groupMethodSelect.find(':selected').val());
        toggleOwnWayOption(selected);
        if (selected === 1 || selected === 3) {
            coefficientSelect.prop("disabled", true);
            coefficientSelect.val(0);
        }
        else if (selected === 2) {
            coefficientSelect.prop("disabled", true);
            coefficientSelect.val(1);
        } else {
            coefficientSelect.prop("disabled", false);
        }
    }

    toggleOwnWayOption(0);
    function toggleOwnWayOption(selected) {
        if (selected === 4) {
            ownMethod.prop("disabled", false);
        } else {
            ownMethod.prop("disabled", true);
        }
    }

    let editGroupMethodSelects = $('[id^="edit-group-method-select-"]');
    if ($(`.js-dials-groups-page`).length > 0) {
        editGroupMethodSelects.each(
            changeEditGroupMethodSelect
        )
    }

    editGroupMethodSelects.change(changeEditGroupMethodSelect);
    function changeEditGroupMethodSelect() {
        let groupSelect = $(this);
        let selected = parseInt(groupSelect.find(':selected').val());
        let editedGroupId = groupSelect.attr('data-group-id');

        let editedGroupCoeffSelectClass = 'group-coeff-' + editedGroupId;
        let editedGroupCoeffSelect = $('.' + editedGroupCoeffSelectClass)
        if (selected === 1) {
            editedGroupCoeffSelect.prop("disabled", true);
            editedGroupCoeffSelect.val(0);
        } else if (selected === 2) {
            editedGroupCoeffSelect.prop("disabled", true);
            editedGroupCoeffSelect.val(1);
        } else {
            editedGroupCoeffSelect.prop("disabled", false);
        }
    }

    $('*[data-tab-index]').on('keypress',function(e) {
        if(e.which === 13) {
            e.preventDefault();
            let index = $(this).attr('data-tab-index');
            index++;
            $('*[data-tab-index='+ index + ']').focus();
        }
    });
});

