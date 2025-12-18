<?php
header('Content-Type: application/json');

// Path file tempat data disimpan
$dataFile = 'score_data.json';

// Cek apakah file ada
if (file_exists($dataFile)) {
    // Baca isi file dan kembalikan sebagai JSON
    $json = file_get_contents($dataFile);
    echo $json;
} else {
    // Kembalikan default kosong jika belum ada data
    echo json_encode([
        'redScore' => 0,
        'blueScore' => 0,
        'senshu' => null,
        'player1' => [
            'name' => 'Kumite Player 1',
            'club' => 'ABC',
            'team' => 'Team ABC Info'
        ],
        'player2' => [
            'name' => 'Kumite Player 2',
            'club' => 'DEF',
            'team' => 'Team DEF Info'
        ],
        'penaltiesRed' => [],
        'penaltiesBlue' => []
    ]);
}
?>
