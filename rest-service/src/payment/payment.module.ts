import { Module } from '@nestjs/common';
import { PaymentService } from './services/payment.service';
import { PaymentController } from './controllers/payment.controller';
import { SoapModule } from '../soap/soap.module';

@Module({
  imports: [SoapModule],
  controllers: [PaymentController],
  providers: [PaymentService],
})
export class PaymentModule {}
