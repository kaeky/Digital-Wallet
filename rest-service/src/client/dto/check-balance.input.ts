import { IsNotEmpty, IsString } from 'class-validator';

export class CheckBalanceInput {
  @IsNotEmpty()
  @IsString()
  public document: string;

  @IsNotEmpty()
  @IsString()
  public cellphone: string;
}
