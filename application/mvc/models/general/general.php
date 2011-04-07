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


    
    
?>
