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

    $('#assetAcquisitionSelect').change(function(){
        let code = parseInt($('#assetAcquisitionSelect').find(':selected').attr('data-code'));
        if (code === 1) {
            $(`.js-invoice-content`).show();
        } else {
            $(`.js-invoice-content`).hide();
        }
    });


    let assetGroupTaxSelect = $('#assetGroupTaxSelect');
    assetGroupTaxSelect.change(function(){
        changeDepreicationGroup();
    });

    function changeDepreicationGroup(){
        let selectedOption = assetGroupTaxSelect.find(':selected');

        let rateFirstYear = selectedOption.attr('data-rate-first');
        let rate = selectedOption.attr('data-rate');
        let rateIncreasedPrice = selectedOption.attr('data-rate-increased');
        let years = selectedOption.attr('data-years');
        let months = selectedOption.attr('data-months');
        let isCoeff = selectedOption.attr('data-coeff');

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

    $('#assetCategorySelect').change(function(){
        let groupId = $('#assetCategorySelect').find(':selected').attr('data-group-id');

        if (groupId && groupId !== 0 && groupId !== '') {
            // TODO : CHANGE ONLY WHEN EDITING WITH ALERT YES!
            // var value = $('#assetGroupTaxSelect').find(":selected").val();
            // if (value === 0 || value === '0') {
                assetGroupTaxSelect.val(groupId);
                changeDepreicationGroup();
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
    typeSelect.change(function(){
        let selectedOption = typeSelect.find(':selected');

        let nextInventoryNumber = parseInt(selectedOption.attr('data-next-inventory-number'));
        if (nextInventoryNumber) {
            $('#assetInventoryNumber').val(nextInventoryNumber);

        }

        let typeCode = parseInt(selectedOption.attr('data-code'));
        let onlyTaxCheckbox = $('#jsOnlyTaxCheckbox');
        let taxContent = $('.js-tax-content');
        let accountingContent = $('.js-accounting-content');

        if (typeCode && typeCode !== 0) {
            if (typeCode === 2 || typeCode === 4) {
                taxContent.hide();
                accountingContent.hide();

            } else if (typeCode === 3) {
                taxContent.hide();
                accountingContent.show();
                onlyTaxCheckbox.hide();
            } else {
                taxContent.show();
                accountingContent.show();
                onlyTaxCheckbox.show();
            }
        }
    });
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

    $('#assetIncreaseDateTax').change(function(){
        let val = $(this).val();

        let year = val.substring(0, 4);

        if (currentYear >= year) {
            let increaseYear = currentYear - year + 1;
            $('#assetDepreciationIncreasedYear').val(increaseYear);
        }
    });

    $('#assetIncreaseDateAccounting').change(function(){
        let val = $(this).val();

        let year = val.substring(0, 4);

        if (currentYear >= year) {
            let increaseYear = currentYear - year + 1;
            $('#assetDepreciationIncreasedYearAccounting').val(increaseYear);
        }
    });

    let locationSelect = $('#assetLocationSelect');
    locationSelect.change(function(){
        let locationId = parseInt(locationSelect.find(':selected').attr('data-location-id'));
        if (locationId && locationId !== 0) {
            $('.place-option').each(function() {
                let locationIdPlace = parseInt($(this).attr('data-location-id'));
                if (locationIdPlace === 0 || locationIdPlace === locationId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })
        } else {
            $('.place-option').show();
        }
    });

    let placeSelect = $('#assetPlaceSelect');
    placeSelect.change(function(){
        let locationId =  parseInt(placeSelect.find(':selected').attr('data-location-id'));
        if (locationId && locationId !== 0) {
            locationSelect.val(locationId);
        }
    });

});

