<?php
// We are only going to translate the queries we need right now, so if we are showing
// 5 translations at a time, we will only call out to Google's API to do 5 translations right now.
// And then if the user clicks to the next page to see the next 5 translations, we will
// Call back out to Google's translation API to get 5 more translations.

namespace App\Http\Controllers;

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;

class ResultsController extends Controller
{

    private function removeParam($url, $param) {
      $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*$/', '', $url);
      $url = preg_replace('/(&|\?)'.preg_quote($param).'=[^&]*&/', '$1', $url);
      return $url;
    }


    public function index() {
      // $validated = Request::validate([
      //   'query' => 'required|max:50',
      // ]);

      if (!Request::has("g-recaptcha-response") || strlen(Request("g-recaptcha-response")) == 0) {
        dd("wat1");
        return response("User Error: submit recaptcha token", 400);
      }


      $client = new \GuzzleHttp\Client();

      // dd(Request("g-recaptcha-response"));

       // Create a POST request
       $response = $client->request(
           'POST',
           'https://www.google.com/recaptcha/api/siteverify',
           [
              //  'form_params' => [
              //      'key1' => 'value1',
              //      'key2' => 'value2'
              //  ]
              'form_params' => [
                  'secret' => env("RECAPTCHA_SECRET_KEY"),//TODO don't use env()
                  'response' => Request("g-recaptcha-response"),
                  'remoteip' => Request::ip()
               ]
           ]
       );

       
       // Parse the response object, e.g. read the headers, body, etc.
       $headers = $response->getHeaders();
      //  $body = $response->getBody();
       $g_response = json_decode($response->getBody());
       if (!$g_response->success) {
        return response("User Error: submit recaptcha token", 400);
       }




      if (!Request::has("query") || strlen(Request("query")) > 50 || strlen(Request("query")) < 1) {
        return response("User Error: Search query required and can't be more than 50 characters", 400);
      }

      $possibleLanguageKeyValPairs = json_decode('{"Afrikaans": "af", "Albanian": "sq", "Amharic": "am", "Arabic": "ar", "Armenian": "hy", "Azerbaijani": "az", "Basque": "eu", "Belarusian": "be", "Bengali": "bn", "Bosnian": "bs", "Bulgarian": "bg", "Catalan": "ca", "Cebuano": "ceb", "Chinese (Simplified)": "zh-CN", "Chinese (Traditional)": "zh-TW", "Corsican": "co", "Croatian": "hr", "Czech": "cs", "Danish": "da", "Dutch": "nl", "English": "en", "Esperanto": "eo", "Estonian": "et", "Filipino (Tagalog)": "fil", "Finnish": "fi", "French": "fr", "Frisian": "fy", "Galician": "gl", "Georgian": "ka", "German": "de", "Greek": "el", "Gujarati": "gu", "Haitian Creole": "ht", "Hausa": "ha", "Hawaiian": "haw", "Hebrew": "he or iw", "Hindi": "hi", "Hmong": "hmn", "Hungarian": "hu", "Icelandic": "is", "Igbo": "ig", "Indonesian": "id", "Irish": "ga", "Italian": "it", "Japanese": "ja", "Javanese": "jv", "Kannada": "kn", "Kazakh": "kk", "Khmer": "km", "Kinyarwanda": "rw", "Korean": "ko", "Kurdish": "ku", "Kyrgyz": "ky", "Lao": "lo", "Latin": "la", "Latvian": "lv", "Lithuanian": "lt", "Luxembourgish": "lb", "Macedonian": "mk", "Malagasy": "mg", "Malay": "ms", "Malayalam": "ml", "Maltese": "mt", "Maori": "mi", "Marathi": "mr", "Mongolian": "mn", "Myanmar (Burmese)": "my", "Nepali": "ne", "Norwegian": "no", "Nyanja (Chichewa)": "ny", "Odia (Oriya)": "or", "Pashto": "ps", "Persian": "fa", "Polish": "pl", "Portuguese (Portugal, Brazil)": "pt", "Punjabi": "pa", "Romanian": "ro", "Russian": "ru", "Samoan": "sm", "Scots Gaelic": "gd", "Serbian": "sr", "Sesotho": "st", "Shona": "sn", "Sindhi": "sd", "Sinhala (Sinhalese)": "si", "Slovak": "sk", "Slovenian": "sl", "Somali": "so", "Spanish": "es", "Sundanese": "su", "Swahili": "sw", "Swedish": "sv", "Tagalog (Filipino)": "tl", "Tajik": "tg", "Tamil": "ta", "Tatar": "tt", "Telugu": "te", "Thai": "th", "Turkish": "tr", "Turkmen": "tk", "Ukrainian": "uk", "Urdu": "ur", "Uyghur": "ug", "Uzbek": "uz", "Vietnamese": "vi", "Welsh": "cy", "Xhosa": "xh", "Yiddish": "yi", "Yoruba": "yo", "Zulu": "zu"}', true);
  
      //filter out all query params that aren't valid language selection key:value pairs
      $validLanguageQueryParamsFilter = function($queryParamValue, $queryParamKey) use($possibleLanguageKeyValPairs) {
        if (preg_match('/^language(\d+)$/', $queryParamKey)) {
          if (in_array($queryParamValue, $possibleLanguageKeyValPairs, true)) {
            return true;
          }
        }

        return false;
      };

      // Should look something like this: ["language1"=>"en", "language2"=>"fr"]
      $targetLanguageKeyValPairs = array_filter(Request::all(), $validLanguageQueryParamsFilter, ARRAY_FILTER_USE_BOTH);

      // $index = 1;
      // foreach($targetLanguageKeyValPairs as $key => $val){
      //   if ($key != ('language' . $index)){
      //     // We want paramaters in order just in case it's helpful to not have duplicate pages for SEO.
      //     // We'll probably block these search results pages from getting indexed anyways though, maybe?
      //     return response("User Error: Please supply language parameters in order. IE language1=x&langauge2=y", 400);
      //   }
      // }

      //All language codes for the languages the user selected
      $targetLanguageCodes = array_values($targetLanguageKeyValPairs);

      if (count($targetLanguageCodes) === 0) {
        return response("User Error: Please supply output languages to translate to.", 400);
      }

      // We shouln't need this check, since it shouldn't matter how many languages the user selects
      // because we are using pagination anyways and only returning the results for 5 
      // languages at a time. But I included this check anyways just in case we move away from
      // pagination in the future and forget to limit the number of languages a user can select.
      if (count($targetLanguageCodes) > 200) {
        return response("User Error: Please supply less than 200 output languages", 400);
      }


      $filter2 = function($languageCode, $language) use ($targetLanguageCodes) {
          if (in_array($languageCode, $targetLanguageCodes, true)) {
            return true;
          }
          else return false;
      };

      //All language names for the languages the user selected
      $targetLanguageNames = array_filter($possibleLanguageKeyValPairs, $filter2, ARRAY_FILTER_USE_BOTH);
      $targetLanguageNames = array_keys($targetLanguageNames);

      $translationInputQuery = [Request('query')];
      // $targetLanguageCodes = ['en', 'fr', 'cs', 'de', 'it', 'ru', 'pl', 'ko', 'ja', 'nl', 'da', 'hr', 'uk', 'sv', 'es', 'no', 'ga', 'is', 'hu', 'he', 'el', 'fi', 'bg', 'ar'];

      #pagination example code used from here https://www.youtube.com/watch?v=sAJGyDPXESo
      $currentPage = Paginator::resolveCurrentPage() ?: 1;
      $itemsPerPage = 5;
      $offset = ($currentPage * $itemsPerPage) - $itemsPerPage;

      //language codes of languages to show on this page
      $currentPageLanguageCodes = array_slice($targetLanguageCodes, $offset , $itemsPerPage);
      $currentPageLanguageNames = array_slice($targetLanguageNames, $offset , $itemsPerPage);
      $curlHandles = [];
  

      // $client = new Client([
      //   'base_uri' => 'https://translation.googleapis.com/language/translate/v2',
      //   'timeout'  => 10.0,
      // ]);

      // $promises = [];
      // foreach($currentPageLanguageCodes as $language) {
      //   $promise = $client->postAsync('https://translation.googleapis.com/language/translate/v2', [
      //       #TODO change this to not use env()
      //       'headers' => [
      //               "X-goog-api-key" => env("GOOGLE_TRANSLATE_API_KEY"),
      //       ],
      //       'json' => [
      //               "q" => $translationInputQuery,
      //               "target" => $language,
      //               "format" => "text"
      //       ],
      //   ])->then(
      //       function (ResponseInterface $res){
      //           $response = json_decode($res->getBody()->getContents());
      //           return $response;
      //       },
      //       function (RequestException $e) {
      //           throw($e);
      //           // $response = [];
      //           // $response['data'] = $e->getMessage();
        
      //           // return $response;
      //       }
      //   );
      //   $promises[] = $promise;
      // }

      // $responses = Promise\Utils::unwrap($promises);

      // dd($responses);
      //// echo json_encode($response);

      foreach($currentPageLanguageCodes as $language) {
      //curl example from https://stackoverflow.com/a/2138534/3470632
      $ch = curl_init();
      if (!$ch) {
        throw new \RuntimeException("curl_init() failed");
      }
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

      // $res = array();
      // foreach ($curlHandles as $ch){
      //   $curlErrorCode = curl_errno($ch);
      //   if ($curlErrorCode === 0) {
      //     $info = curl_getinfo($ch);
      //     $info['url'] = trim($info['url']);
      //     if ($info['http_code'] == 200) {
      //         $content = curl_multi_getcontent($ch);
      //         $res[] = sprintf("#HTTP-OK %0.2f kb returned", strlen($content) / 1024);
      //     } else {
      //         // $res[] = "#HTTP-ERROR {$info['http_code'] }  for : {$info['url']}";
      //         $res[] = curl_multi_getcontent($ch);
      //     }
      //   } else {
      //       $res[] = sprintf("#CURL-ERROR %d: %s ", $curlErrorCode, curl_error($ch));
      //   }
      // }
      // dd($res);

      foreach($curlHandles as $ch) {
        curl_multi_remove_handle($mh, $ch);
      }

      curl_multi_close($mh);

      $translations = [];

      foreach($curlHandles as $ch) {
        //TODO add error handling here
        $translations[] = json_decode(curl_multi_getcontent($ch), true)["data"]["translations"][0]["translatedText"];
      }

      $languagesAndTranslations = array_combine($currentPageLanguageNames, $translations);

      //TODO do I need to close the individual curl handles when using curl multi?
      // $server_output = curl_exec($ch);
      // curl_close ($ch);
      $paginator = new LengthAwarePaginator($languagesAndTranslations,count($targetLanguageCodes) ,$itemsPerPage);
      $paginator->withPath('/results');
      // dd(compact('paginator'));

      $queryString = str_replace(Request::url(), '', Request::fullUrl());
      $URL = $this->removeParam($queryString, 'page');
      $URL = $this->removeParam($queryString, 'query');
      $URL = '/' . "#" . substr($URL,1);

      return view('results', [
          // 'translations' => compact('paginator')['translations']
          'languagesAndTranslations' => $paginator,
          'homeUrl' => $URL
      ]);

    }
}
