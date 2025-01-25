import { Injectable } from '@nestjs/common';
import { SoapService } from '../../soap/services/soap.service';
import { PaymentInput } from '../dto/payment.input';
import { ConfirmPaymentInput } from '../dto/confirmPayment.input';

@Injectable()
export class PaymentService {
  constructor(private soapService: SoapService) {}
  pay(paymentInput: PaymentInput, token: string) {
    return this.soapService.sendSoapRequest(
      'pay',
      'PaymentInput',
      paymentInput,
      token,
    );
  }

  confirmPayment(confirmPaymentInput: ConfirmPaymentInput, token: string) {
    return this.soapService.sendSoapRequest(
      'confirmPayment',
      'confirmPaymentInput',
      confirmPaymentInput,
      token,
    );
  }
}
