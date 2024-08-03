$(document).ready(function() {
    let web3;
    let userAccount;

    // Connect wallet function
    $('#connectWallet').click(async function() {
        if (typeof window.ethereum !== 'undefined') {
            try {
                // Request account access
                await window.ethereum.request({ method: 'eth_requestAccounts' });
                web3 = new Web3(window.ethereum);
                const accounts = await web3.eth.getAccounts();
                userAccount = accounts[0];
                $(this).text(userAccount.substring(0, 6) + '...' + userAccount.substring(38));
                $(this).prop('disabled', true);
                updateBalance();
            } catch (error) {
                console.error("User denied account access");
            }
        } else {
            console.log('Please install MetaMask!');
        }
    });

    // Swap form submission
    $('#swapForm').submit(function(e) {
        e.preventDefault();
        const fromToken = $('#fromToken').val();
        const toToken = $('#toToken').val();
        const amount = $('#amount').val();
        
        // Here you would typically interact with a smart contract
        console.log(`Swapping ${amount} ${fromToken} to ${toToken}`);
        
        // For demonstration, let's show an alert
        alert(`Swapped ${amount} ${fromToken} to ${toToken}`);
    });

    // Function to update balance (simulated)
    function updateBalance() {
        const tokens = ['ETH', 'DAI', 'USDC'];
        let balanceHtml = '';
        tokens.forEach(async token => {
            let balance;
            if (token === 'ETH') {
                balance = await web3.eth.getBalance(userAccount);
                balance = web3.utils.fromWei(balance, 'ether');
            } else {
                balance = (Math.random() * 10).toFixed(4);
            }
            balanceHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                ${token}
                                <span class="badge bg-primary rounded-pill">${balance}</span>
                            </li>`;
        });
        $('#balanceList').html(balanceHtml);
    }

    // Add smooth scrolling for navigation
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top
        }, 500, 'linear');
    });
});
