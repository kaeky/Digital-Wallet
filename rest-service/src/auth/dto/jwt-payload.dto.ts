export class jwtPayloadDto {
  public iss: string;

  public sub: string;

  public aud: string[];

  public iat: number;

  public exp: number;

  public azp: string;

  public scope: string;

  public gty: string;
}
