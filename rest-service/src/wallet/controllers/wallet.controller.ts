import { Body, Controller, Post, UseGuards } from '@nestjs/common';
import { WalletService } from '../services/wallet.service';
import { RechargeWalletInput } from '../dto/recharge-wallet.input';
import { CurrentToken } from '../../auth/decorators/current-token.decorator';
import { AuthGuard } from '../../auth/guards/jwt-auth.guard';

@Controller('wallet')
@UseGuards(AuthGuard)
export class WalletController {
  constructor(private readonly walletService: WalletService) {}

  @Post()
  rechargeWallet(
    @Body() rechargeWalletInput: RechargeWalletInput,
    @CurrentToken() token: string,
  ) {
    return this.walletService.rechargeWallet(rechargeWalletInput, token);
  }
}
