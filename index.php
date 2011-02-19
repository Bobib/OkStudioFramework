<?php
    
    //include object in session BEFORE initializing the session
    set_include_path(get_include_path()
        . PATH_SEPARATOR . 'application/conf/'
        . PATH_SEPARATOR . 'application/class/'
        . PATH_SEPARATOR . 'application/library/'
        . PATH_SEPARATOR . 'application/mvc/models/autoLoadedClass/'
    );
    
    require_once('AutoLoader.php');
    
    session_start();
    
    setlocale(LC_TIME,"fr_FR.utf8") ;
    header('Content-type: text/html; charset=UTF-8');

    //test d'admin
   if ($_POST["password"] != null && $_POST["login"] != null)  {

       $xml = simplexml_load_file("application/conf/admin.xml");

        if ($_POST["password"] == $xml->Password["value"] && $_POST["login"] == $xml->Login["value"]) {
            $_SESSION["user"]->setRole("admin");
        } else {
            $_SESSION["user"]->setRole("guest");
        }

    } 
    
    $tplEngine = new SimpleTemplate();

    //initialize ACL
    $_SESSION["user"] = ($_SESSION["user"] == null) ? new User() : $_SESSION["user"];
    $tplEngine->initACL( "application/conf/ACL.xml", $_SESSION["user"]->getRole() );

    //get MVC map
    $pages = PageLoader::loadFromXML("application/conf/MVCMap.xml") ;

    if (isset($_REQUEST["displayPage"])  && $_REQUEST["displayPage"] != "" ) {
        $displayPage = ( !is_null($pages[strtolower($_REQUEST["displayPage"])]) ) ? $pages[strtolower($_REQUEST["displayPage"])] : $pages["404"] ;
        define("MODEL", $displayPage["Model"]);
        define("VIEW", $displayPage["View"]);
        $page = $tplEngine->getPage($displayPage["Model"], $displayPage["View"]);
    } else {
        define("MODEL", $pages['home']["Model"]);
        define("VIEW", $pages['home']["View"]);
        $page = $tplEngine->getPage($pages['home']["Model"], $pages['home']["View"]);
    }

    echo $page;
?>