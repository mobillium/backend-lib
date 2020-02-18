<?php
namespace App\Traits;

use Astrotomic\Translatable\Translatable as BaseTranslatable;
use Illuminate\Database\Eloquent\Builder;

trait Translatable
{
    use BaseTranslatable;
    protected function getLocales()
    {
        return Language::locales();
    }
    protected function saveTranslations()
    {
        $saved = true;
        foreach ($this->translations as $translation) {
            if ($saved && $this->isTranslationDirty($translation)) {
                if (! empty($connectionName = $this->getConnectionName())) {
                    $translation->setConnection($connectionName);
                }
                $translation->setAttribute($this->getRelationKey(), $this->getKey());
                try {
                    $saved = $translation->save();
                } catch (\Exception $e) {
                    $translation->delete();
                }
            }
        }
        return $saved;
    }
    public function scopeTranslatedInOrFallback(Builder $query, $locale = null)
    {
        $locale = $locale ?: $this->locale();
        $fallback = $this->getFallbackLocale($locale);
        $locales = [$locale, $fallback];
        return $query->whereHas('translations', function (Builder $q) use ($locales, $locale) {
            $q->whereIn($this->getLocaleKey(), $locales);
        });
    }
}