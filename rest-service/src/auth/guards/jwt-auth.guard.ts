import {
  CanActivate,
  ExecutionContext,
  Injectable,
  UnauthorizedException,
} from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import * as jwt from 'jsonwebtoken';
import { jwtPayloadDto } from '../dto/jwt-payload.dto';

@Injectable()
export class AuthGuard implements CanActivate {
  constructor(private readonly reflector: Reflector) {}

  public async canActivate(context: ExecutionContext): Promise<boolean> {
    const isPublic = this.reflector.get<boolean>(
      'isPublic',
      context.getHandler(),
    );
    if (isPublic) return true;

    const request = context.switchToHttp().getRequest();
    const authHeader = request.headers['authorization'];

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      throw new UnauthorizedException('No token provided or invalid format');
    }

    const token = authHeader.split(' ')[1];
    const decodedToken: jwtPayloadDto = jwt.decode(token) as jwtPayloadDto;
    if (!decodedToken) {
      throw new UnauthorizedException('Invalid Token');
    }
    request.token = authHeader;
    return true;
  }
}
