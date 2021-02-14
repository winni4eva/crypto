<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\InvalidCredentialsException;
use NotificationChannels\Twilio\Exceptions\CouldNotSendNotification;
use Twilio\Exceptions\RestException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (InvalidCredentialsException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        });

        $this->renderable(function(CouldNotSendNotification $e) {
          return response()->json(['error' => $e->getMessage()], 403);
        });

        $this->renderable(function(RestException $e) {
          return response()->json(['error' => $e->getMessage()], 403);
        });
    }
}
