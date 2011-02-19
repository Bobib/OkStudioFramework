<?php
/*****************************************************/
/***               BOBLib 1.0                      ***/
/***           06/06/08  PHP5-MySQL                ***/
/*****************************************************/
//update 16/08/2010 : passage au pdo

class Connexion
{
    //attributs :

    private $_host;
    private $_login;
    private $_pass;
    private $_base;
    private $_bdd;
    private $_connected = false;

    //methodes :
    public function __construct()
    {
        
        $xml = simplexml_load_file("application/conf/databaseConf.xml");
        $this->_host = $xml->host["value"];
        $this->_login = $xml->login["value"];
        $this->_pass = $xml->password["value"];
        $this->_base = $xml->name["value"];
    }

    public function __destruct()
    {
        $this->_bdd = null;
    }

    public function connect()
    {
        if (!$this->_connected) {
            
            try {
                $this->_bdd = new PDO("mysql:host=$this->_host;dbname=$this->_base", $this->_login, $this->_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
            
            $this->_connected = true;
        } else {
             $this->_bdd = null;
             $this->_connected = false;
             $this->connect();
        }
    }

    public function select($requete='')
    {
        if ($requete != '')
        {
            $retour = array();
            
            foreach ($this->_bdd->query($requete) as $row)
            {
                 array_push($retour, $row);
            }

            return $retour;
        }
    }

    public function insert($requete='')
    {

        $retour = ( $this->_bdd->exec($requete) != false) ? $retour : false;
        
        return $retour;
    }

    public function update($requete='')
    {
        $retour = null;

        if ($requete != '')
        {
            $retour = $this->_bdd->exec($requete);
        }

        return $retour;
    }



    public function count($requete='')
    {
        if ($requete != '')
        {   
            return sizeof( $this->_bdd->query($requete) );
        }
    }

    public function lastInsertId($requete) {
        $this->_bdd->lastInsertId();
    }

    public function set_host($_host) {
        $this->_host = $_host;
    }

    public function set_login($_login) {
        $this->_login = $_login;
    }

    public function set_pass($_pass) {
        $this->_pass = $_pass;
    }

    public function set_base($_base) {
        $this->_base = $_base;
    }


}
?>