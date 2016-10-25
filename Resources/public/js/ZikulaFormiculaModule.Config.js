(function($) {
    $(document).ready(function(){
        if (!$('#zikulaformiculamodule_config_enablespamcheck').prop('checked')) {
            $('#formiculaSpamCheckDetails').addClass('hidden');
        }
        $('#zikulaformiculamodule_config_enablespamcheck').change(function() {
            $('#formiculaSpamCheckDetails').toggleClass('hidden', !$('#zikulaformiculamodule_config_enablespamcheck').prop('checked'));
        });

        if (!$('#zikulaformiculamodule_config_storesubmitdata').prop('checked')) {
            $('#formiculaDataStorageDetails').addClass('hidden');
        }
        $('#zikulaformiculamodule_config_storesubmitdata').change(function(){
            $('#formiculaDataStorageDetails').toggleClass('hidden', !$('#zikulaformiculamodule_config_storesubmitdata').prop('checked'));
        });
    });
})(jQuery)
