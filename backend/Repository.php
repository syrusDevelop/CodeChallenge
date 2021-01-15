<?php


/**
 * Class Repository
 *
 * Backend classe - for the transaction with the database
 *  
 */
class Repository
{

    /**
     * SQL Adapter 
     */
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("mysql:host=127.0.0.1;dbname=telefonbuchdb", 'root', '');
        } catch (Exception $e) {
            echo "<script>alert('Es konnte keine Verbindung zu DB hergestellt werden')</script>";
        }
    }

    /**
     * Add a person to the database
     * 
     * @param $firstName 
     * @param $lastName
     * @param $telephoneNumber
     */
    public function insertPerson($firstName, $lastName, $telephoneNumber)
    {
        $stmt = $this->pdo->prepare("INSERT INTO contacts (firstName, lastName, telephoneNumber ) VALUES ( :firstName, :lastName,:telephoneNumber)");
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':telephoneNumber', $telephoneNumber);
        echo $stmt->execute();
    }

    /**
     * Sets the SQL based on the dialed phone number
     * 
     * @param $searchingNumber
     * @return String $buildSql
     * 
     */
    private function getSearchingSQL($searchingNumber)
    {
        $searchingElments = str_split($searchingNumber, 1);
        $telephonKeyboard = [
            0 => "",
            1 => "",
            2 => ["A", "B", "C"],
            3 => ["D", "E", "F"],
            4 => ["G", "H", "I"],
            5 => ["J", "K", "L"],
            6 => ["M", "N", "O"],
            7 => ["P", "Q", "R", "S"],
            8 => ["T", "U", "V"],
            9 => ["W", "X", "Y", "Z"]
        ];
        $searchingSql = [];

        for ($i = 0; $i < sizeof($searchingElments); $i++) {
            $searchingSql[] = $telephonKeyboard[(int)$searchingElments[$i]];
        }

        $buildSql = "SELECT firstName,lastName,telephoneNumber FROM contacts WHERE ";
        $prefix = "";
        for ($i = 0; $i < sizeof($searchingSql); $i++) {
            if (is_array($searchingSql[$i])) {
                if ($i === 0) {
                    $buildSql .= "(" . $this->setSearchingElement($searchingSql[$i], $prefix) . ")";
                } elseif ($i === sizeof($searchingSql) - 1) {
                    $buildSql .= "AND " . "(" . $this->setSearchingElement($searchingSql[$i], $prefix) . ")";
                } else {
                    $buildSql .= "AND " . "(" . $this->setSearchingElement($searchingSql[$i], $prefix) . ")";
                }
            } else {
                if ($i === 0) {
                    $buildSql .= "TRUE ";
                } else {
                    $buildSql .= "AND " . "TRUE ";
                }
            }
            $prefix .= "_";
        }
        return $buildSql;
    }


    /**
     * Help function for composing exactly one number
     * 
     * @ String $sql
     * 
     */
    private function setSearchingElement($searchingList, $prefix)
    {
        $sql = "";
        for ($i = 0; $i < sizeof($searchingList); $i++) {
            $searchingLetter = "'" . $prefix . $searchingList[$i] . "%'";
            if ($i === 0) { // erstes SQL Element
                $sql .= "(firstName LIKE " . $searchingLetter . " OR lastName LIKE " . $searchingLetter . ") OR ";
            } elseif ($i === sizeof($searchingList) - 1) { // letzes SQL Element
                $sql .= "(firstName LIKE " . $searchingLetter . " OR lastName LIKE " . $searchingLetter . ") ";
            } else {
                $sql .= "(firstName LIKE " . $searchingLetter . " OR lastName LIKE " . $searchingLetter . ") OR ";
            }
        }
        return $sql;
    }

    /**
     * Returns the searched pesons
     * 
     * @return {pdo} $res
     * 
     */
    public function getContacts($searchingNumber)
    {
        $sql = $this->getSearchingSQL($searchingNumber . " LIMIT 1000");
        $res = $this->pdo->query($sql);
        return $res;
    }
}
