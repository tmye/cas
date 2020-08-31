// Functions

$(function(){

    // Fonction de conversion
    function precisionRound(number, precision) {
        var factor = Math.pow(10, precision);
        return Math.round(number * factor) / factor;
    }

    //$('.collapsible').collapsible();
    $('.tap-target').tapTarget('show');
    $('.tap-target').tapTarget('hide');
    $('select').material_select();
    $(".button-collapse").sideNav();
    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year,
        today: 'Today',
        clear: 'Clear',
        format: 'dd-mm-yyyy',
        close: 'Ok',
        closeOnSelect: false // Close upon selecting a date,
    });

    $('.timepicker').pickatime({
        default: 'now', // Set default time: 'now', '1:30AM', '16:30'
        twelvehour: false, // Use AM/PM or 24-hour format
        donetext: 'OK', // text for done-button
        cleartext: 'Effacer', // text for clear-button
        canceltext: 'Annuler', // Text for cancel-button
        autoclose: false, // automatic close timepicker
        ampmclickable: true, // make AM PM clickable
        aftershow: function(){} //Function for after opening timepicker
    });
    $('.modal').modal();
    $('.ajax_modals').modal({
        opacity: .6
    });
    $('.tooltipped').tooltip();
});