// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract PenyaluranLogic is ZakatBase {
    function getSaldoBrankas() public view returns (uint256) {
        return address(this).balance;
    }
}
