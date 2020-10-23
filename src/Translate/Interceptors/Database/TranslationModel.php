<?php

namespace Twigger\Translate\Translate\Interceptors\Database;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Twigger\Translate\Translate\Interceptors\CacheInterceptor;
use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;
use Illuminate\Database\Eloquent\Model;

/**
 * Holds translations in the database
 *
 * @method static Builder from(string $from) Limit translations to those originally in this language
 * @method static Builder to(string $to) Limit translations to those translated to this language
 * @method static Builder translate(string $translate) Limit translations to those translating this text
 */
class TranslationModel extends Model
{

    /**
     * The table name. This is also set by default on construct of a model
     *
     * @var string
     */
    protected $table = 'translations';

    /**
     * Mark the primary key as non-incrementing
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Set the primary key to type string
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Define fillable attributes
     *
     * @var string[]
     */
    protected $fillable = [
        'text_original',
        'text_translated',
        'lang_from',
        'lang_to'
    ];

    /**
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->table = config('laravel-translate.table', 'translations');

        parent::__construct($attributes);
        self::creating(function($model) {
            $model->id = static::getUniqueKey($model->text_original, $model->lang_to, $model->lang_from);
        });
    }

    protected static function booted()
    {
        static::creating(function($model) {
            $model->id = static::getUniqueKey($model->text_original, $model->lang_to, $model->lang_from);
        });
        static::saved(function($model) {
            Cache::forget(CacheInterceptor::getCacheKey($model->text_original, $model->lang_to, $model->lang_from));
        });
    }

    /**
     * Get the unique key representing the row
     *
     * @param string $line Line of text to translate
     * @param string $to Language to translate to
     * @param string $from Language to translate from
     */
    public static function getUniqueKey(string $line, string $to, string $from)
    {
        return $from . '_' . $to . '_' .  md5(DatabaseInterceptor::class . $line);
    }

    /**
     * Impose a condition on the original language
     *
     * Only return translations from this language
     *
     * @param Builder $query
     * @param string $from ISO code for language
     *
     * @return Builder
     */
    public function scopeFrom(Builder $query, string $from)
    {
        return $query->where('lang_from', $from);
    }

    /**
     * Impose a condition on the translated language
     *
     * Only return translations to this language
     *
     * @param Builder $query
     * @param string $to ISO code for language
     *
     * @return Builder
     */
    public function scopeTo(Builder $query, string $to)
    {
        return $query->where('lang_to', $to);
    }

    /**
     * Impose a condition on the original text
     *
     * Only return translations of this text
     *
     * @param Builder $query
     * @param string $original The original text to translate
     *
     * @return Builder
     */
    public function scopeTranslate(Builder $query, string $original)
    {
        return $query->where('text_original', $original);
    }
}
