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
            {gt text='Contact'} : {$contact.name}<br />
            {gt text='Your Name'} : {$userdata.uname}<br />
            {gt text='Email'} : {$userdata.uemail}<br />
            {if $modvars.Formicula.show_url==1 and $userdata.url}
            {gt text='Homepage'} : {$userdata.url}<br />
            {/if}
            {if $modvars.Formicula.show_company==1 and $userdata.company}
            {gt text='Company'} : {$userdata.company}<br />
            {/if}
            {if $modvars.Formicula.show_phone==1 and $userdata.phone}
            {gt text='Phone Number'} : {$userdata.phone}<br />
            {/if}
            {if $modvars.Formicula.show_location==1 and $userdata.location}
            {gt text='Location'} : {$userdata.location}<br />
            {/if}
            <br />
            {gt text='Comment'} :<br />{strip}
            {if $userformat eq 'html'}
            {$userdata.comment|safehtml}<br />
            {else}
            {$userdata.comment|safehtml|nl2br}<br />
            {/if}{/strip}
            {if $modvars.Formicula.show_attachfile==1 and $custom.fileupload.data}
                <br />
                {$custom.fileupload.name} : {$custom.fileupload.data.name}<br />
            {/if}
            <br />
            {gt text='We will respond to your email address as soon as possible.'}<br />
            <br />
            {gt text="The %s Team" tag1=$sitename comment="%s will be replaced with the sitename"}<br />
            <br />
        </p>
    </body>
</html>