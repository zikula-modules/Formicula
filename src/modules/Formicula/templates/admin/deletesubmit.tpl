{include file='admin/header.tpl'}

<h2>{gt text="Delete Form submit"}</h2>
<p class="z-warningmsg">{gt text="Do you really want to delete this Form submit?"}</p>
<form class="z-form" action="{modurl modname=Formicula type=admin func=deletesubmit}" method="post">
    <fieldset>
        <legend>{gt text='Confirmation prompt'}</legend>
        <input type="hidden" name="sid" value="{$submit.sid}" />
        <input type="hidden" name="authid" value="{insert name="generateauthkey" module="Formicula"}" />
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
        {button class="z-btgreen" src='button_ok.gif' name='confirmation' value='confirmation' set='icons/extrasmall' __alt='Delete' __title='Delete' __text='Delete'}
        <a class="z-btred" href="{modurl modname='Formicula' type='Admin' func='viewsubmits'}">{img modname='core' src='button_cancel.gif' set='icons/extrasmall' __alt='Cancel'  __title='Cancel'} {gt text="Cancel"}</a>
    </div>
</form>

{include file='admin/footer.tpl'}
