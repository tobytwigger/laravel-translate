<?php

namespace Twigger\Translate\Translate\Interceptors\Database;

use Twigger\Translate\Translate\Interceptors\DatabaseInterceptor;
use Illuminate\Database\Eloquent\Model;

class TranslationModel extends Model
{

    protected $table = 'translations';

    protected $fillable = [
        'original',
        'translation',
        'lang'
    ];

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        self::creating(function($model) {

            dd(DatabaseInterceptor::class . $model->original . $model->lang);
            dd(md5(DatabaseInterceptor::class . $model->original . $model->lang));
            $model->id = md5(DatabaseInterceptor::class . $model->original, $model->lang);
        });
    }

}
