$(document).ready(function(){
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

        let isDepreciable = $(`.category-depreciable-` + categoryId)[0].checked;
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

    $(`.js-only-tax-checkbox`).each(function () {
        checkOnlyTaxCheckbox($(this));
        $(this).change(function () {
            checkOnlyTaxCheckbox($(this));
        });
    });

    function checkOnlyTaxCheckbox(checkBox) {
        if (checkBox.prop("checked") === true) {
            $(`.js-only-tax-false`).hide();
        } else {
            $(`.js-only-tax-false`).show();
        }
    }

    const acquisitionSelect = $('#assetAcquisitionSelect');
    const acquisitionCodeJson = acquisitionSelect.attr('data-codes-json');
    const acquisitionCodes = jQuery.parseJSON(acquisitionCodeJson);
    acquisitionSelect.change(function(){
        let acquisitionId = parseInt(acquisitionSelect.find(':selected').val());
        let code = acquisitionCodes[acquisitionId];
        if (code === 1) {
            $(`.js-invoice-content`).show();
        } else {
            $(`.js-invoice-content`).hide();
        }
    });

    let assetGroupTaxSelect = $('#assetGroupTaxSelect');

    const groupInfoJson = assetGroupTaxSelect.attr('data-groups-info-json');
    const groupInfoObj = jQuery.parseJSON(groupInfoJson);

    assetGroupTaxSelect.change(function(){
        changeDepreciationGroup();
    });

    function changeDepreciationGroup(){
        let groupId = assetGroupTaxSelect.find(':selected').val();

        let rateFirstYear = groupInfoObj[groupId]['rate-first'];
        let rate = groupInfoObj[groupId]['rate'];
        let rateIncreasedPrice = groupInfoObj[groupId]['rate-increased'];
        let years = groupInfoObj[groupId]['years'];
        let months = groupInfoObj[groupId]['months'];
        let isCoeff = groupInfoObj[groupId]['coeff'];

        if (isCoeff === '1') {
            $('.assetGroupTaxPerc').hide();
            $('.assetGroupTaxKoef').show();
        } else {
            $('.assetGroupTaxPerc').show();
            $('.assetGroupTaxKoef').hide();
        }

        if (months && months !== '' && months !== 0) {
            $('#assetGroupTaxMonthsText').show()
            $('#assetGroupTaxYearsText').hide()
            $('#assetGroupTaxYearsMonths').val(months)
        } else {
            $('#assetGroupTaxMonthsText').hide()
            $('#assetGroupTaxYearsText').show()
            $('#assetGroupTaxYearsMonths').val(years)
        }

        $('#assetGroupTax1').val(rateFirstYear);
        $('#assetGroupTax2').val(rate);
        $('#assetGroupTax3').val(rateIncreasedPrice);
    }

    const categorySelect = $('#assetCategorySelect')
    const categoriesGroupsJson = categorySelect.attr('data-categories-groups-json');
    const categoriesGroupsObj = jQuery.parseJSON(categoriesGroupsJson);

    categorySelect.change(function(){
        let categoryId = categorySelect.find(':selected').val();
        let groupId = categoriesGroupsObj[categoryId];

        if (groupId && groupId !== 0 && groupId !== '') {
            // TODO : CHANGE ONLY WHEN EDITING WITH ALERT YES!
            // var value = $('#assetGroupTaxSelect').find(":selected").val();
            // if (value === 0 || value === '0') {
            assetGroupTaxSelect.val(groupId);
            changeDepreciationGroup();
            // }
        }
    });

    let entryPriceTaxInput = $('#assetEntryPriceTax');
    let increasedPriceTaxInput = $('#assetIncreasedPriceTax');
    let depreciatedAmountTaxInput = $('#assetDepreciatedAmountTax');

    entryPriceTaxInput.change(function(){
        calculateResidualPriceTax();
    });
    increasedPriceTaxInput.change(function(){
        calculateResidualPriceTax();
    });
    depreciatedAmountTaxInput.change(function(){
        calculateResidualPriceTax();
    });

    if (entryPriceTaxInput.length > 0) {
        calculateResidualPriceTax();
    }

    function calculateResidualPriceTax() {
        let entryPrice = entryPriceTaxInput.val();
        let increasedPrice = increasedPriceTaxInput.val();
        let depreciatedAmount = depreciatedAmountTaxInput.val();

        let isEntryPriceNumeric = $.isNumeric(entryPrice);
        let isIncreasedPriceNumeric = $.isNumeric(increasedPrice);

        if (isEntryPriceNumeric || isIncreasedPriceNumeric && $.isNumeric(depreciatedAmount)) {
            let firstValue = 0;
            if (isEntryPriceNumeric) {
                firstValue = entryPrice;
            }
            if (isIncreasedPriceNumeric) {
                firstValue = increasedPrice;
            }
            let residualPrice = firstValue - depreciatedAmount;
            $('#assetLeftAmountTax').val(residualPrice);
        }
    }

    let entryPriceAccountingInput = $('#assetEntryPriceAccounting');
    let increasedPriceAccountingInput = $('#assetIncreasedPriceAccounting');
    let depreciatedAmountAccountingInput = $('#assetDepreciatedAmountAccounting');

    entryPriceAccountingInput.change(function(){
        calculateResidualPriceAccounting();
    });
    increasedPriceAccountingInput.change(function(){
        calculateResidualPriceAccounting();
    });
    depreciatedAmountAccountingInput.change(function(){
        calculateResidualPriceAccounting();
    });

    if (entryPriceAccountingInput.length > 0) {
        calculateResidualPriceAccounting();
    }
    function calculateResidualPriceAccounting() {
        let entryPrice = entryPriceAccountingInput.val();
        let increasedPrice = increasedPriceAccountingInput.val();
        let depreciatedAmount = depreciatedAmountAccountingInput.val();

        let isEntryPriceNumeric = $.isNumeric(entryPrice);
        let isIncreasedPriceNumeric = $.isNumeric(increasedPrice);

        if (isEntryPriceNumeric || isIncreasedPriceNumeric && $.isNumeric(depreciatedAmount)) {
            let firstValue = 0;
            if (isEntryPriceNumeric) {
                firstValue = entryPrice;
            }
            if (isIncreasedPriceNumeric) {
                firstValue = increasedPrice;
            }
            let residualPrice = firstValue - depreciatedAmount;
            $('#assetLeftAmountAccounting').val(residualPrice);
        }
    }

    let typeSelect = $('#assetTypeSelect');
    const assetTypeCodesJson = typeSelect.attr('data-codes-json');
    const assetTypeCodes = jQuery.parseJSON(assetTypeCodesJson);
    const nextInventoryNumbersJson = typeSelect.attr('data-next-inventory-numbers-json');
    const nextInventoryNumbers = jQuery.parseJSON(nextInventoryNumbersJson);

    typeSelect.change(function(){
        changeAssetTypeSelect();
    });

    changeAssetTypeSelect();
    function changeAssetTypeSelect() {
        let assetTypeId = typeSelect.find(':selected').val();

        let nextInventoryNumber = parseInt(nextInventoryNumbers[assetTypeId]);
        if (nextInventoryNumber) {
            $('#assetInventoryNumber').val(nextInventoryNumber);
        }

        let typeCode = parseInt(assetTypeCodes[assetTypeId]);
        let onlyTaxCheckbox = $('#jsOnlyTaxCheckbox');
        let onlyTaxCheckboxLabel = $('#only-tax-label');
        let onlyTaxCheckboxLabelSmall = $('#only-tax-label-small');
        let taxContent = $('.js-tax-content');
        let accountingContent = $('.js-accounting-content');

        if (typeCode && typeCode !== 0) {
            if (typeCode === 2 || typeCode === 4) {
                taxContent.hide();
                accountingContent.hide();
                onlyTaxCheckboxLabelSmall.hide();
            } else if (typeCode === 3) {
                taxContent.hide();
                accountingContent.show();
                onlyTaxCheckbox.show();
                onlyTaxCheckboxLabel.hide()
                onlyTaxCheckboxLabelSmall.show();
            } else {
                taxContent.show();
                accountingContent.show();
                onlyTaxCheckbox.show();
                onlyTaxCheckboxLabel.show()
                onlyTaxCheckboxLabelSmall.hide();
            }
        }
    }

    let currentYear = new Date().getFullYear();
    $('#assetEntryDate').change(function(){
        let val = $(this).val();

        let year = val.substring(0, 4);

        if (currentYear >= year) {
            let depreciationYear = currentYear - year + 1;
            $('#assetDepreciationYear').val(depreciationYear);
            $('#assetDepreciationYearAccounting').val(depreciationYear);
        }
    });


    let placeSelect = $('#assetPlaceSelect');
    let locationSelect = $('#assetLocationSelect');
    const placesLocationsJson = locationSelect.attr('data-places-locations-json');
    const placesLocationsObj = jQuery.parseJSON(placesLocationsJson);

    locationSelect.change(changeLocationSelect);
    function changeLocationSelect() {
        let locationId = parseInt(locationSelect.find(':selected').val());
        if (locationId && locationId !== 0) {
            $(placeSelect).find("option").each(function() {
                let placeId = parseInt($(this).val());
                let locationIdPlace = placesLocationsObj[placeId];
                if (locationIdPlace === 0 || locationIdPlace === locationId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })
        } else {
            $(placeSelect).find("option").each(function() {
                $(this).show();
            });
        }
    }

    placeSelect.change(function(){
        let locationId =  parseInt(placeSelect.find(':selected').attr('data-location-id'));
        if (locationId && locationId !== 0) {
            locationSelect.val(locationId);
        }
    });

});

