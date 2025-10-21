<?php

namespace App\Traits;

trait AutoTranslatableAttributes
{
    public function getAttribute($key)
    {
        if (in_array($key, $this->translatable ?? [])) {
            $locale = app()->getLocale();
            return $this->getTranslation($key, $locale);
        }

        return parent::getAttribute($key);
    }
}
