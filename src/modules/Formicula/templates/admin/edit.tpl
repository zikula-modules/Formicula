{include file='admin/header.tpl'}
{if $mode=='create'}
{gt text="Add contact" assign=fortitle"}
{else}
{gt text="Edit contact" assign=fortitle"}
{/if}

<h2>{$fortitle}</h2>
{form cssClass="z-form"}
{formvalidationsummary}

<fieldset>
    <legend>{$fortitle}</legend>
    <div class="z-formrow">
        {formlabel for="cname" __text='Contact name'}
        {formtextinput size="40" maxLength="100" id="cname" text=$contact.name}
    </div>
    <div class="z-formrow">
        {formlabel for="email" __text='E-Mail'}
        {formtextinput size="40" maxLength="200" id="email" text=$contact.email}
    </div>
    <div class="z-formrow">
        {formlabel for="public" __text='Public'}
        {formcheckbox id="public" checked=$contact.public}
    </div>
    <div class="z-formnote z-warningmsg">{gt text="Use this information in the users confirmation mail"}</div>
    <div class="z-formrow">
        {formlabel for="semail" __text='Sender E-Mail'}
        {formtextinput size="40" maxLength="100" id="semail" text=$contact.semail}
    </div>
    <div class="z-formrow">
        {formlabel for="sname" __text='Sender name'}
        {formtextinput size="40" maxLength="480" id="sname" text=$contact.sname}
    </div>
    <div class="z-formrow">
        {formlabel for="ssubject" __text='Subject'}
        {formtextinput size="40" maxLength="100" id="ssubject" text=$contact.ssubject}
    </div>
    <div class="z-formnote z-informationmsg">{gt text="with <ul>    <li>%s = sitename</li>    <li>%l = slogan</li>    <li>%u = site url</li>    <li>%c = contacts sender name</li>    <li>%n&lt;num&gt; = user defined field name &lt;num&gt;</li>    <li>%d&lt;num&gt; = user defined field data &lt;num&gt;</li></ul>"}</div>
</fieldset>

<div class="z-formbuttons z-buttons">
    {formbutton id="submit" commandName="submit" __text="Submit"}
</div>

{/form}

{include file='admin/footer.tpl'}