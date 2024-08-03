// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

import "@chainlink/contracts/src/v0.8/interfaces/AggregatorV3Interface.sol";

interface IERC20 {
    function transfer(address recipient, uint256 amount) external returns (bool);
    function transferFrom(address sender, address recipient, uint256 amount) external returns (bool);
    function balanceOf(address account) external view returns (uint256);
}

contract TokenSwap {
    address public owner;
    AggregatorV3Interface internal priceFeed;

    constructor(address _priceFeed) {
        owner = msg.sender;
        priceFeed = AggregatorV3Interface(_priceFeed);
    }

    function swapTokens(address token1, address token2, uint256 amount) public {
        require(IERC20(token1).balanceOf(msg.sender) >= amount, "Insufficient balance");
        require(IERC20(token1).transferFrom(msg.sender, address(this), amount), "Transfer failed");
        uint256 price = getLatestPrice();
        uint256 amountToReceive = amount * price; // Assuming price is in appropriate units
        require(IERC20(token2).transfer(msg.sender, amountToReceive), "Swap failed");
    }

    function getLatestPrice() public view returns (uint256) {
        (
            uint80 roundID,
            int price,
            uint startedAt,
            uint timeStamp,
            uint80 answeredInRound
        ) = priceFeed.latestRoundData();
        return uint256(price);
    }

    function withdraw(address token, uint256 amount) public {
        require(msg.sender == owner, "Only owner can withdraw");
        IERC20(token).transfer(owner, amount);
    }
}
