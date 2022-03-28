<?php

namespace Elnooronline\LaravelTranslationLoaderGui;

use Illuminate\Http\Request;
use Spatie\TranslationLoader\LanguageLine;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Controller extends BaseController
{
    /**
     * @var \Elnooronline\LaravelTranslationLoaderGui\TranslationImporter
     */
    private $importer;

    /**
     * TranslationManagerController constructor.
     *
     * @param \Elnooronline\LaravelTranslationLoaderGui\TranslationImporter $importer
     */
    public function __construct(TranslationImporter $importer)
    {
        if (! config('translation-loader-gui.enabled')) {
            throw new NotFoundHttpException();
        }

        $this->importer = $importer;
    }

    /**
     * Get the view for specific group lines with select of all groups.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $groups = LanguageLine::select('group')->groupBy('group')->pluck('group');

        if ($request->group OR $groups->count()>0) {
            $group = $request->group;
            $lines = LanguageLine::where('group', $request->group)->get();
        }

        return view('translation-loader-gui::index', compact('lines', 'group', 'groups'));
    }

    /**
     * Update an existing language line.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Spatie\TranslationLoader\LanguageLine $languageLine
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(Request $request, LanguageLine $languageLine)
    {
        $lang = $request->lang;
        $line = $languageLine;
        $line->setTranslation($lang, $request->trans)->save();

        return view('translation-loader-gui::partials.line', compact('line', 'lang'));
    }

    /**
     * Load the translation files into the database.
     */
    public function load()
    {
        $this->importer->import();

        return redirect()->route('_translation-loader.index');
    }
}
