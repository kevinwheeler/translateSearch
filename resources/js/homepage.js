// On initial page load, setup default hash fragment if there isn't one.
if (!window.location.hash){
    window.location.hash = "language0=fr&language1=pl&language2=it&language3=sv&language4=es&language5=pt&language6=zh-CN&language7=zh-TW&language8=ru&language9=bn&language10=hi&language11=ar&language12=ur&language13=id&language14=ja"
}

let selectedLanguages = [];

function buildModelFromHash(){
    selectedLanguages = [];
    let hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character

    if (hash === "") return;

    // hashVarPairStrings should now be an array that looks something like ["lang23=en", "lang55=fr", ...]
    let keyAndValueStrings = hash.split('&');
    for (let i = 0; i < keyAndValueStrings.length; i++) {
        selectedLanguages[i] = keyAndValueStrings[i].split('=');
    }
    // selectedLanguages should now be a 2d array that looks something ike [["lang1","en"],["lang2","fr"], ...]
}

// On initial page load, build up our model / data structure of which languages are selected
// and in what order.
buildModelFromHash();


function kmwSetForm(languages) {
    for (const language of languages){
        let languageCode = language[1];
        document.querySelector(`input[value=${languageCode}]`).checked = true;
    }
}

window.addEventListener('hashchange', function(){
    buildModelFromHash();

    const checkboxes = document.querySelectorAll("#mainForm input[type=checkbox]");
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = false;
    });

    kmwSetForm(selectedLanguages);
});

//Also clears model and hash
function clearForm() {
    const checkboxes = document.querySelectorAll("#mainForm input[type=checkbox]");
    selectedLanguages = [];
    window.location.hash = "#";
    checkboxes.forEach(function(checkbox){
        checkbox.checked = false;
    });
    // document.getElementById('queryInput').value = "";

}

$('#clear-button').on('keypress click', function(e){
    if (e.which === 13 || e.type === 'click') {
        clearForm();
    }
});

// on initial page load, set up our form to match whatever is in the hash fragment. IE we will
// check whatever checkboxes / select whatever languages as dictated by the hash fragment.
// This is part of the work of implementing the ability for a user to bookmark a page and come 
// back to it to have all of the same checkboxes checked instead of having to recheck all of the
// checkboxes every time if they want to search using the same set of languages.
kmwSetForm(selectedLanguages);

// When the form is submitted, we want to submit our model instead of the form itself, 
// because we want the search results to be ordered based on the order that checkboxes/languages
// were checked. If someone clicked on English first, we want enlish results to be the first search
// results.
// function onFormSubmit(e) {
window.onSubmit = function(token) {
    // if (e.preventDefault) e.preventDefault();
    let actionUrl = new URL("results", document.baseURI);
    for (const language of selectedLanguages){
        actionUrl.searchParams.append(language[0], language[1]);
    }
    let searchQuery = document.getElementById('queryInput').value;
    actionUrl.searchParams.append("query", searchQuery);

    let postBodyPameters = {
        "g-recaptcha-response": token,
        "_token": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    // actionUrl.searchParams.append("g-recaptcha-response", token);
       
    // fetch(actionUrl);
    // window.location = actionUrl;
    window.kmwPost(actionUrl, postBodyPameters);

    return false; // I don't think this line does anything
}


// let form = document.getElementById('mainForm');
// form.addEventListener("submit", onFormSubmit);


function modelToQueryString(){
    const searchParams = new URLSearchParams();
    for (const language of selectedLanguages){
        searchParams.append(language[0], language[1]);
    }
    return searchParams.toString();
}

function addSelectedLanguage(languageCode){
    selectedLanguages.push([`language${selectedLanguages.length}`, languageCode]);
    window.location.hash = modelToQueryString();
}

function removeSelectedLanguage(languageCodeToRemove){
    selectedLanguages = selectedLanguages.filter(function(item){
        const languageCode = item[1];
        if (languageCode === languageCodeToRemove){
            return false;
        }
        else return true;
    })
    selectedLanguages.forEach(function(selectedLang, index){
        // selectedLanguages[index] = [`language${index}`, selectedLanguages[index][1]];
        selectedLanguages[index][0] = `language${index}`;
    })
    window.location.hash = modelToQueryString();
}

let checkboxes = document.querySelectorAll("#mainForm input[type=checkbox]");
checkboxes.forEach(function(checkbox){
    checkbox.addEventListener('change', (event) => {
        if (event.currentTarget.checked) {
            addSelectedLanguage(event.currentTarget.value);
        } else {
            removeSelectedLanguage(event.currentTarget.value);
        }
      })
});