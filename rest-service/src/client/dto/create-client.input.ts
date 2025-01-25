import {IsEmail, IsString} from "class-validator";

export class CreateClientInput {
    @IsString()
    public document: string;
    @IsString()
    public names: string;
    @IsEmail()
    public email: string;
    @IsString()
    public cellphone: string
    @IsString()
    public password: string;
}
