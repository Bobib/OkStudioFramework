<?php
    //load database module
    $SQL = new SQL(new Connexion());
    
    //Load traduction module
    include_once("application/class/Traduction.class.php");
    $traduction = new traduction("application/");
    
    //Admin part :
    
    if (preg_match("#^(admin)#", MODEL)) {

        if ($_SESSION["user"]->getRole() == "admin") {
            $listePage = $SQL->query_getTables();

            $pageArg = ($_GET["page"] == null) ? -1 : $_GET["page"];

            foreach ($listePage as $table) {
                    $cssClass = ($pageArg == $table[0]) ? "adminMenuBtnActif" : "adminMenuBtn";
                    $tables .= '<a href="adminread_page-' . $table[0] . '.html" class=" ' . $cssClass . '"><span>' . $table[0] . '</span></a>';
            }
        } else {
             $tables = '<a href="adminhome.html" class="adminMenuBtnActif"><span>Authentification</span></a>';
        }
        $page->MENU =$tables;
    }

//Langues
$pageName = str_replace(".php", "", MODEL);
    $enActif = ($_SESSION['langue'] == "en_US") ? 'class="actif"' : "" ;
    $frActif = ($_SESSION['langue'] == "fr_FR") ? 'class="actif"' : "" ;
    $esActif = ($_SESSION['langue'] == "es_ES") ? 'class="actif"' : "" ;

    $page->MenuLangues = '<a ' . $enActif . ' href="' . $pageName . '_langue-en.html">En</a> .
                          <a ' . $frActif . ' href="' . $pageName . '_langue-fr.html">Fr</a> .
                          <a ' . $esActif . ' href="' . $pageName . '_langue-es.html">Es</a>';

//Menu
$page->Menu = '<a class="menuBtn' . ((MODEL == "accueil.php") ? ' actif' : '' ) . '" href="accueil.html">News</a>
                   <a class="menuBtn' . ((MODEL == "familia.php") ? ' actif' : '' ) . '" href="familia.html">Familia</a>
                   <a class="menuBtn' . ((MODEL == "collection.php") ? ' actif' : '' ) . '" href="collection.html">Collection</a>
                   <a class="menuBtn' . ((MODEL == "events.php") ? ' actif' : '' ) . '" href="events.html">Events</a>
                   <a class="menuBtn' . ((MODEL == "video.php") ? ' actif' : '' ) . '" href="video.html">Video</a>
                   <a class="menuBtn' . ((MODEL == "shop.php") ? ' actif' : '' ) . '" href="#">Shop</a>
                   <a class="menuBtn' . ((MODEL == "store.php") ? ' actif' : '' ) . '" href="store.html">Store</a>
                   <a class="' . ((MODEL == "about.php") ? ' actif' : '' ) . '" href="about.html">About</a>
                   
                    <form id="searchForm" method="post" action="recherche.html">
                        <p>
                            <input type="text" name="searchBox" id="searchBox" value="' . $traduction->translate("Search") . '" />
                            <img id="searchBtn" src="images/searchBtn.jpg" alt="' . $traduction->translate("Search") . '" />
                        </p>
                    </form>';

//Footer

$page->Footer = '<a class="footerLink" href="contact.html">Contact</a>
                     <a class="footerLink" href="legal.html">Legal</a>
                     <span class="okstudio">website by <a id="okstudio" href="http://www.okstudio.fr">Ok Studio</a></span>
                     <a class="footerLink" href="http://www.facebook.com/group.php?gid=14580729238">Facebook</a>
                     <a class="footerLink" href="#">Twitter</a>';
    
    
?>
