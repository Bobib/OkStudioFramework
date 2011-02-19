<?php
    if ($_POST['bddinfos'] != null) {

            $bddInfos = explode("|", $_POST['bddinfos']);
            $values = $_POST["values"];
            $existing = false;
            
            $values = ( ereg("(\;)$", $values) ) ? substr($values,0,-1) : $values;
            $values = ( ereg("^(\;)", $values) ) ? substr($values,1) : $values;


            if (sizeof($values) == 0 ){
                $avalues = array();
                $avalues[0] = $values;
            } else {
                $avalues = explode(";", $values);
            }

            $linkedFields = $SQL->query_manyToMany($bddInfos[0], $bddInfos[1], $avalues, $bddInfos[2]);

            foreach ($linkedFields as $linkedField) {
                 $page->CONTENT .= '<br />' . $linkedField[ $bddInfos[1] ] . ' : ' . $linkedField[ $bddInfos[2] ] . ' <span id="' . $_POST['element'] . '|' . $linkedField[ $bddInfos[1] ] . '" class="removeLink" ></span>';
            }

    }

    if ($_POST["actualId"] != null) {
        $table = $_POST["table"];
        $idField = $_POST['idField'];

        $values = array();
        $values[0] = array();
        $values[0]["id"] = $_POST["actualId"];
        $values[0]["pos"] = $_POST["targetPos"];
        $values[1] = array();
        $values[1]["id"] = $_POST["targetId"];
        $values[1]["pos"] = $_POST["actualPos"];

        $SQL->query_updateSort($idField, $table, $values);
    }

?>
