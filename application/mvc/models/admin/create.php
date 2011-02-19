<?php
    $pageArg = ($_GET["page"] == null) ? -1 : $_GET["page"];
    $stmt = $SQL->query_describe($pageArg);
    $fields = array();
    
    foreach($stmt as $table) {
        if ($table['Key'] == "PRI") {
            $clef = $table['Field'];
        }
    }
    
    if ($_POST['submit'] != null) {    
       $data = array();
       $data[$clef] = null;
       foreach($_POST as $indice => $valeur) {
           if ($indice != "submit") {
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
       
       $SQL->query_addNewEntry($pageArg, $data);
       $page->RETOUR = '<div class="success">Elément créé.</div>';
    }
    
    $output = '<form action="#" method="post" enctype="multipart/form-data">';
    $output .= '<table cellpadding="3">';
    foreach($stmt as $table) {
        if ($table['Field'] != "(sortable)") {
            $fields[ $table['Field'] ] = new Field();
            $fields[ $table['Field'] ]->setConnexion(&$SQL);


            //regex sur type pour avoir size

            preg_match("#(([a-zA-Z])*)\((([0-9])*)\)#", $table['Type'], $matches);

            if ($matches != null) {
               $size = $matches[3];
               $type = $matches[1];
            } else {
                $size = "-1";
                $type = $table['Type'];
            }

            $fields[ $table['Field'] ]->setType( $type );
            $fields[ $table['Field'] ]->setName( $table['Field'] );
            $fields[ $table['Field'] ]->setSize( $size );
            $fields[ $table['Field'] ]->setKey( $table['Key'] );
            $fields[ $table['Field'] ]->setExtra( $table['Extra'] );
            $output .= $fields[ $table['Field'] ]->getHtmlField() ;
        }
    }
    
    $output .= '<tr><td colspan="2"><input type="submit" name="submit" value="Submit"/></td></tr>';
    $output .= '</table></form>';
    
    $page->CONTENT = $output;
?>
