
if (!window.location.hash){
    window.location.hash = 'language1=ar&language2=bn&language3=zh-CN';
}
 
let hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
// hashVarPairStrings should now be an array that looks something like ["lang23=en", "lang55=fr", ...]
let keyAndValueStrings = hash.split('&');
let selectedLanguages = [];
for (let i = 0; i < keyAndValueStrings.length; i++) {
    selectedLanguages[i] = keyAndValueStrings[i].split('=');
}
// hashVars should now be a 2d array that looks something ike [["lang1","en"],["lang2","fr"], ...]
console.log("selectedLanguages = ", JSON.stringify(selectedLanguages));

function kmwSetForm(languages){
    for (const language of languages){
        console.log("in loop");
        let languageCode = language[1];
        document.querySelector(`input[value=${languageCode}]`).checked = true;
    }
}

kmwSetForm(selectedLanguages);