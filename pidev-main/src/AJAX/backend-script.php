<?php

include("Pidev_SwapNShare.php");
$db = $conn;
// fetch query
function fetch_data()
{
    global $db;
    $query = "SELECT u.nom, u.prenom, u.telephone, u.email, r.titreR ,r.descriptionR, r.date , r.urgence
   from reclamation r, utilisateur u
   JOIN r.id_utilisateur=u.id_utilisateur
  ORDER BY urgence DESC";
    $exec = mysqli_query($db, $query);
    if (mysqli_num_rows($exec) > 0) {
        $row = mysqli_fetch_all($exec, MYSQLI_ASSOC);
        return $row;
    } else {
        return $row = [];
    }
}
$fetchData = fetch_data();
show_data($fetchData);

function show_data($fetchData)
{
    echo '<table border="1">
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Reason of Complaint</th>
            <th>Description</th>
            <th>Date</th>
            <th>urgence</th>
            <th>Actions</th>
        </tr>';

    if (count($fetchData) > 0) {
        $sn = 1;
        foreach ($fetchData as $data) {

            echo "<tr>
          <td>" . $sn . "</td>
          <td>" . $data['nom'] . "</td>
          <td>" . $data['prenom'] . "</td>
          <td>" . $data['telephone'] . "</td>
          <td>" . $data['email'] . "</td>
          <td>" . $data['titreR'] . "</td>
          <td>" . $data['descriptionR'] . "</td>
          <td>" . $data['date'] . "</td>
          <td>" . $data['urgence'] . "</td>
          <td><a href='crud-form.php?edit=" . $data['id'] . "'>Edit</a>
          <a href='crud-form.php?delete=" . $data['id'] . "'>Delete</a></td>
   </tr>";

            $sn++;
        }
    } else {

        echo "<tr>
        <td colspan='7'>No Data Found</td>
       </tr>";
    }
    echo "</table>";
}
