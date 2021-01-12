<?php

namespace Twigger\Translate\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Twigger\Translate\Translate\Interceptors\Database\TranslationModel;

class DatabaseTranslationController extends Controller
{

    public function index()
    {
        return TranslationModel::paginate(10);
    }

    public function store()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }

}
