<?php

namespace Twigger\Translate;

use Twigger\Translate\Locale\DetectorFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string lang() Get the requested language to translate into
 */
class Detect extends Facade
{

    public static function getFacadeAccessor()
    {
        return DetectorFactory::class;
    }

}
