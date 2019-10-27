<?php


namespace App\Middleware;


use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class CommonErrorMiddleware
{
    public function __construct($self)
    {
        // Define Custom Error Handler
        $customErrorHandler = function (
            ServerRequestInterface $request,
            Throwable $exception,
            bool $displayErrorDetails,
            bool $logErrors,
            bool $logErrorDetails
        ) use ($self) {
            $payload = ['error' => $exception->getMessage()];

            $response = $self->app->getResponseFactory()->createResponse();
            $response->getBody()->write(
                json_encode($payload, JSON_UNESCAPED_UNICODE)
            );

            return $self->response($response);
        };

        /*
         *
         * @param bool $displayErrorDetails -> Should be set to false in production
         * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
         * @param bool $logErrorDetails -> Display error details in error log
         * which can be replaced by a callable of your choice.
         *
         * Note: This middleware should be added last. It will not handle any exceptions/errors
         * for middleware added after it.
         */
        $errorMiddleware = $self->app->addErrorMiddleware(true, true, true);
        $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
    }

    public function run()
    {
        // do nothing, 只是為了讓呼叫使用的語法符合規範
    }
}