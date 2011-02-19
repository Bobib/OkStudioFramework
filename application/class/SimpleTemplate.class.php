<?php
/**
 * Classe SimpleTemplate
 * cette classe offre un mini moteur de template, permettant simplement
 * de remplacer des variables de template dans des templates simples.
 * Elle offre l'avantage par rapport aux système de template évolués de se
 * charger très rapidement, pour les utilisateurs qui n'ont pas besoin de toutes
 * les fonctions d'un systeme de template complexe.
 *
 * Auteur : spiritualmind
 * Date de creation : 25/03/2006
 * Date de MAJ : 25/03/2006
 *  
 * Ajout : gestion de cache et méthode getPage(modele.php, vue.tpl)
 * Auteur : bobib
 * Date : 04/02/2010
 *
 * Ajout : utilisation de getpage avec un objet $page au lieu d'une page.php en entrée.
 *         permet d'utiliser le MVC dans un modele pour en récuperer le resultat et le
 *         réutiliser par la suite.
 * Auteur : bobib
 * Date : 16/08/2010
 *
 * Ajout : ajout de l'ACL
 * Auteur : bobib
 * Date : 17/08/2010
 *
 * Licence : GPL
 */
class SimpleTemplate{

    //attributs
    private $_file ;
    private $_isCacheEnabled;
    private $_cacheTimeLimit;
    private $_ACL = null;

    //constructeur
    /**
     * charge le fichier de template
     * @param boolean $isCacheEnabled determine si le template utilise le cache (par défaut false)
     * @param integer $cacheTimeLimit durée des fichiers dans le cache (par défaut 1440min -> 24h)
     */
    function SimpleTemplate($isCacheEnabled=false, $cacheTimeLimit=1440)
    {
        $this->_isCacheEnabled = $isCacheEnabled;
        $this->_cacheTimeLimit = $cacheTimeLimit;
    }

    /**
     * charge le fichier de template
     * @param string $file le chemin du fichier de template
     * @return bool false si le fichier n'est pas bon
     */
    function loadTemplateFile($file)
    {
        if (file_exists($file))
        {
            if (is_readable($file))
            {
                if (!is_dir($file))
                {
                    $this->_file = file_get_contents($file) ;
                    return true ;
                } else
                {
                    return false;
                }
            }else
            {
                return false ;
            }
        }else
        {
            return false ;
        }
    }

    /**
     * parse le fichier de template avec les variables definies
     * @param object $page l'objet contenant les variables a utiliser
     * @param boolean $debug active ou non la visiblité des variables de template
     * @return string le fichier parsé avec les variables
     */
    function parse($page=null,$debug=null)
    {
        //transformation de l'objet en tableau
        if (is_array($page) || is_object($page))
        {
            $trans = array() ;
            foreach($page as $key => $value)
            {
                $trans['{'.$key.'}'] = $value ;
            }

            if ($debug == null)
            {
                $tmp = strtr($this->_file,$trans);
                return preg_replace("/\{([0-9a-zA-Z_]*)\}/","",$tmp) ;
            }else
            {
                return (strtr($this->_file,$trans)) ;
            }
        }else
        {
            if ($debug == null)
            {
                return preg_replace("/\{([0-9a-zA-Z]*)\}/","",$this->_file) ;
            }else
            {
                return ($this->_file) ;
            }
        }
    }

    /**
     * parse le fichier de template avec les variables definies
     * retourne le resultat dans un fichier passé en parametre
     * Attention : le fichier passé en paramètre doit soit ne pas exister, soit
     * etre vierge, car cette methode efface tout le contenu du fichier
     * @param object $page l'object contenant les variables a utiliser
     * @param string le chemin et le nom du fichier pour le resultat
     */
    function fparse(&$page,$file)
    {
        $f = fopen($file,"w") ;
        fwrite($f,$this->parse($page)) ;
        fclose($f);
    }

    /**
     * Formate le contenu passé en parametre pour une sorti sur un flux HTML
     * la methode accepte une page sous forme de modele objet (pour le Template)
     * ou simplement une chaine de caractere arbitraire
     * @param $page un objet page ou une chaine de caractere
     * @return le meme type que le parametre en entree
     */
    function htmlFormat($page)
    {
        if (is_object($page))
        {
            foreach ($page as $key => $value)
            {
                $page->$key = nl2br(htmlentities(stripslashes($value))) ;
            }
        }else
        {
            $page = nl2br(htmlentities(stripslashes($page))) ;
        }
        return $page ;
    }

    /*
     * Gestion du cache
     */


    /**
     * Verifie si la page existe en cache sur le serveur
     * @param string $pageurl est l'adresse du fichier de modèle requis
     * @return boolean true si la page existe, false si elle n'existe pas
     */
     function checkPageInCache($pageUrl){
        if ($this->_isCacheEnabled) {
            return (file_exists("application/cache/".md5($pageUrl))) ? true: false;
        } else { return false; }
     }

     /**
     * cree la page demandé dans le cache
     * @param string $pageurl est l'adresse du fichier de modèle requis
     * @return boolean true si l'ecriture a réussi, false en cas d'erreur
     */
     function createPageInCache($pageUrl, $content) {
        if ($this->_isCacheEnabled) {
            $pageUrl = "application/cache/".md5($pageUrl);
            if (file_exists($pageUrl)) {
                unlink($pageUrl);
            }
            $cacheFile = fopen($pageUrl, "w");
            $content = "<!-- Cache generated on " . date("m-d-Y H:i:s") . "-->\n" . $content;
            fputs($cacheFile, $content);
            fclose($cacheFile);
        }
     }

     /**
     * recupère la page en cache depuis le serveur
     * les erreurs sont interprétés en 404 pour une gestion facile et rapide
     * de leur affichage (le visiteur s'en fout que md5(file) est pas readable
     * il verra un 404 si la apge est inaccessible).
     * @param string $pageurl est l'adresse du fichier de modèle requis
     * @return string renvoi le contenu de la page de cache
     */
     function getPageInCache($pageUrl) {
         $page = "application/cache/".md5($pageUrl);
         if ($this->_isCacheEnabled) {
            if (file_exists($page)) {
                if (is_readable($page)) {
                    return file_get_contents($page) ;
                }else { return "404" ; }
            }else { return "404" ; }
        }else { return null ; }
     }

      /**
       * retourne la page parsé, prete a etre affichée
       * @param string $modele page du modele à charger ou objet $page
       * @param string $vue page de la vue a charger
       * @return string contenu de la page pret à etre affichée
       */
       function getPage($modele, $vue) {

           //Charge les templates utilisés sur toutes les pages
           $d = dir("application/mvc/models/general/");
           while($entry = $d->read()) {
              $file = (eregi("\.php$", $entry)) ? $entry : null;
              if ($file != null) { include_once("application/mvc/models/general/".$file);}
           }
           $d->close();

           //charge les classes générales de l'application
           $d = dir("application/mvc/models/autoLoadedClass/");
           while($entry = $d->read()) {
              $file = (eregi("\.php$", $entry)) ? $entry : null;
              if ($file != null) { include_once("application/mvc/models/autoLoadedClass/".$file);}
           }
           $d->close();
           
            //charge la vue
            $this->loadTemplateFile("application/mvc/views/".$vue);

            //Si ACl activé, verif les droits d'accès
            if ($this->_ACL != null) {
                if (!$this->_ACL->checkAuthorization($modele)) {
                    $modele = "503.php";
                }
            }

            $output = null;
            //verifie si la page existe en cache
            if ($this->checkPageInCache($modele)) {
               //redirige vers la page en cache
               $output = $this->getPageInCache($modele);
                 if ($output != null && $output != "404") {
                     //recupere la page dans le cache
                     if(round((time()-filemtime("application/cache/".md5($modele)))/60) > $this->_cacheTimeLimit) {
                        $page = null;
                        $output = (!@include("application/mvc/models/".$modele)) ? "404" : $this->parse($page);
                       if ($output != "404") {
                             $this->createPageInCache($modele, $output);
                       }
                     }
                 } else {$output = "404" ;}

            } else {
                 if (gettype($modele) != "string") {
                         $output = $this->parse($modele);
                } else {
                    if(!@include("application/mvc/models/".$modele)) {
                         $output = "404" ;
                    } else {
                        $output = $this->parse($page);
                        $this->createPageInCache($modele, $output);
                    }
                }
            }
            return $output;
       }

       /**
       * initialise l'ACL
       * @param string $xml chemin du fichier de configuration xml
       * @param string $user role de l'utilisateur a tester
       */
       public function initACL($xml, $user) {
           $this->_ACL = new ACL();
           $this->_ACL->setUser($user);
           $this->_ACL->LoadFromXml($xml);
       }

}
?>