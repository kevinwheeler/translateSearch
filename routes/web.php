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
  
  $authToken = rtrim(shell_exec("gcloud auth application-default print-access-token"));


  $translationInputQuery = [Request('query')];
  // $targetLanguages = ['en', 'fr', 'cs', 'de', 'it', 'ru', 'pl', 'ko', 'ja', 'nl', 'da', 'hr', 'uk', 'sv', 'es', 'no', 'ga', 'is', 'hu', 'he', 'el', 'fi', 'bg', 'ar'];
  $targetLanguages = ['en', 'fr', 'cs', 'de', 'it', 'ru'];
  // $targetLanguages = ['de'];
  $curlHandles = [];
  foreach($targetLanguages as $language) {
  //curl example from https://stackoverflow.com/a/2138534/3470632
  $ch = curl_init();
  $curlHandles[] = $ch;

  #TODO change this to not use env()
  curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2');
  curl_setopt($ch, CURLOPT_POST, true);

 
  // {
  //   "q": "hi",
  //   "target": "de",
  //   "format": "text"
  //  }

  $payload = json_encode( array(
    "q" => $translationInputQuery,
    "target" => $language,
    "format" => "text"
  ));

//   $payload = json_encode(<<<EOD
//   {
//     "contents": [
//       string
//     ],
//     "mimeType": string,
//     "sourceLanguageCode": string,
//     "targetLanguageCode": string,
//     "model": string,
//     "glossaryConfig": {
//       object (TranslateTextGlossaryConfig)
//     },
//     "labels": {
//       string: string,
//       ...
//     }
//   }  
//   EOD
//   );

  curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
  // curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
  #TODO change this to not use env()
  curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json; charset=utf-8', "X-goog-api-key: " . env("GOOGLE_TRANSLATE_API_KEY")));
  
  // Receive server response ...
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  }

  $mh = curl_multi_init();
  foreach($curlHandles as $ch) {
    curl_multi_add_handle($mh, $ch);
  }

  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while ($running);

  foreach($curlHandles as $ch) {
    curl_multi_remove_handle($mh, $ch);
  }

  curl_multi_close($mh);

  $translations = [];

  foreach($curlHandles as $ch) {
    $translations[] = json_decode(curl_multi_getcontent($ch), true)["data"]["translations"][0]["translatedText"];

  }

  // $server_output = curl_exec($ch);
  // curl_close ($ch);

  return view('results', [
      'translations' => $translations
  ]);
});

//  Route::get('/results', function () {
//      $yo = ["sup"];
//      $yo = [Request('query')];
//  try {
// //  $translations = Promise\wait(parallelMap(['en', 'fr', 'cs', 'de', 'it', 'ru'], function ($languageCode) use ($yo) {
//  $translations = Promise\wait(parallelMap(['en', 'fr', 'cs', 'de', 'it', 'ru', 'pl', 'ko', 'ja', 'nl', 'da', 'hr', 'uk', 'sv', 'es', 'no', 'ga', 'is', 'hu', 'he', 'el', 'fi', 'bg', 'ar'], function ($languageCode) use ($yo) {
//      // \sleep($time); // a blocking function call, might also do blocking I/O here
//      $translationClient = new TranslationServiceClient();
//      $content = $yo;
//      $targetLanguage = $languageCode;
//      $response = $translationClient->translateText(
//          $content,
//          $targetLanguage,
//          #TODO change this to not use env()
//          TranslationServiceClient::locationName(env("GOOGLE_CLOUD_PROJECT"), 'global')
//      );
  
//      $translationOutput = "";
//      foreach ($response->getTranslations() as $key => $translation) {
//          $separator = $key === 2
//              ? '!'
//              : ', ';
//          $translationOutput .= $translation->getTranslatedText() . $separator;
//      }

//      return $translationOutput;
//  }));
//  }  catch (Exception $exception) {
//      dd($exception->getReasons());
//      exit;
//  }
  
//      return view('results', [
//          'translations' => $translations
//      ]);
//  });
