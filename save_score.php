<?php
// Include the database connection
require_once 'db.php';

// Read the JSON data sent via POST
$data = json_decode(file_get_contents("php://input"));

// Prepare the SQL query to insert the match details into the database
$sql = "INSERT INTO match_scores (player1_name, player1_club, player1_team, player2_name, player2_club, player2_team, red_score, blue_score, penalties_red, penalties_blue, senshu)
        VALUES (:player1_name, :player1_club, :player1_team, :player2_name, :player2_club, :player2_team, :red_score, :blue_score, :penalties_red, :penalties_blue, :senshu)";

$db = new Database();
$db->query($sql);
$db->bind(':player1_name', $data->player1->name);
$db->bind(':player1_club', $data->player1->club);
$db->bind(':player1_team', $data->player1->team);
$db->bind(':player2_name', $data->player2->name);
$db->bind(':player2_club', $data->player2->club);
$db->bind(':player2_team', $data->player2->team);
$db->bind(':red_score', $data->redScore);
$db->bind(':blue_score', $data->blueScore);
$db->bind(':penalties_red', json_encode($data->penaltiesRed)); // Store penalties as JSON string
$db->bind(':penalties_blue', json_encode($data->penaltiesBlue)); // Store penalties as JSON string
$db->bind(':senshu', $data->senshu);

// Execute the query
try {
    $db->execute();
    echo json_encode(['message' => 'Match data saved successfully!']);
} catch (PDOException $e) {
    echo json_encode(['message' => 'Error saving match data: ' . $e->getMessage()]);
}

?>
