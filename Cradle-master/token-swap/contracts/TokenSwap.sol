// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

import "@chainlink/contracts/src/v0.8/interfaces/AggregatorV3Interface.sol";

// Interface for ERC20 token functions used in the swap
interface IERC20 {
    function transfer(address recipient, uint256 amount) external returns (bool);
    function transferFrom(address sender, address recipient, uint256 amount) external returns (bool);
    function balanceOf(address account) external view returns (uint256);
}

contract TokenSwap {
    // Owner of the contract
    address public owner;

    // Chainlink Price Feed
    AggregatorV3Interface internal priceFeed;

    // Constructor that sets the owner and the Chainlink price feed address
    constructor(address _priceFeed) {
        owner = msg.sender;
        priceFeed = AggregatorV3Interface(_priceFeed);
    }

    // Function to swap tokens
    function swapTokens(address token1, address token2, uint256 amount) public {
        // Ensure the sender has sufficient balance of token1
        require(IERC20(token1).balanceOf(msg.sender) >= amount, "Insufficient balance");

        // Transfer token1 from the sender to this contract
        require(IERC20(token1).transferFrom(msg.sender, address(this), amount), "Transfer failed");

        // Get the latest price from Chainlink
        uint256 price = getLatestPrice();

        // Calculate the amount of token2 to be received
        uint256 amountToReceive = amount * price; // Adjust calculation based on token decimals

        // Transfer token2 from the contract to the sender
        require(IERC20(token2).transfer(msg.sender, amountToReceive), "Swap failed");
    }

    // Function to fetch the latest price from Chainlink price feed
    function getLatestPrice() public view returns (uint256) {
        (
            uint80 roundID,
            int price,
            uint startedAt,
            uint timeStamp,
            uint80 answeredInRound
        ) = priceFeed.latestRoundData();
        return uint256(price); // Convert price from int to uint256
    }

    // Function to allow the owner to withdraw tokens
    function withdraw(address token, uint256 amount) public {
        // Only the owner can call this function
        require(msg.sender == owner, "Only owner can withdraw");

        // Transfer the specified amount of tokens to the owner
        IERC20(token).transfer(owner, amount);
    }
}
