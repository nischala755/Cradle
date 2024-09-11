<?php
header('Content-Type: application/json');

// Simulate user data for demo purposes
$balances = [
    'ETH' => 10.5,
    'DAI' => 120.0,
    'USDC' => 75.5,
];

$transactions = [
    ['timestamp' => '2024-09-01 12:00', 'type' => 'swap', 'amount' => '100', 'token' => 'DAI'],
    ['timestamp' => '2024-09-02 14:30', 'type' => 'mine', 'amount' => '0.01', 'token' => 'ETH'],
];

$action = $_POST['action'] ?? '';

// Function to handle token swaps
function handleSwap($fromToken, $toToken, $amount) {
    global $balances;
    
    if ($balances[$fromToken] >= $amount) {
        $balances[$fromToken] -= $amount;
        $balances[$toToken] += $amount; // Simple swap logic
        return ['success' => true, 'message' => 'Swap successful'];
    } else {
        return ['success' => false, 'message' => 'Insufficient balance'];
    }
}

// Function to handle mining
function handleMine() {
    global $balances, $transactions;
    
    $miningReward = 0.01; // Simulate a small reward for mining
    $balances['ETH'] += $miningReward;
    
    // Add transaction to history
    $transactions[] = ['timestamp' => date('Y-m-d H:i'), 'type' => 'mine', 'amount' => $miningReward, 'token' => 'ETH'];
    
    return ['success' => true, 'reward' => $miningReward];
}

// Function to get token balance
function getBalance($token) {
    global $balances;
    return ['success' => true, 'balance' => $balances[$token]];
}

// Function to get transaction history
function getTransactionHistory() {
    global $transactions;
    return ['success' => true, 'transactions' => $transactions];
}

// Handle API actions
switch ($action) {
    case 'swap':
        $fromToken = $_POST['fromToken'] ?? '';
        $toToken = $_POST['toToken'] ?? '';
        $amount = (float)($_POST['amount'] ?? 0);
        echo json_encode(handleSwap($fromToken, $toToken, $amount));
        break;
        
    case 'mine':
        echo json_encode(handleMine());
        break;

    case 'get_balance':
        $token = $_POST['token'] ?? '';
        echo json_encode(getBalance($token));
        break;

    case 'get_transaction_history':
        echo json_encode(getTransactionHistory());
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
