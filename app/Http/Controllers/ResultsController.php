<?php
// We are only going to translate the queries we need right now, so if we are showing
// 5 translations at a time, we will only call out to Google's API to do 5 translations right now.
// And then if the user clicks to the next page to see the next 5 translations, we will
// Call back out to Google's translation API to get 5 more translations.

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ResultsController extends Controller
{
    public function index(){
  //TODO think more about validations. I need to make sure query and language parameters aren't empty
  // I also need to make sure query is limited in length to some number of characters. 
  if (strlen(Request('query')) > 50) {
    dd("TODO finish validating queries that are > 50 characters long.");
  }

  // $possibleLanguages = json_decode("{'Afrikaans': 'af', 'Albanian': 'sq', 'Amharic': 'am', 'Arabic': 'ar', 'Armenian': 'hy', 'Azerbaijani': 'az', 'Basque': 'eu', 'Belarusian': 'be', 'Bengali': 'bn', 'Bosnian': 'bs', 'Bulgarian': 'bg', 'Catalan': 'ca', 'Cebuano': 'ceb', 'Chinese (Simplified)': 'zh-CN', 'Chinese (Traditional)': 'zh-TW', 'Corsican': 'co', 'Croatian': 'hr', 'Czech': 'cs', 'Danish': 'da', 'Dutch': 'nl', 'English': 'en', 'Esperanto': 'eo', 'Estonian': 'et', 'Filipino (Tagalog)': 'fil', 'Finnish': 'fi', 'French': 'fr', 'Frisian': 'fy', 'Galician': 'gl', 'Georgian': 'ka', 'German': 'de', 'Greek': 'el', 'Gujarati': 'gu', 'Haitian Creole': 'ht', 'Hausa': 'ha', 'Hawaiian': 'haw', 'Hebrew': 'he\xa0or\xa0iw', 'Hindi': 'hi', 'Hmong': 'hmn', 'Hungarian': 'hu', 'Icelandic': 'is', 'Igbo': 'ig', 'Indonesian': 'id', 'Irish': 'ga', 'Italian': 'it', 'Japanese': 'ja', 'Javanese': 'jv', 'Kannada': 'kn', 'Kazakh': 'kk', 'Khmer': 'km', 'Kinyarwanda': 'rw', 'Korean': 'ko', 'Kurdish': 'ku', 'Kyrgyz': 'ky', 'Lao': 'lo', 'Latin': 'la', 'Latvian': 'lv', 'Lithuanian': 'lt', 'Luxembourgish': 'lb', 'Macedonian': 'mk', 'Malagasy': 'mg', 'Malay': 'ms', 'Malayalam': 'ml', 'Maltese': 'mt', 'Maori': 'mi', 'Marathi': 'mr', 'Mongolian': 'mn', 'Myanmar (Burmese)': 'my', 'Nepali': 'ne', 'Norwegian': 'no', 'Nyanja (Chichewa)': 'ny', 'Odia (Oriya)': 'or', 'Pashto': 'ps', 'Persian': 'fa', 'Polish': 'pl', 'Portuguese (Portugal, Brazil)': 'pt', 'Punjabi': 'pa', 'Romanian': 'ro', 'Russian': 'ru', 'Samoan': 'sm', 'Scots Gaelic': 'gd', 'Serbian': 'sr', 'Sesotho': 'st', 'Shona': 'sn', 'Sindhi': 'sd', 'Sinhala (Sinhalese)': 'si', 'Slovak': 'sk', 'Slovenian': 'sl', 'Somali': 'so', 'Spanish': 'es', 'Sundanese': 'su', 'Swahili': 'sw', 'Swedish': 'sv', 'Tagalog (Filipino)': 'tl', 'Tajik': 'tg', 'Tamil': 'ta', 'Tatar': 'tt', 'Telugu': 'te', 'Thai': 'th', 'Turkish': 'tr', 'Turkmen': 'tk', 'Ukrainian': 'uk', 'Urdu': 'ur', 'Uyghur': 'ug', 'Uzbek': 'uz', 'Vietnamese': 'vi', 'Welsh': 'cy', 'Xhosa': 'xh', 'Yiddish': 'yi', 'Yoruba': 'yo', 'Zulu': 'zu'}", true);
  $possibleLanguages = json_decode('{"Afrikaans": "af", "Albanian": "sq", "Amharic": "am", "Arabic": "ar", "Armenian": "hy", "Azerbaijani": "az", "Basque": "eu", "Belarusian": "be", "Bengali": "bn", "Bosnian": "bs", "Bulgarian": "bg", "Catalan": "ca", "Cebuano": "ceb", "Chinese (Simplified)": "zh-CN", "Chinese (Traditional)": "zh-TW", "Corsican": "co", "Croatian": "hr", "Czech": "cs", "Danish": "da", "Dutch": "nl", "English": "en", "Esperanto": "eo", "Estonian": "et", "Filipino (Tagalog)": "fil", "Finnish": "fi", "French": "fr", "Frisian": "fy", "Galician": "gl", "Georgian": "ka", "German": "de", "Greek": "el", "Gujarati": "gu", "Haitian Creole": "ht", "Hausa": "ha", "Hawaiian": "haw", "Hebrew": "he or iw", "Hindi": "hi", "Hmong": "hmn", "Hungarian": "hu", "Icelandic": "is", "Igbo": "ig", "Indonesian": "id", "Irish": "ga", "Italian": "it", "Japanese": "ja", "Javanese": "jv", "Kannada": "kn", "Kazakh": "kk", "Khmer": "km", "Kinyarwanda": "rw", "Korean": "ko", "Kurdish": "ku", "Kyrgyz": "ky", "Lao": "lo", "Latin": "la", "Latvian": "lv", "Lithuanian": "lt", "Luxembourgish": "lb", "Macedonian": "mk", "Malagasy": "mg", "Malay": "ms", "Malayalam": "ml", "Maltese": "mt", "Maori": "mi", "Marathi": "mr", "Mongolian": "mn", "Myanmar (Burmese)": "my", "Nepali": "ne", "Norwegian": "no", "Nyanja (Chichewa)": "ny", "Odia (Oriya)": "or", "Pashto": "ps", "Persian": "fa", "Polish": "pl", "Portuguese (Portugal, Brazil)": "pt", "Punjabi": "pa", "Romanian": "ro", "Russian": "ru", "Samoan": "sm", "Scots Gaelic": "gd", "Serbian": "sr", "Sesotho": "st", "Shona": "sn", "Sindhi": "sd", "Sinhala (Sinhalese)": "si", "Slovak": "sk", "Slovenian": "sl", "Somali": "so", "Spanish": "es", "Sundanese": "su", "Swahili": "sw", "Swedish": "sv", "Tagalog (Filipino)": "tl", "Tajik": "tg", "Tamil": "ta", "Tatar": "tt", "Telugu": "te", "Thai": "th", "Turkish": "tr", "Turkmen": "tk", "Ukrainian": "uk", "Urdu": "ur", "Uyghur": "ug", "Uzbek": "uz", "Vietnamese": "vi", "Welsh": "cy", "Xhosa": "xh", "Yiddish": "yi", "Yoruba": "yo", "Zulu": "zu"}', true);
  $filter = function($queryParamValue, $queryParamKey) use($possibleLanguages) {
    // if ($queryParamKey === "query") return true;

    if (preg_match('/^language(\d+)$/', $queryParamKey)) {
      if (in_array($queryParamValue, $possibleLanguages, true)) {
        return true;
      }
    }

    return false;
  };
  #TODO refactor this variable name to $targetLanguageCodes
  $targetLanguages = array_filter(Request::all(), $filter, ARRAY_FILTER_USE_BOTH);
  $targetLanguages = array_values($targetLanguages);

  $filter2 = function($languageCode, $language) use ($targetLanguages) {
      if (in_array($languageCode, $targetLanguages, true)) {
        return true;
      }
      else return false;
  };

  $targetLanguageNames = array_filter($possibleLanguages, $filter2, ARRAY_FILTER_USE_BOTH);
  $targetLanguageNames = array_keys($targetLanguageNames);

  $translationInputQuery = [Request('query')];
  // $targetLanguages = ['en', 'fr', 'cs', 'de', 'it', 'ru', 'pl', 'ko', 'ja', 'nl', 'da', 'hr', 'uk', 'sv', 'es', 'no', 'ga', 'is', 'hu', 'he', 'el', 'fi', 'bg', 'ar'];

  #pagination example code used from here https://www.youtube.com/watch?v=sAJGyDPXESo
  $currentPage = Paginator::resolveCurrentPage() ?: 1;
  $itemsPerPage = 5;
  $offset = ($currentPage * $itemsPerPage) - $itemsPerPage;

  //language codes of languages to show on this page
  $languagesToShow = array_slice($targetLanguages, $offset , $itemsPerPage);
  $languageNamesToShow = array_slice($targetLanguageNames, $offset , $itemsPerPage);
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
    //TODO add error handling here
    $translations[] = json_decode(curl_multi_getcontent($ch), true)["data"]["translations"][0]["translatedText"];
  }

  $languagesAndTranslations = array_combine($languageNamesToShow, $translations);

  //TODO do I need to close the individual curl handles when using curl multi?
  // $server_output = curl_exec($ch);
  // curl_close ($ch);
  $paginator = new LengthAwarePaginator($languagesAndTranslations,count($targetLanguages) ,$itemsPerPage);
  $paginator->withPath('/results');
  // dd(compact('paginator'));

  return view('results', [
      // 'translations' => compact('paginator')['translations']
      'languagesAndTranslations' => $paginator
  ]);

    }
}
