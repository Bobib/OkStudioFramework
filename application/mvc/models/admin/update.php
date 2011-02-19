<?php

    $id = ($_GET["id"] == null) ? -1 : $_GET["id"];
    $pageArg = ($_GET["page"] == null) ? -1 : $_GET["page"];
    
    $stmt = $SQL->query_describe($pageArg);
    $fields = array();
    
    foreach($stmt as $table) {
        if ($table['Key'] == "PRI") {
            $key = $table['Field'];
        }
    }
    
    if ($_POST['submit'] != null) {    
       $data = array();

       foreach($_POST as $indice => $valeur) {
           if ($indice != "submit" && !preg_match("#Select#", $indice)) {
                $data[$indice] = $valeur;
           }
       }

       if ($_FILES != null) {
           foreach($_FILES as $indice => $file) {
               if ($file["name"] != null) {
                   if($file['error'] == UPLOAD_ERR_OK) {
                       preg_match("#(.+)\.(.+)#", $file["name"], $matches);
                       $fileName = md5(time() . rand()) . '.' . $matches[2];
                       $data[$indice] = $fileName;
                       move_uploaded_file($file['tmp_name'], 'uploads/' . $fileName);
                   }
               }
           }
       }
       
       $SQL->query_updateEntry($pageArg, $key, $id, $data);
       $page->RETOUR = '<div class="success">Elément modifié.</div>';
    }
    
    $data = $SQL->query_getTableValue($pageArg, $key, $id);
    
    $output = '<form action="#" method="post" enctype="multipart/form-data">';
    $output .= '<table cellpadding="3">';


    foreach($stmt as $table) {
        if ($table['Field'] != "(sortable)") {
            $fields[ $table['Field'] ] = new Field();
            $fields[ $table['Field'] ]->setConnexion(&$SQL);
            $fields[ $table['Field'] ]->setValue( $data[0][ $table['Field'] ] );

            //regex sur type pour avoir size

            preg_match("#(([a-zA-Z])*)\((([0-9])*)\)#", $table['Type'], $matches);

            if ($matches != null) {
               $size = $matches[3];
               $type = $matches[1];
            } else {
                $size = "-1";
                $type = $table['Type'];
            }

            if ($table['Key'] == "PRI") {
                $clef = $table['Field'];
            }

            $fields[ $table['Field'] ]->setType( $type );
            $fields[ $table['Field'] ]->setName( $table['Field'] );
            $fields[ $table['Field'] ]->setSize( $size );
            $fields[ $table['Field'] ]->setKey( $table['Key'] );
            $fields[ $table['Field'] ]->setExtra( $table['Extra'] );
            $output .= $fields[ $table['Field'] ]->getHtmlField();
        }
    }
    
    $output .= '<tr><td colspan="2"><input type="submit" name="submit" value="Submit"/></td></tr>';
    $output .= '</table></form>';
    
    $page->CONTENT = $output;
?>
