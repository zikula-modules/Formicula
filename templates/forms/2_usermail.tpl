<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>{gt text='Contact our team'}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <base href="{$baseurl}" />
    </head>
    <body>
        <p>
            {gt text='Hello,'}<br />
            <br />
            {gt text='Thank you for the comments posted from our Website. The sent data is:'}<br />
            <br />
            {gt text='Contact or Theme'} : {$contact.name}<br />
            {gt text='Your Name'} : {$userdata.uname}<br />
            {gt text='Email'} : {$userdata.uemail}<br />
            {if $modvars.Formicula.show_url==1}
            {gt text='Homepage'} : {$userdata.url}<br />
            {/if}
            {if $modvars.Formicula.show_company==1}
            {gt text='Company'} : {$userdata.company}<br />
            {/if}
            {if $modvars.Formicula.show_phone==1}
            {gt text='Phone Number'} : {$userdata.phone}<br />
            {/if}
            {if $modvars.Formicula.show_location==1}
            {gt text='Location'} : {$userdata.location}<br />
            {/if}
            <br />
            {gt text='Comment'} : {$userdata.comment|safehtml|nl2br}<br />
            <br />
            {gt text='We will respond to your email as soon as possible.'}<br />
            <br />
            {gt text="The %s Team" tag1=$sitename comment="%s will be replaced with the sitename"}<br />
        <br /></p>
    </body>
</html>