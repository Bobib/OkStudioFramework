<?php
class PageLoader {

    static public function loadFromXML($url) {
        $xml = simplexml_load_file($url);
        foreach ($xml->page as $page) {
           $pages[ (string) $page["AccessString"] ] = Array("Model" => (string) $page["Model"], "View" => (string) $page["View"]);
        }
        return $pages;
    }

}

?>
