{include file='forms/0_userheader.tpl'}

{pageaddvar name="javascript" value="jquery"}
{pageaddvar name='javascript' value='modules/Formicula/javascript/js-webshim/minified/extras/modernizr-custom.js'}
{pageaddvar name='javascript' value='modules/Formicula/javascript/js-webshim/minified/polyfiller.js'}

<p class="z-informationmsg">
    {gt text="Mandatory fields are indicated with a"} <span class="mandatory">*</span>
</p>

<form id="contactform2" class="z-form" action="{modurl modname=Formicula type=user func=send}" method="post"{if $modvars.Formicula.show_attachfile==1} enctype="multipart/form-data"{/if}>
    <fieldset>
        <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
        <input type="hidden" name="form" value="0" />
        <input type="hidden" name="formname" value="Simple contact form" />
        <input type="hidden" name="adminformat" value="{$modvars.Formicula.default_adminformat}" />
        {if $modvars.Formicula.show_userformat!=1}<input type="hidden" name="userformat" value="{$modvars.Formicula.default_userformat}" />{/if}
        <input type="hidden" name="dataformat" value="array" />

        <legend>{gt text='Contact our team'}</legend>
        <div class="z-formrow">
            <label for="cid">{gt text="Contact"}</label>
            <select class="formborder" id="cid" name="cid">
                {foreach item='contact' from=$contacts}
                {if $contact.public == "1"}
                <option value="{$contact.cid}">{$contact.name}</option>
                {/if}
                {/foreach}
            </select>
        </div>

        <div class="z-formrow">
            <label for="uname">{gt text="Your Name"}<span class="mandatory">*</span></label>
            <input autofocus required class="formborder" id="uname" name="userdata[uname]" size="40" maxlength="80" value="{$userdata.uname}" />
        </div>

        <div class="z-formrow">
            <label for="uemail">{gt text="Email"}<span class="mandatory">*</span></label>
            <input type="email" required placeholder="{gt text='Enter a valid email address'}" class="formborder" id="uemail" name="userdata[uemail]" size="40" maxlength="40" value="{$userdata.uemail}" />
        </div>

        {if $modvars.Formicula.show_url==1}
        <div class="z-formrow">
            <label for="url">{gt text="Homepage"}</label>
            <input type="url" placeholder="http://" class="formborder" id="url" name="userdata[url]" size="40" maxlength="40" value="{$userdata.url}" />
        </div>
        {/if}

        {if $modvars.Formicula.show_phone==1}
        <div class="z-formrow">
            <label for="phone">{gt text="Phone Number"}</label>
            <input type="text" class="formborder" id="phone" name="userdata[phone]" size="40" maxlength="40" value="{$userdata.phone}" />
        </div>
        {/if}

        {if $modvars.Formicula.show_company==1}
        <div class="z-formrow">
            <label for="company">{gt text="Company"}</label>
            <input type="text" class="formborder" id="company" name="userdata[company]" size="40" maxlength="40" value="{$userdata.company}" />
        </div>
        {/if}

        {if $modvars.Formicula.show_location==1}
        <div class="z-formrow">
            <label for="location">{gt text="Location"}</label>
            <input type="text" class="formborder" id="location" name="userdata[location]" size="40" maxlength="40" value="{$userdata.location}" />
        </div>
        {/if}

        <div class="z-formrow">
            <label for="comment">{gt text="Comment"}<span class="mandatory">*</span></label>
            <textarea required placeholder="{gt text='Your comments here...'}" class="formborder" rows="6" cols="45" id="comment" name="userdata[comment]">{$userdata.comment}</textarea>
        </div>

        {if $modvars.Formicula.show_userformat eq 1}
        <div class="z-formrow">
            <label for="userformat">{gt text="Email Format"}</label>
            <select class="formborder" id="userformat" name="userformat">
                <option value="html"{if $modvars.Formicula.default_userformat eq 'html'} selected="selected"{/if}>{gt text="HTML"}</option>
                <option value="plain"{if $modvars.Formicula.default_userformat eq 'plain'} selected="selected"{/if}>{gt text="Text"}</option>
            </select>
        </div>
        {/if}

        {if $modvars.Formicula.show_attachfile eq 1}
        <div class="z-formrow">
            <label for="fileupload">{gt text="Attach a file"}</label>
            <input type="hidden" name="custom[fileupload][name]" value="{gt text='Attached file'}" />
            <input type="hidden" name="custom[fileupload][mandatory]" value="0" />
            <input id="fileupload" type='file' class="formborder2" name='custom[fileupload][data]' size='20' maxlength='16700000' value=''>
        </div>
        {/if}

        {if $spamcheck eq 1}
        <div class="z-formrow">
            <label for="Formicula_captcha">{gt text='Please solve this calculation'}<span class="mandatory">*</span></label>
            <span>
                {simplecaptcha font='quikhand' size='14' bgcolor='ffffff' fgcolor='000000'}
                <input id="Formicula_captcha" name="captcha" type="text" size="5" maxlength="5" value="" />
                <span class="z-sub">{gt text='(to prevent spam)'}</span>
            </span>
        </div>
        {/if}

    </fieldset>

    {notifydisplayhooks eventname='formicula.ui_hooks.forms.form_edit' id=null assign='hooks'}
    {foreach from=$hooks key='provider_area' item='hook'}
    {if $hook}
    <fieldset>
        <div class="z-formnote">
            {$hook}
        </div>
    </fieldset>
    {/if}
    {/foreach}

    <div class="z-formbuttons z-buttons">
        {button src='button_ok.png' name='submit' value='submit' set='icons/extrasmall' __alt='Send' __title='Send' __text='Send'}
    </div>

	<script type="text/javascript">
		// call webshims polyfill to get html5 forms validation and other effects into non-html5 browsers
		jQuery.webshims.activeLang('{{lang}}');
		jQuery.webshims.polyfill('forms');
	</script>
</form>
