<div class="z-formrow">
    {formlabel for='form' __text="Form #"}
    {formtextinput id='form' maxLength='2' width="30px" group='data'}
</div>

<div class="z-formrow">
    {formlabel for='contact' __text="Show contact"}
    {formdropdownlist id="contact" items=$contacts group="data"}
</div>
