$(document).ready(function () {
    $('#demande_relance').change(function()  {
        if($(this).is(":checked")) {
           $('#divDelai').show();
        }
        else {
            $('#divDelai').hide();
        }
    });
});




