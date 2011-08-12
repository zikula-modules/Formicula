{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="display" size="small"}
    <h3>{gt text="View submitted form data"}</h3>
</div>
<p>
    {gt text='Hello,'}<br />
    <br />
    {gt text='A visitor of to your web site used the form for contact and sent the following:'} <br />
    <br />
    {gt text='Form #'} : {$submit.form}<br />
    {gt text='Contact or Theme'} : {$submit.name}<br />
    {gt text='Homepage'} : {$submit.url}<br />
    {gt text='Company'} : {$submit.company}<br />
    {gt text='Phone Number'} : {$submit.phone}<br />
    {gt text='Location'} : {$submit.location}<br />
    <hr>
    {foreach item=field key=k from=$submit.customdata}
    {$k} : {$field}<br />
    {/foreach}
    <hr>
    {gt text='Comment'} : {$submit.comment|safehtml|nl2br} <br />
    <br />
    {gt text='The user has the following IP address/hostname: '} {$submit.ip} / {$submit.host} <br />
    <br />
</p>
{adminfooter}