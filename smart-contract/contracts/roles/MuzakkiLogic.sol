// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

import "../base/ZakatBase.sol";

abstract contract MuzakkiLogic is ZakatBase {
    function bayarZIS(string memory _tipeDana) public payable saatAktif {
        require(msg.value > 0, "Nominal tidak boleh nol");
        emit DepositZIS(msg.sender, msg.value, _tipeDana, block.timestamp);
    }
}
