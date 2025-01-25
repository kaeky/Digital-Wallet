import { Module } from '@nestjs/common';
import { ClientService } from './services/client.service';
import { ClientController } from './controllers/client.controller';
import {SoapModule} from "../soap/soap.module";

@Module({
  imports: [SoapModule],
  controllers: [ClientController],
  providers: [ClientService],
})
export class ClientModule {}
