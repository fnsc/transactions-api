<?php

namespace User;

use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Transaction\Application\Exceptions\UserException;

class UserExceptionTest extends TestCase
{
    public function test_should_throw_exception_when_failed_storing(): void
    {
        // Actions
        $exception = UserException::failedStoring();

        // Assertions
        $this->assertSame('The new user cannot be stored.', $exception->getMessage());
        $this->assertSame(Response::HTTP_SERVICE_UNAVAILABLE, $exception->getCode());
    }

    public function test_should_throw_exception_when_email_already_exists(): void
    {
        // Actions
        $exception = UserException::emailAlreadyExists();

        // Assertions
        $this->assertSame('The email has already been taken.', $exception->getMessage());
        $this->assertSame(Response::HTTP_CONFLICT, $exception->getCode());
    }

    public function test_should_throw_exception_when_fiscal_doc_already_exists(): void
    {
        // Actions
        $exception = UserException::fiscalDocAlreadyExists();

        // Assertions
        $this->assertSame('The fiscal doc has already been taken.', $exception->getMessage());
        $this->assertSame(Response::HTTP_CONFLICT, $exception->getCode());
    }
}
