import { Module } from '@nestjs/common';
import { WalletService } from './services/wallet.service';
import { WalletController } from './controllers/wallet.controller';
import { SoapModule } from '../soap/soap.module';

@Module({
  imports: [SoapModule],
  controllers: [WalletController],
  providers: [WalletService],
})
export class WalletModule {}
