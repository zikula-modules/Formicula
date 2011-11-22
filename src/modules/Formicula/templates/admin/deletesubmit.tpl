{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="delete" size="small"}
    <h3>{gt text="Delete form submit"}</h3>
</div>
<p class="z-warningmsg">{gt text="Do you really want to delete this form submit?"}</p>
<form class="z-form" action="{modurl modname=Formicula type=admin func=deletesubmit}" method="post">
    <fieldset>
        <legend>{gt text='Confirmation prompt'}</legend>
        <input type="hidden" name="sid" value="{$submit.sid}" />
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <div class="z-formrow">
            <span class="z-label">{gt text="Submit Form #"}</span>
            <span>{$submit.form|safetext}</span>
        </div>
        <div class="z-formrow">
            <span class="z-label">{gt text="Contact or Theme"}</span>
            <span>{$submit.cid|safetext}</span>
        </div>
        <div class="z-formrow">
            <span class="z-label">{gt text="Submitted on"}</span>
            <span>{$submit.cr_date|dateformat}</span>
        </div>
        <div class="z-formrow">
            <span class="z-label">{gt text="Submitted by"}</span>
            <span>{$submit.cr_uid|profilelinkbyuid} ({$submit.cr_uid})</span>
        </div>
    </fieldset>

    <div class="z-formbuttons z-buttons">
        {button class="z-btgreen" src='button_ok.png' name='confirmation' value='confirmation' set='icons/extrasmall' __alt='Delete' __title='Delete' __text='Delete'}
        <a class="z-btred" href="{modurl modname='Formicula' type='admin' func='viewsubmits'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel'  __title='Cancel'} {gt text="Cancel"}</a>
    </div>
</form>

{adminfooter}