(function($) {
    $(document).ready(function() {
        if (!$('#zikulaformiculamodule_config_enableSpamCheck').prop('checked')) {
            $('#formiculaSpamCheckDetails').addClass('d-none');
        }
        $('#zikulaformiculamodule_config_enableSpamCheck').change(function() {
            $('#formiculaSpamCheckDetails').toggleClass('d-none', !$('#zikulaformiculamodule_config_enableSpamCheck').prop('checked'));
        });

        if (!$('#zikulaformiculamodule_config_storeSubmissionData').prop('checked')) {
            $('#formiculaDataStorageDetails').addClass('d-none');
        }
        $('#zikulaformiculamodule_config_storeSubmissionData').change(function(){
            $('#formiculaDataStorageDetails').toggleClass('d-none', !$('#zikulaformiculamodule_config_storeSubmissionData').prop('checked'));
        });
    });
})(jQuery)
