import { createParamDecorator } from '@nestjs/common';
import { ExecutionContextHost } from '@nestjs/core/helpers/execution-context-host';

export const CurrentToken = createParamDecorator<any, any, any>(
  (data, req: ExecutionContextHost) => {
    const handler = req.switchToHttp();
    const request = handler.getRequest();
    return request.token;
  },
);
