(function($) {
    $(document).ready(function(){
        if (!$('#spamcheck').prop('checked')) {
            $('#formicula_spamcheck_details').addClass('hidden');
        }
        $('#spamcheck').change(function() {
            $('#formicula_spamcheck_details').toggleClass('hidden', !$('#spamcheck').prop('checked'));
        });

        if (!$('#store_data').prop('checked')) {
            $('#formicula_storedata_details').addClass('hidden');
        }
        $('#store_data').change(function(){
            $('#formicula_storedata_details').toggleClass('hidden', !$('#store_data').prop('checked'));
        });
    });
})(jQuery)
