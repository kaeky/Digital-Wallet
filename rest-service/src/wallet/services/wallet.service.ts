import { Injectable } from '@nestjs/common';
import { SoapService } from '../../soap/services/soap.service';
import { RechargeWalletInput } from '../dto/recharge-wallet.input';

@Injectable()
export class WalletService {
  constructor(private soapService: SoapService) {}

  rechargeWallet(rechargeWalletInput: RechargeWalletInput, token: string) {
    return this.soapService.sendSoapRequest(
      'rechargeWallet',
      'rechargeWalletInput',
      rechargeWalletInput,
      token,
    );
  }
}
