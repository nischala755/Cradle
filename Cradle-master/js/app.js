$(document).ready(function () {
    let web3;
    let userAccount;

    // Dark/Light Mode Toggle
    const themeToggle = $('#themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';

    // Apply the saved theme on load
    if (currentTheme === 'dark') {
        $('body').addClass('dark-mode');
        themeToggle.prop('checked', true);
    }

    // Toggle theme and save preference
    themeToggle.on('change', function () {
        $('body').toggleClass('dark-mode');
        const theme = $('body').hasClass('dark-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
    });

    // Connect wallet function
    $('#connectWallet').click(async function () {
        if (typeof window.ethereum !== 'undefined') {
            try {
                // Request account access
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                web3 = new Web3(window.ethereum);
                const accounts = await web3.eth.getAccounts();
                userAccount = accounts[0];

                // Display shortened account address
                $(this).text(userAccount.substring(0, 6) + '...' + userAccount.substring(38));
                $(this).prop('disabled', true);

                // Fetch and display balance
                const balance = await web3.eth.getBalance(userAccount);
                const ethBalance = web3.utils.fromWei(balance, 'ether');
                alert(`Connected with ${ethBalance} ETH`);
                updateBalance();
            } catch (error) {
                console.error("User denied account access");
            }
        } else {
            alert('Please install MetaMask!');
        }
    });

    // Swap form submission
    $('#swapForm').submit(function (e) {
        e.preventDefault();
        const fromToken = $('#fromToken').val();
        const toToken = $('#toToken').val();
        const amount = $('#amount').val();

        // Simulated token swap
        $.post('php/api.php', { action: 'swap', fromToken: fromToken, toToken: toToken, amount: amount }, function (response) {
            if (response.success) {
                alert(`Swapped ${amount} ${fromToken} to ${toToken}`);
                updateBalance(); // Refresh the balance
                updateBlockchainViewer(); // Refresh the blockchain viewer
            } else {
                alert('Swap failed: ' + response.message);
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            alert('Swap request failed: ' + textStatus);
        });
    });

    // Mining function
    $('#mineButton').click(function () {
        $.post('php/api.php', { action: 'mine' }, function (response) {
            if (response.success) {
                const rewardInEth = web3.utils.fromWei(response.reward, 'ether'); // Convert to ETH if needed
                alert(`Mining successful! You earned ${rewardInEth} ETH.`);
                updateBalance(); // Refresh the balance
                updateBlockchainViewer(); // Refresh the blockchain viewer
            } else {
                alert('Mining failed: ' + response.message);
            }
        }, 'json').fail(function (jqXHR, textStatus, errorThrown) {
            alert('Mining request failed: ' + textStatus);
        });
    });

    // Function to update balances for ETH, DAI, USDC
    function updateBalance() {
        const tokens = ['ETH', 'DAI', 'USDC'];
        $('#balanceList').empty(); // Clear current balances

        tokens.forEach(token => {
            $.post('php/api.php', { action: 'get_balance', token: token }, function (response) {
                if (response.success) {
                    const balanceHtml = `<li class="list-group-item d-flex justify-content-between align-items-center">
                                            ${token}
                                            <span class="badge bg-primary rounded-pill">${response.balance}</span>
                                        </li>`;
                    $('#balanceList').append(balanceHtml);
                }
            }, 'json');
        });
    }

    // Function to update the transaction history
    function updateBlockchainViewer() {
        $.post('php/api.php', { action: 'get_transaction_history' }, function (response) {
            if (response.success) {
                $('#transactionHistory').empty(); // Clear current transactions
                response.transactions.forEach(tx => {
                    const txHtml = `<li class="list-group-item">${tx.timestamp}: ${tx.type} ${tx.amount} ${tx.token}</li>`;
                    $('#transactionHistory').append(txHtml);
                });
            }
        }, 'json');
    }

    // Initial load
    updateBalance();
    updateBlockchainViewer();
});
