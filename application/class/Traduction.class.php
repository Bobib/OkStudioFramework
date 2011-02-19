<?php

    class Traduction {

        private $_xmlPath;

        public function __construct($path = null) {

            $this->_xmlPath = $path;

            if (isset($_GET['langue'])) { // Si l'utilisateur a choisi une langue
                 define('LANGUE',$_GET['langue']);
            }
            elseif(isset($_SESSION['langue'])){
                define('LANGUE',$_SESSION['langue']);
            }
            else{
                define('LANGUE',$_SERVER['HTTP_ACCEPT_LANGUAGE'][0].$_SERVER['HTTP_ACCEPT_LANGUAGE'][1]);
            }

            switch (substr(LANGUE, 0, 2)) { // En fonction de la langue, on crée une variable $langage qui contient le code
                 case 'fr':
                     $langage = 'fr_FR';
                 break;
                 case 'en':
                     $langage = 'en_US';
                 break;
                 case 'es':
                     $langage = 'es_ES';
                 break;
                 default:
                     $langage = 'fr_FR';
                 break;
            }

            $_SESSION['langue'] = $langage;

        }

        /**
         * Traduit le mot en entrée par celui correspondant à la langue en session
         * @param <string> $word
         */
        public function translate($word) {

            $xmlFile = $this->_xmlPath . "lang/" . $_SESSION['langue'] . "/lang.xml";
            $xml = new SimpleXMLElement(file_get_contents($xmlFile));
            if(isset($xml->word)){
                foreach($xml->word as $element){
                    $word = str_replace($element['find'], $element['replace'], $word);
                }
            }
            return $word;
        }

        /**
         * Traduit le mot entré traduit par celui d'origine de la page
         * @param <string> $word
         */
        public function reverseTranslate($word) {

            $xmlFile = $this->_xmlPath . "lang/" . $_SESSION['langue'] . "/lang.xml";
            $xml = new SimpleXMLElement(file_get_contents($xmlFile));
            if(isset($xml->word)){
                foreach($xml->word as $element){
                    $word = str_replace( remplaceAccent($element['replace']), $element['find'], remplaceAccent($word) );
                }
            }
            return $word;
        }

        public function get_xmlPath() {
            return $this->_xmlPath;
        }

        public function set_xmlPath($_xmlPath) {
            $this->_xmlPath = $_xmlPath;
        }
    }

?>
