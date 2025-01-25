import { Body, Controller, Post, UseGuards } from '@nestjs/common';
import { PaymentService } from '../services/payment.service';
import { PaymentInput } from '../dto/payment.input';
import { AuthGuard } from '../../auth/guards/jwt-auth.guard';
import { CurrentToken } from '../../auth/decorators/current-token.decorator';
import { ConfirmPaymentInput } from '../dto/confirmPayment.input';

@Controller('payment')
@UseGuards(AuthGuard)
export class PaymentController {
  constructor(private readonly paymentService: PaymentService) {}

  @Post()
  pay(@Body() paymentInput: PaymentInput, @CurrentToken() token: string) {
    return this.paymentService.pay(paymentInput, token);
  }

  @Post('confirm')
  confirmPayment(
    @Body() confirmPaymentInput: ConfirmPaymentInput,
    @CurrentToken() token: string,
  ) {
    return this.paymentService.confirmPayment(confirmPaymentInput, token);
  }
}
