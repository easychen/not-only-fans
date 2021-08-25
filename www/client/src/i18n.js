import i18n from 'i18next';
import Backend from 'i18next-xhr-backend';
import LanguageDetector from 'i18next-browser-languagedetector';
import { reactI18nextModule } from 'react-i18next';
import axios from 'axios';


const dev_api = 'http://0.0.0.0:8088/';

i18n
  .use(Backend)
  .use(LanguageDetector)
  .use(reactI18nextModule)
  .init({
    fallbackLng: 'en-US',
    lng: localStorage.getItem("i18nextLng") || "en-US",
    // have a common namespace used around the full app
    ns: ['translations'],
    defaultNS: 'translations',

    debug: false,
    saveMissing:false,
    missingKeyHandler: function(lng, ns, key) {
        //console.log(lng, ns, key);
        var params = new URLSearchParams();
        params.append("lng" , JSON.stringify( lng ));
        params.append("ns" ,  ns );
        params.append("key" , key );
        const { data } = axios.post( dev_api + 'misswords' , params );
    },
    interpolation: {
      escapeValue: false, // not needed for react!!
    },

    react: {
      wait: true
    }
  });


export default i18n;