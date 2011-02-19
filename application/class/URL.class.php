<?php
class URL {

    static public function Encode($url) {
        str_replace(array("-","_"), array("--", "__"), $url);
        return $url;
    }

    static public function Decode($url) {
        str_replace(array("--","__"), array("-", "_"), $url);
        return $url;
    }
}

?>
