header('Content-Type: application/json');

// Simulated token prices
$prices = [
    'ETH' => 3000,
    'DAI' => 1,
    'USDC' => 1
];

echo json_encode($prices);
?>
