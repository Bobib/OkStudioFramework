<?php
    class ACL {

        private $_roles;
        private $_pages;
        private $_user;

       /**
       * retourne la page parsé, prete a etre affichée
       * @param string $xml : chemin du fichier xml de configuration
       */
        public function LoadFromXml($xml) {
            $xml = simplexml_load_file($xml);
            
            foreach($xml->roles->role as $role) {
                $this->_roles[ (string) $role["name"] ] = (string) $role["inherit"];
            }

            foreach($xml->pages->page as $page) {
                $this->_pages[ (string) $page["model"] ] = (string) $page["authorized"];
            }
        }

       /**
       * verifie si l'utilisateur peut acceder a la page demandée
       * @param string $model : page "modele.php" demandée
       * @return boolean : true si l'utilisateur peut y accéder, false sinon
       */
        public function checkAuthorization($model) {
            $retour = true;

            if ($this->_user != null && $this->_pages[$model] != "") {
                
                $role = $this->_user;
                while($role != $this->_pages[$model]) {

                    if ($role == "") {
                        $retour = false;
                        break;
                    }

                    $role = $this->_roles[$role];
                }
            }

            return $retour;
        }


        public function setUser($user) {
            $this->_user = $user;
        }

        public function getRoles() {
            return $this->_roles;
        }

        public function setRoles($roles) {
            $this->_roles = $roles;
        }

        public function getPages() {
            return $this->_pages;
        }

        public function setPages($pages) {
            $this->_pages = $pages;
        }


    }
?>
