$(document).ready(function () {
    $('#demande_relance').change(function()  {
        if($(this).is(":checked")) {
           $('#divDelai').show('500');
        }
        else {
            $('#divDelai').hide('500');
        }
    });
});




