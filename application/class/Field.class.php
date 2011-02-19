<?php

    class Field {
        
        private $_name;
        private $_type;
        private $_size;
        private $_key;
        private $_extra;
        private $_values = array();
        private $_value;
        private $_SQL;
        private $_completeName;
        
        private $_secondaryKey_table;
        private $_secondaryKey_id;
        private $_secondaryKey_showField;
        private $_secondaryKey_values;

        private $_manyToMany = false;
        
        public function __construc() {
        }
        
        public function getHtmlField() {
            
            if ($this->_extra != "auto_increment") {
                if ($this->_secondaryKey_id == null ) {
                    switch ($this->_type) {
                        case "smallint" :
                        case "int" :
                        case "integer" :
                            $value = ($this->_value == null) ? "0" : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' . $this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "float" :
                        case "double" :
                        case "precision" :
                            $value = ($this->_value == null) ? "00.00" : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "date" :
                        case "datetime" :
                            $value = ($this->_value == null) ? date('d/m/Y') : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "timestamp" :
                            $value = ($this->_value == null) ? time() : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "year" :
                            $value = ($this->_value == null) ? date('Y') : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "bit" :
                        case "tinyint" :
                        case "bool" :
                            $value = ($this->_value == null) ? "1" : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td>';
                            $htmlField .=  '<select name="' . $this->_completeName . '" >';
                            $htmlField .= '<option value="1" ' . (($value == 1 ) ? ' selected="selected" ' : '') . '>Oui</option>';
                            $htmlField .= '<option value="0" ' . (($value == 0 ) ? ' selected="selected" ' : '') . '>Non</option>';
                            $htmlField .= '</select>';
                            $htmlField .='</td></tr> ';
                            break;
                        case "char" :
                            $value = ($this->_value == null) ? "" : $this->_value;
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $value . '"/></td></tr>';
                            break;
                        case "varchar" :
                            if (preg_match("#\(file\)#", $this->_completeName)) {
                                $value = ($this->_value == null) ? "" : '<br />(<a href="uploads/' . $this->_value . '">' . $this->_value . '</a>)';
                                $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input type="file" name="' . $this->_completeName . '" /> ' . $value . '</td></tr>';
                            } else {
                                $value = ($this->_value == null) ? "" : $this->_value;
                                $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td> <input name="' . $this->_completeName . '" value="' . $this->_value . '" /></td></tr>';
                            }
                            break;
                        case "text" :
                            $value = ($this->_value == null) ? "" : $this->_value;
                            $htmlField = '<tr><td colspan="2"><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '<br /> <textarea name="' . $this->_completeName . '" style="width: 100%; height: 100px;">' . $value . '</textarea></td></tr>';
                            break;
                        case "enum" :
                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' .$this->_name . '</td><td>';
                            $htmlField .=  '<select name="' . $this->_completeName . '" >';

                            foreach($this->_values as $value)  {
                                $htmlField .= '<option value="' . $value . '" ' . (($value == $this->_value ) ? ' selected="selected" ' : '') . '>' . $value . '</option>';
                            }
                            $htmlField .= '</select>';
                            $htmlField .='</td></tr> ';
                            break;
                    }

                } elseif ($this->_manyToMany == false) {

                    //jointure one to many
                    $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' . $this->_name . '</td><td> <select name="' . $this->_completeName . '">';
                    foreach ($this->_secondaryKey_values as $value) {                  
                        $htmlField .= '<option value="' . $value[$this->_secondaryKey_id] . '" ' . (($this->_value == $value[$this->_secondaryKey_id])? 'selected="selected"' : '') . '>' . $value[$this->_secondaryKey_showField] . '</option>';
                    }
                   $htmlField .= '</select></td></tr>';
                } else {
                        //jointure many to many
                        $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' . $this->_name . '</td><td> ( after creation )</td></tr>';
                        if (MODEL != 'admin/create.php') {

                            $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />' . $this->_name . '</td><td> <select name="Select' . $this->_completeName . '">';
                            foreach ($this->_secondaryKey_values as $value) {
                                $htmlField .= '<option  value="' . $value[$this->_secondaryKey_id] . '">' . $value[$this->_secondaryKey_showField] . '</option>';
                            }
                           $htmlField .= '</select><span id="' . $this->_completeName . '" class="addLink" ></span>';
                           $htmlField .= '<span class="' . $this->_secondaryKey_table . '|' . $this->_secondaryKey_id . '|' . $this->_secondaryKey_showField . '|' . $this->_completeName . '" id="queryInfos' . $this->_completeName . '" ></span>';
                           $htmlField .= '<input type="hidden" name="' . $this->_completeName . '" value="' . $this->_value . '"/>';


                           //liste des valeurs liées
                           if ($this->_value != 0) {
                                $values = explode(";", $this->_value);
                                $linkedFields = $this->_SQL->query_manyToMany($this->_secondaryKey_table, $this->_secondaryKey_id, $values, $this->_secondaryKey_showField);
                           }

                           $htmlField .= '<div id="result' . $this->_completeName . '">';
                           foreach ($linkedFields as $linkedField) {
                                $htmlField .=  '<br />' . $linkedField[ $this->_secondaryKey_id ] . ' : ' . $linkedField[ $this->_secondaryKey_showField ] . ' <span id="' . $this->_completeName . '|' . $linkedField[ $this->_secondaryKey_id ] . '" class="removeLink " ></span>';
                           }
                           $htmlField .= '</div></td></tr>';
                       }
                }


            } else {
                $value = ($this->_value == null) ? "new" : $this->_value;
                $htmlField = '<tr><td><img src="images/admin/puce.jpg" alt="puce" />Identifiant </td><td>' . $value . '</td></tr>';
            }
            return $htmlField;
        }
        
        public function setSecondaryKey($table, $id, $showField) {
            $this->_secondaryKey_table = $table;
            $this->_secondaryKey_id = $id;
            $this->_secondaryKey_showField = $showField;
        }
        
        public function setName($name) {
            
            $this->_completeName = $name;
            if (preg_match("#(.+)\_manyToMany\(([a-zA-Z]+)\|([a-zA-Z]+)\|([a-zA-Z]+)\)#", $name, $matches)) {
               //jointure many to many
               $this->_name = $matches[1];
               $this->_secondaryKey_table = $matches[2];
               $this->_secondaryKey_id = $matches[3];
               $this->_secondaryKey_showField = $matches[4];
               $this->_manyToMany = true;

               $this->_secondaryKey_values = $this->_SQL->query_oneToMany($this->_secondaryKey_table,
                                                                                                                                   $this->_secondaryKey_id,
                                                                                                                                   $this->_secondaryKey_showField
                                                                                                                                  );
                
            } elseif (preg_match("#(.+)\_oneToMany\(([a-zA-Z]+)\|([a-zA-Z]+)\|([a-zA-Z]+)\)#", $name, $matches)) {
               
                //jointure one to many
                $this->_name = $matches[1];
                
                $this->_secondaryKey_table = $matches[2];
                $this->_secondaryKey_id = $matches[3];
                $this->_secondaryKey_showField = $matches[4];
                
                $this->_secondaryKey_values = $this->_SQL->query_oneToMany($this->_secondaryKey_table, 
                                                                                                                                   $this->_secondaryKey_id,
                                                                                                                                   $this->_secondaryKey_showField
                                                                                                                                  );
            }  elseif (preg_match("#(.+)\(file\)#", $name, $matches)) {
                //varchar contenant une url de fichier
                $this->_name = $matches[1];
            } else {
                //champ simple
                 $this->_name = $name;
            }
        }
        
        public function setConnexion($connexion) {
            $this->_SQL = $connexion;
        }
        
        public function setType($type) {

            if (preg_match("#enum\((\'(.)+\')+\)#", $type, $matches)) {
                //enum
                $values = str_replace("'", "", $matches[1]);
               $this->setValues( explode(",", $values) );
                
                $this->_type = "enum";
            } else {
                $this->_type = $type;
            }

        }
        
        public function setValues($type) {
            $this->_values = ($this->_values != null) ? $this->_values : $type;
        }
        
        public function setValue($value) {
            $this->_value = $value;
        }
        
        public function setSize($size) {
            $this->_size = $size;
        }
        
        public function setKey($key) {
            $this->_key = $key;
        }
        
        public function setExtra($extra) {
            $this->_extra = $extra;
        }
        
        public function getName() {
            return $this->_name;
        }
        
        public function getValues() {
            return $this->_values;
        }
        
        public function getValue() {
            return $this->_value;
        }
        
        public function getType() {
            return $this->_type;
        }
        
        public function getSize() {
            return $this->_size;
        }
        
        public function getKey() {
            return $this->_key;
        }
        
        public function getExtra() {
            return $this->_extra;
        }
        
    }

?>
