{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="list" size="small"}
    <h3>{gt text="View submitted form data"}</h3>
</div>

<table class="z-admintable z-clearer">
    <thead>
        <tr>
            <th>{gt text='Submit ID'}</th>
            <th>{gt text='Form #'}</th>
            <th>{gt text='Contact ID'}</th>
            <th>{gt text='On'}</th>
            <th>{gt text='By'}</th>
            <th class="z-right">{gt text='Options'}</th>
        </tr>
    </thead>
    <tbody>
        {foreach item=submit from=$formsubmits}
        <tr class="{cycle values="z-odd,z-even" name=submits}">
            <td>{$submit.sid}</td>
            <td>{$submit.form}</td>
            <td>{$submit.cid}</td>
            <td>{$submit.cr_date|dateformat}</td>
            <td>{$submit.cr_uid|profilelinkbyuid} ({$submit.cr_uid})</td>
            <td class="z-right">
                <a href="{modurl modname=Formicula type=admin func=displaysubmit sid=$submit.sid}" title="{gt text="View form submit"}">{img src="14_layer_visible.png" modname="core" set="icons/extrasmall" __alt="View form submit" }</a>
                <a href="{modurl modname=Formicula type=admin func=deletesubmit sid=$submit.sid}" title="{gt text="Delete form submit"}">{img src="14_layer_deletelayer.png" modname="core" set="icons/extrasmall" __alt="Delete form submit" }</a>
            </td>
        </tr>
        {foreachelse}
        <tr class="z-admintableempty"><td colspan="6">{gt text="No items found."}</td></tr>
        {/foreach}
    </tbody>
</table>

{adminfooter}
