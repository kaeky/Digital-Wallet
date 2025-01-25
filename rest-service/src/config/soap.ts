import * as dotenv from 'dotenv';
dotenv.config();

export const soapConfig = {
    soapService: process.env.SOAP_SERVICE,
}