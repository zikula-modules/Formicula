// hide or show fields 
jQuery(document).ready(function(){
    if (!jQuery('#spamcheck').prop('checked')) {
        jQuery('#formicula_spamcheck_details').hide();
    }
    jQuery('#spamcheck').change(function(){
        if (jQuery('#spamcheck').prop('checked')) {
            jQuery('#formicula_spamcheck_details').show("slow");
        } else {
            jQuery('#formicula_spamcheck_details').hide("slow");
        }
    });
    if (!jQuery('#store_data').prop('checked')) {
        jQuery('#formicula_storedata_details').hide();
    }
    jQuery('#store_data').change(function(){
        if (jQuery('#store_data').prop('checked')) {
            jQuery('#formicula_storedata_details').show("slow");
        } else {
            jQuery('#formicula_storedata_details').hide("slow");
        }
    });
}); 