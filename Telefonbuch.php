<?php
require_once("backend/Repository.php");

$pdo = new Repository();
$res = false;

if (isset($_POST["save"])) {
    echo "speichen";
    if (strlen($_POST["lastName"]) > 0 && strlen($_POST["firstName"]) > 0 && strlen($_POST["telephoneNumber"]) > 0) {
        $pdo->insertPerson($_POST["firstName"], $_POST["lastName"], $_POST["telephoneNumber"]);
    } else {
        echo "<script>alert('Um eine Person zu speichern müssen Sie alle Eingabefelder ausfüllen)</script>";
    }
}
if (isset($_POST["search"])) {
    echo "suchen";
    $res = $pdo->getContacts($_POST["searchNumber"]);
}

?>

<form action="Telefonbuch.php" method="post">
    <p>Eine Person in das Telefonbuch speichern</p>
    <p>Name: <input type="text" name="lastName" /></p>
    <p>Vorname: <input type="text" name="firstName" /></p>
    <p>Telefonnumer: <input type="number" name="telephoneNumber" /></p>
    <p><input type="submit" value="Speichern" name="save" /></p>
</form>

<form action="Telefonbuch.php" method="post">
    <p>Person suchen</p>
    <p>Nummer: <input type="number" name="searchNumber" /></p>
    <p><input type="submit" value="Suchen" name="search" /></p>
</form>


<table>
    <?php
    if ($res) {
    ?>
        <tr>
            <th>Name</th>
            <th>Vorname</th>
            <th>Nummer</th>
        </tr>
    <?php

        foreach ($res as $vaule) {
            echo "<tr>" . "<td>" . $vaule["lastName"] . "</td>" . "<td>" . $vaule["lastName"] . "</td>" . "<td>" . $vaule["telephoneNumber"] . "</td>" . "</tr>";
        }
    }
    ?>
</table>