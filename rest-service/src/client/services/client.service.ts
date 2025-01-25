import { Injectable } from '@nestjs/common';
import { CreateClientInput } from '../dto/create-client.input';
import { SoapService } from '../../soap/services/soap.service';
import { CheckBalanceInput } from '../dto/check-balance.input';

@Injectable()
export class ClientService {
  constructor(private soapService: SoapService) {}
  create(createClientDto: CreateClientInput) {
    return this.soapService.sendSoapRequest(
      'createClient',
      'createClientInput',
      createClientDto,
    );
  }

  checkBalance(checkBalanceInput: CheckBalanceInput, token: string) {
    return this.soapService.sendSoapRequest(
      'checkBalance',
      'checkBalanceInput',
      checkBalanceInput,
      token,
    );
  }
}
