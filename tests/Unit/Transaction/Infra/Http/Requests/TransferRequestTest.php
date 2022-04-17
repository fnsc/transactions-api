<?php

namespace Tests\Unit\Transaction\Infra\Http\Requests;

use PHPUnit\Framework\TestCase;
use Transaction\Infra\Http\Requests\TransferRequest;
use function app;

class TransferRequestTest extends TestCase
{
    public function test_should_return_an_array_with_rules(): void
    {
        // Set
        $request = app(TransferRequest::class);
        $expected = [
            'payee_id' => 'required|integer|exists:users,id',
            'payer_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric',
        ];

        // Actions
        $result = $request->rules();

        // Assertions
        $this->assertSame($expected, $result);
    }

    public function test_should_return_if_the_request_is_authorized(): void
    {
        // Set
        $request = app(TransferRequest::class);

        // Actions
        $result = $request->authorize();

        // Assertions
        $this->assertTrue($result);
    }
}
