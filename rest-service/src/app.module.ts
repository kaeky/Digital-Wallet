import { Module } from '@nestjs/common';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { ClientModule } from './client/client.module';
import { SoapModule } from './soap/soap.module';
import { AuthModule } from './auth/auth.module';
import { WalletModule } from './wallet/wallet.module';
import { PaymentModule } from './payment/payment.module';

@Module({
  imports: [ClientModule, SoapModule, AuthModule, WalletModule, PaymentModule],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
