<?php

    class SQL {

        private $_connexion;

        public function __construct($connexion = null){
            $this->_connexion = $connexion;
            $this->_connexion->connect();
        }

        public function setConnexion($connexion) {
            $this->_connexion = $connexion;
            $this->_connexion->connect();
        }

        private function cleanString($str) {
            $str = (!get_magic_quotes_gpc()) ? addslashes($str) : $str;
            $str = strip_tags(htmlspecialchars($str));
            return $str;
        }
        
        // Admin Queries :
        
        public function query_describe($arg){
            $query = 'DESCRIBE ' . $arg . ';';
            return $this->_connexion->select($query);
        }
        
        public function query_oneToMany($table, $id, $field){
            $query = 'Select  ' . $id . ', ' . $field . ' From ' . $table . ';';
            return $this->_connexion->select($query);
        }

        public function query_manyToMany($table, $id, $values, $field){

            foreach($values as $value) {
                        $queryComposer .= 'Or ' . $table . '.' . $id . ' = ' . $value . ' ';
                    }

                    $queryComposer = substr($queryComposer, 2);

            $query = 'Select  ' . $id . ', ' . $field . ' From ' . $table . ' Where ' . $queryComposer . ';';
            return $this->_connexion->select($query);
        }

        public function query_getTableContent($table, $fields) {
        
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $qfield .= ", " . $field;
                } 
                $qfield = substr($qfield, 2);
            }else {
                $qfield = $fields;
            }
            
            $query = 'Select  ' . $qfield . ' from ' . $table . ';';

            return $this->_connexion->select($query);
        }
        
        public function query_getTableValue($table, $id, $value) {
            $query = 'Select * from ' . $table . ' where ' . $id . '=' . $value;
            return $this->_connexion->select($query);
        }
        
        public function query_addNewEntry($table, $data) {
            
            foreach($data as $indice => $valeur) {
                $champs .= ", `" . $indice ."`";
                $valeurs .= ", " . (($valeur == null) ? "NULL" : "'".$this->cleanString($valeur)."'");
            }
            
            $champs = substr($champs, 2);
            $valeurs = substr($valeurs, 2);
            
            $query = 'Insert Into `' . $table . '` ( ' . $champs . ' ) Values (' . $valeurs . ');';
            $this->_connexion->insert($query);
        }
        
        public function query_updateEntry($table, $key, $valueKey, $data) {
            foreach($data as $indice => $valeur) {
                $champs .= ", `" . $indice ."` = '" . $valeur . "'";
            }
            
            $champs = substr($champs, 2);
            $query = 'Update `' . $table . '` Set ' . $champs . ' Where `' . $key . '`=' . $valueKey . ';';
            $this->_connexion->insert($query);
        }
        
        public function query_getTables() {
            $query = "SHOW TABLES";
            return $this->_connexion->select($query);
        }
        
        public function query_delete($table, $key, $value) {
            $query = "DELETE FROM `" . $table . "` WHERE `" . $key . "` = " . $value . " LIMIT 1";
            $this->_connexion->select($query);
        }
        
		public function query_updateSort($idField, $table, $positions) {
            $idField = $this->cleanString($idField);
            $table = $this->cleanString($table);

             for ($i=0; $i< sizeof($positions); $i++){
               $query = "UPDATE `" . $table . "` SET  `(sortable)` =  '" . $i . "' WHERE  `" . $idField . "` =" . $positions[$i] . ";";
               $this->_connexion->select($query);
             }

        }

        //Queries :
    }

?>
