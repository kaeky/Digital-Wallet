import { BadRequestException, Injectable } from '@nestjs/common';
import { HttpService } from '@nestjs/axios';
import { lastValueFrom } from 'rxjs';
import { soapConfig } from '../../config/soap';
import * as xml2js from 'xml2js';

@Injectable()
export class SoapService {
  constructor(private readonly httpService: HttpService) {}

  async sendSoapRequest(
    action: string,
    dtoName: string,
    dto: any,
    token?: string,
  ): Promise<any> {
    const body = this.generateXmlFromDto(dto);
    const soapEnvelope = `
      <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://localhost:8000/soap">
         <soapenv:Header/>
         <soapenv:Body>
            <ser:${action}>
                <${dtoName}>
                  ${body}
                </${dtoName}>
            </ser:${action}>
         </soapenv:Body>
      </soapenv:Envelope>
    `;
    try {
      const response = await lastValueFrom(
        this.httpService.post(soapConfig.soapService, soapEnvelope, {
          headers: {
            'Content-Type': 'text/xml',
            Authorization: token || '',
          },
        }),
      );
      return this.parseSoapResponse(response.data);
    } catch (error) {
      console.log('SOAP request failed:', error);
      throw new BadRequestException(error.message);
    }
  }

  private generateXmlFromDto(dto: any): string {
    return Object.entries(dto)
      .map(([key, value]) => `<${key}>${value}</${key}>`)
      .join('');
  }

  private async parseSoapResponse(xml: string): Promise<any> {
    console.log('SOAP response:', xml);
    const parser = new xml2js.Parser({ explicitArray: false });
    const parsed = await parser.parseStringPromise(xml);

    // Navigate to the relevant fields in the response dynamically
    const body = parsed['SOAP-ENV:Envelope']['SOAP-ENV:Body'];
    const responseKey = Object.keys(body)[0];
    const response = body[responseKey]?.return;

    if (!response) {
      throw new Error('Unexpected SOAP response format');
    }
    console.log('response:', response);
    // Extract specific fields
    const items = Array.isArray(response.data.item)
      ? response.data.item
      : response.data.item
        ? [response.data.item]
        : [];
    let transformedItems = [];

    if (items.length > 0) {
      transformedItems = items.reduce((acc, item) => {
        acc[item.key._] = item.value._;
        return acc;
      }, {});
    }

    return {
      success: response.success._,
      errorCode: response.errorCode._,
      errorMessage: response.errorMessage._,
      data: transformedItems,
    };
  }
}
