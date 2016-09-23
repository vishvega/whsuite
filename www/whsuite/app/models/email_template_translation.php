<?php

class EmailTemplateTranslation extends AppModel
{

    public function EmailTemplate()
    {
        return $this->belongsTo('EmailTemplate');
    }

}
