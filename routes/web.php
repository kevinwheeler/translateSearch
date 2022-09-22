<?php
use function Amp\ParallelFunctions\parallelMap;
use Amp\Promise;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Illuminate\Support\Facades\Route;

#TODO alphabetical order and move to controller
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;





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
  $targetLanguages = ['en', 'fr', 'cs', 'de', 'it', 'ru', 'pl', 'ko', 'ja', 'nl', 'da', 'hr', 'uk', 'sv', 'es', 'no', 'ga', 'is', 'hu', 'he', 'el', 'fi', 'bg', 'ar'];

  $currentPage = Paginator::resolveCurrentPage() ?: 1;
  $itemsPerPage = 5;
  $offset = ($currentPage * $itemsPerPage) - $itemsPerPage;

  $languagesToShow = array_slice($targetLanguages, $offset , $itemsPerPage);
  // $targetLanguages = ['en', 'fr', 'cs', 'de', 'it', 'ru'];
  // $targetLanguages = ['de'];
  $curlHandles = [];
  foreach($languagesToShow as $language) {
  //curl example from https://stackoverflow.com/a/2138534/3470632
  $ch = curl_init();
  $curlHandles[] = $ch;

  #TODO change this to not use env()
  curl_setopt($ch, CURLOPT_URL, 'https://translation.googleapis.com/language/translate/v2');
  curl_setopt($ch, CURLOPT_POST, true);

 

  $payload = json_encode( array(
    "q" => $translationInputQuery,
    "target" => $language,
    "format" => "text"
  ));


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
  $paginator = new LengthAwarePaginator($translations ,count($targetLanguages) ,$itemsPerPage);
  $paginator->path('/results');
  // dd(compact('paginator'));

  return view('results', [
      // 'translations' => compact('paginator')['translations']
      'translations' => $paginator
  ]);
});