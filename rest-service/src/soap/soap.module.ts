import { Module } from '@nestjs/common';
import { SoapService } from './services/soap.service';
import {HttpModule} from "@nestjs/axios";


@Module({
  imports: [HttpModule],
  controllers: [],
  providers: [SoapService],
  exports: [SoapService]
})
export class SoapModule {}
