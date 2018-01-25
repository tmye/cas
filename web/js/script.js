$(function(){
    $(".dropdown-button").dropdown();
    $('.collapsible').collapsible();
    $('.tap-target').tapTarget('open');
    $('.tap-target').tapTarget('close');
    $('select').material_select();
    $(".button-collapse").sideNav();
    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 15, // Creates a dropdown of 15 years to control year,
        today: 'Today',
        clear: 'Clear',
        close: 'Ok',
        closeOnSelect: false // Close upon selecting a date,
    });
});