import { Module } from '@nestjs/common';
import { AuthGuard } from './guards/jwt-auth.guard';
import { HttpModule } from '@nestjs/axios';

@Module({
  imports: [HttpModule],
  providers: [AuthGuard],
  exports: [],
})
export class AuthModule {}
