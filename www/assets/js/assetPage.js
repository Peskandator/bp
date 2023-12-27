export default function() {
    const selectorPrefix = '.js-asset-form';
    if ($(selectorPrefix).length === 0) {
        return;
    }

    if ($(`.js-asset-form`).length > 0) {
        $(`.js-only-tax-checkbox`).each(function () {
            checkOnlyTaxCheckbox($(this));
            $(this).change(function () {
                checkOnlyTaxCheckbox($(this));
            });
        });

        function checkOnlyTaxCheckbox(checkbox) {
            if (checkbox.prop("checked") === true) {
                $(`.js-only-tax-false`).hide();
            } else {
                $(`.js-only-tax-false`).show();
            }
        }

        $(`.js-has-tax-depreciations-checkbox`).each(function () {
            checkHasTaxDepreciationsCheckbox($(this));
            $(this).change(function () {
                checkHasTaxDepreciationsCheckbox($(this));
            });
        });
        function checkHasTaxDepreciationsCheckbox(checkbox) {
            if (checkbox.prop("checked") === false) {
                $(`.js-tax-depreciations-content`).hide();
            } else {
                $(`.js-tax-depreciations-content`).show();
            }
        }


        const acquisitionSelect = $('#assetAcquisitionSelect');
        const acquisitionCodeJson = acquisitionSelect.attr('data-codes-json');
        const acquisitionCodes = jQuery.parseJSON(acquisitionCodeJson);
        acquisitionSelect.change(function(){
            changeAcquisition();
        });

        function changeAcquisition(){
            let acquisitionId = parseInt(acquisitionSelect.find(':selected').val());
            let code = acquisitionCodes[acquisitionId];
            if (code === 1) {
                $(`.js-invoice-content`).show();
            } else {
                $(`.js-invoice-content`).hide();
            }
        }

        let assetGroupTaxSelect = $('#assetGroupTaxSelect');
        const groupInfoJson = assetGroupTaxSelect.attr('data-groups-info-json');
        const groupInfoObj = jQuery.parseJSON(groupInfoJson);

        assetGroupTaxSelect.change(function(){
            changeDepreciationGroupTax();
        });

        function changeDepreciationGroupTax(){
            let groupId = assetGroupTaxSelect.find(':selected').val();

            let rateFirstYear = "";
            let rate = "";
            let rateIncreasedPrice = "";
            let years = "";
            let months = "";
            let rateFormat = 1;

            if (groupId && groupId !== 0 && groupInfoObj[groupId]) {
                rateFirstYear = groupInfoObj[groupId]['rate-first'];
                rate = groupInfoObj[groupId]['rate'];
                rateIncreasedPrice = groupInfoObj[groupId]['rate-increased'];
                years = groupInfoObj[groupId]['years'];
                months = groupInfoObj[groupId]['months'];
                rateFormat = parseInt(groupInfoObj[groupId]['rate-format']);
            }

            if (rateFormat === 2) {
                $('.assetGroupTaxPerc').hide();
                $('.assetGroupTaxKoef').show();
            } else {
                $('.assetGroupTaxPerc').show();
                $('.assetGroupTaxKoef').hide();
            }

            if (months && months !== '' && months !== '0') {
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

            if (groupId === '') {
                groupId = 0;
            }

            // TODO : CHANGE ONLY WHEN EDITING WITH ALERT YES!
            // var value = $('#assetGroupTaxSelect').find(":selected").val();
            // if (value === 0 || value === '0') {
            assetGroupTaxSelect.val(groupId);
            changeDepreciationGroupTax();
            // }
        });

        let assetGroupAccountingSelect = $('#assetGroupAccountingSelect');
        const groupInfoJsonAccounting = assetGroupAccountingSelect.attr('data-groups-info-json');
        const groupInfoObjAccounting = jQuery.parseJSON(groupInfoJsonAccounting);
        const assetGroupAccountingRates = $('#assetGroupAccountingRates');

        assetGroupAccountingSelect.change(function(){
            changeDepreciationGroupAccounting();
        });

        function changeDepreciationGroupAccounting(){
            let groupId = assetGroupAccountingSelect.find(':selected').val();

            let rateFirstYear = "";
            let rate = "";
            let rateIncreasedPrice = "";
            let years = "";
            let months = "";
            let rateFormat = 1;

            if (groupId && groupId !== 0 && groupInfoObjAccounting[groupId]) {
                rateFirstYear = groupInfoObjAccounting[groupId]['rate-first'];
                rate = groupInfoObjAccounting[groupId]['rate'];
                rateIncreasedPrice = groupInfoObjAccounting[groupId]['rate-increased'];
                years = groupInfoObjAccounting[groupId]['years'];
                months = groupInfoObjAccounting[groupId]['months'];
                rateFormat = parseInt(groupInfoObjAccounting[groupId]['rate-format']);
            }

            if (rateFormat === 3) {
                assetGroupAccountingRates.hide();
            } else {
                assetGroupAccountingRates.show();
            }

            if (rateFormat === 2) {
                $('.assetGroupAccountingPerc').hide();
                $('.assetGroupAccountingKoef').show();
            } else {
                $('.assetGroupAccountingPerc').show();
                $('.assetGroupAccountingKoef').hide();
            }

            if (months && months !== '' && months !== '0') {
                $('#assetGroupAccountingMonthsText').show()
                $('#assetGroupAccountingYearsText').hide()
                $('#assetGroupAccountingYearsMonths').val(months)
            } else {
                $('#assetGroupAccountingMonthsText').hide()
                $('#assetGroupAccountingYearsText').show()
                $('#assetGroupAccountingYearsMonths').val(years)
            }

            $('#assetGroupAccounting1').val(rateFirstYear);
            $('#assetGroupAccounting2').val(rate);
            $('#assetGroupAccounting3').val(rateIncreasedPrice);
        }

        let entryPriceInput = $('#assetEntryPrice');
        let increasedPriceInput = $('#assetIncreasedPrice');
        let depreciatedAmountTaxInput = $('#assetDepreciatedAmountTax');

        entryPriceInput.change(function(){
            calculateResidualPriceTax();
            calculateResidualPriceAccounting();
        });
        increasedPriceInput.change(function(){
            calculateResidualPriceTax();
            calculateResidualPriceAccounting();
        });
        depreciatedAmountTaxInput.change(function(){
            calculateResidualPriceTax();
        });


        function calculateResidualPriceTax() {
            let entryPrice = entryPriceInput.val();
            let increasedPrice = increasedPriceInput.val();
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

        let depreciatedAmountAccountingInput = $('#assetDepreciatedAmountAccounting');
        depreciatedAmountAccountingInput.change(function(){
            calculateResidualPriceAccounting();
        });

        function calculateResidualPriceAccounting() {
            let entryPrice = entryPriceInput.val();
            let increasedPrice = increasedPriceInput.val();
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

            if (assetTypeId === 0) {
                return;
            }

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

                let selectedPlace = placeSelect.find(':selected').val();
                if (selectedPlace) {
                    let selectedPlaceLocationId = placesLocationsObj[selectedPlace];
                    if (locationId !== selectedPlaceLocationId) {
                        placeSelect.val(0)
                    }
                }

                placeSelect.find("option").each(function() {
                    let placeId = parseInt($(this).val());
                    let locationIdPlace = placesLocationsObj[placeId];
                    if (!locationIdPlace || locationIdPlace === locationId) {
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
            let placeId = parseInt(placeSelect.find(':selected').val());
            let locationId = placesLocationsObj[placeId];

            if (locationId && locationId !== 0) {

                locationSelect.val(locationId);
            }
        });

        if ($(`.js-edit-asset-page`).length > 0) {
            changeAssetTypeSelect();
            changeDepreciationGroupTax();
            changeDepreciationGroupAccounting();
            changeAcquisition();
            calculateResidualPriceTax();
            calculateResidualPriceAccounting();
        }
    }
};