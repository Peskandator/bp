
//Imports
import depreciations from './depreciations.js';
import assetForm from './assetPage.js';
import dials from './dials.js';

$(document).ready(function(){
    depreciations();
    assetForm();
    dials();

    setTimeout(flashMessage, 4200);
    function flashMessage() {
        $(".flash-message-alert").alert('close');
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

    $('*[data-tab-index]').on('keypress',function(e) {
        if(e.which === 13) {
            e.preventDefault();
            let index = $(this).attr('data-tab-index');
            index++;
            let newFocusedInput = $('*[data-tab-index='+ index + ']');
            if (!newFocusedInput.is(":disabled")) {
                newFocusedInput.focus();
            }
        }
    });

    $('*[data-tab-index-1]').on('keypress',function(e) {
        if(e.which === 13) {
            e.preventDefault();
            let index = $(this).attr('data-tab-index-1');
            index++;
            let newFocusedInput = $('*[data-tab-index-1='+ index + ']');
            if (!newFocusedInput.is(":disabled")) {
                newFocusedInput.focus();
            }
        }
    });
});

