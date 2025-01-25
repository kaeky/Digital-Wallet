import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  UseGuards,
} from '@nestjs/common';
import { ClientService } from '../services/client.service';
import { CreateClientInput } from '../dto/create-client.input';
import { CheckBalanceInput } from '../dto/check-balance.input';
import { AuthGuard } from '../../auth/guards/jwt-auth.guard';
import { CurrentToken } from '../../auth/decorators/current-token.decorator';
import { Public } from '../../auth/decorators/public.decorator';

@Controller('client')
@UseGuards(AuthGuard)
export class ClientController {
  constructor(private readonly clientService: ClientService) {}

  @Post()
  @Public()
  createClient(@Body() createClientDto: CreateClientInput) {
    return this.clientService.create(createClientDto);
  }

  @Post('balance')
  checkBalance(
    @Body() checkBalanceInput: CheckBalanceInput,
    @CurrentToken() token: string,
  ) {
    return this.clientService.checkBalance(checkBalanceInput, token);
  }
}
