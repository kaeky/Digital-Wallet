import { IsNotEmpty, IsString } from 'class-validator';

export class ConfirmPaymentInput {
  @IsNotEmpty()
  @IsString()
  sessionId: string;

  @IsNotEmpty()
  @IsString()
  token: string;
}
