import { IsNotEmpty, IsNumber, IsString } from 'class-validator';
export class PaymentInput {
  @IsNotEmpty()
  @IsString()
  public document: string;

  @IsNotEmpty()
  @IsString()
  public cellphone: string;

  @IsNotEmpty()
  @IsNumber()
  public amount: number;
}
