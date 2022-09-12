<?php
use function Amp\ParallelFunctions\parallelMap;
use Amp\Promise;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Illuminate\Support\Facades\Route;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/results', function () {
    $yo = ["sup"];
    $yo = [Request('query')];

$translations = Promise\wait(parallelMap(['en', 'fr', 'cs'], function ($languageCode) use ($yo) {
    // \sleep($time); // a blocking function call, might also do blocking I/O here
    $translationClient = new TranslationServiceClient();
    $content = $yo;
    $targetLanguage = $languageCode;
    $response = $translationClient->translateText(
        $content,
        $targetLanguage,
        #TODO change this to not use env()
        TranslationServiceClient::locationName(env("GOOGLE_CLOUD_PROJECT"), 'global')
    );
    
    $translationOutput = "";
    foreach ($response->getTranslations() as $key => $translation) {
        $separator = $key === 2
            ? '!'
            : ', ';
        $translationOutput .= $translation->getTranslatedText() . $separator;
    }

    return $translationOutput;
}));
    
    return view('results', [
        'translations' => $translations
    ]);
});
