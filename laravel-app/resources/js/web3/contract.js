// resources/js/web3/contract.js
import PengelolaanZakatABI from '../abi/PengelolaanZakat.json';

export const CONTRACT_DETAILS = {
  // PENTING: Ganti dengan Contract Address hasil deploy terakhir kamu!
  address: "0x5FbDB2315678afecb367f032d93F642f64180aa3",
  abi: PengelolaanZakatABI.abi
};

export const getContract = async (providerOrSigner) => {
  return new ethers.Contract(
    CONTRACT_DETAILS.address,
    CONTRACT_DETAILS.abi,
    providerOrSigner
  );
};