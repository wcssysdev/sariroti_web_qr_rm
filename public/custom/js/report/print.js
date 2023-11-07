function printDiv(divID) {
    //Get the HTML of div
    var divElements = document.getElementById(divID).innerHTML;
    //Get the HTML of whole page
    var oldPage = document.body.innerHTML;

    //Reset the page's HTML with div's HTML only
    document.body.innerHTML = 
    "<html><head><title></title></head><body><br>" + 
    divElements + "</body>";

    //Print Page
    window.print();

    //Restore orignal HTML
    document.body.innerHTML = oldPage;


}

$(function () {
    $('#plant_select2').select2({
        placeholder: "Choose Plant",
        allowClear: true
    });
});