const TokenSwap = artifacts.require("TokenSwap");

module.exports = function(deployer) {
    // Chainlink ETH/USD price feed address on Ethereum mainnet
    const priceFeedAddress = "0x5f4ec3df9cbd43714fe2740f5e3616155c5b8419"; // Replace with the correct address for your network
    deployer.deploy(TokenSwap, priceFeedAddress);
};
