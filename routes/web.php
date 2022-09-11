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

Route::get('/results/{query}', function ($query) {
    // try {
    //     $formattedParent = $translationServiceClient->locationName(env("GOOGLE_CLOUD_PROJECT"), 'global');
    //     $sourceLanguageCode = '';
    //     $targetLanguageCodes = [en, fr, cs];
    //     $inputConfigs = [];
    //     $outputConfig = new OutputConfig();
    //     $operationResponse = $translationServiceClient->batchTranslateText($formattedParent, $sourceLanguageCode, $targetLanguageCodes, $inputConfigs, $outputConfig);
    //     $operationResponse->pollUntilComplete();
    //     if ($operationResponse->operationSucceeded()) {
    //         $result = $operationResponse->getResult();
    //         // doSomethingWith($result)
    //     } else {
    //         $error = $operationResponse->getError();
    //         // handleError($error)
    //     }
    // } finally {
    //     $translationServiceClient->close();
    // }
    

$values = Promise\wait(parallelMap(['en', 'fr', 'cs'], function ($languageCode) {
    // \sleep($time); // a blocking function call, might also do blocking I/O here
    $translationClient = new TranslationServiceClient();
    $content = ['one two four'];
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
    

    
    // $translationServiceClient = new TranslationServiceClient();
    // try {
    //     $contents = [];
    //     $targetLanguageCode = '';
    //     $formattedParent = $translationServiceClient->locationName('[PROJECT]', '[LOCATION]');
    //     $response = $translationServiceClient->translateText($contents, $targetLanguageCode, $formattedParent);
    // } finally {
    //     $translationServiceClient->close();
    // }


    return view('results', [
        'query' => implode(" ", $values)
    ]);
});
