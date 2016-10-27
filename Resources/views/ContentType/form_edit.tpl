<div class="z-formrow">
    {formlabel for='form' __text='Form #'}
    {formtextinput group='data' id='form' maxLength='2' width='30px'}
</div>

<div class="z-formrow">
    {formlabel for='contact' __text='Show contact'}
    {formdropdownlist group='data' id='contact' items=$contacts}
</div>
