<?php
    
    $pageArg = ($_GET["page"] == null) ? -1 : $_GET["page"];
    
    $stmtDescribe = $SQL->query_describe($pageArg);
    $sortable = false;
    for ($i=0; $i<sizeof($stmtDescribe); $i++) {

        if ($stmtDescribe[$i]["Field"] == "(sortable)") {
            $sortable = true;
            break;
        }
    }
    
    if ($_GET['execute'] == "delete") {
        $SQL->query_delete($pageArg, $_GET['key'], $_GET['value']);

        $page->RETOUR = '<div class="error">Elément supprimé.</div>';
    }


    $output = '<a href="admincreate_page-' . $pageArg . '.html" id="addButton"><span>Ajouter</span></a><br />';
    $output .= '<table class="adminTable ">';
    $arrayField = array();
    $output .= "<tr>";
    $counter = 0;
    $maxFields = 3;

    $output .= '<span id="tableInformations" style="visibility: hidden;">' . $pageArg . '</span>';

    //entete du tableau
    foreach($stmtDescribe as $description) {
        
        preg_match("#(.+)\((.*)\)#", $description['Field'], $matches);

        if ($matches == null && $description['Field'] != "(sortable)") {
            if ($counter < $maxFields) {
                $counter++;
                $output .= "<th>";
                if ($description['Key'] == "PRI") {
                    $key = $description;
                } 
                array_push($arrayField, $description['Field']);
                $output .= $description['Field'] . "</th>";
            }
        }
    }

    $output .= '<span id="idFieldInformations" style="visibility: hidden;">' . $key["Field"] . '</span>';
    
    $output .= "<th>Actions</th>";
    $output .= "</tr>";
    
    
    //Contenu du tableau
    $stmt = $SQL->query_getTableContent($pageArg, $arrayField, $sortable);

    $count = 0;
    foreach ($stmt as $element) {
            $count++;
            
            $fields .= "<tr><td>" . $element[ $key["Field"] ] . "</td>";
            
            foreach($arrayField as $field) {
                
                if ($field != $key["Field"]) {
                    $fields .=  "<td>" . substr($element[ $field ], 0, 40) . "</td>";
                }
                
            }
            
            $fields .= '<td> <a class="tableLink actionUpdate" href="adminupdate_id-' . $element[ $key["Field"] ] . '_page-' . $pageArg . '.html"></a>  <a class="tableLink actionDelete" href="adminread_value-' . $element[ $key["Field"] ] . '_key-' . $key["Field"] . '_page-' . $pageArg . '_execute-delete.html" onclick="javascript:return confirm(\'Etes-vous sur de vouloir supprimer cet élément ?\')"></a>';
            
            //sortable system
            if ($sortable) {

                $fields .='<span class="sortIdentifier">' . $element[ $key["Field"] ] . '</span>';

                //up arrow
                if ($count != 1) {
                    $fields .= '<a class="sortUP" rel="' . $element[ $key["Field"] ] . '"></a>';
                } else {
                    $fields .= '<span class="spacer"></span>';
                }

                //down arrow
                if ($count != sizeof($stmt)) {
                    $fields .= '<a class="sortDOWN" rel="' . $element[ $key["Field"] ] . '"></a>';
                }

            }

            $fields .= "</td>";
    }
    $output .=  $fields;
    
    $output .= '</table>';
    
    $page->CONTENT = $output;
    
?>
