<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\TransferRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TransferRequestTest extends TestCase
{
    /**
     * @return void
     * @test
     * The following checks are made:
     * - The data should be accepted by the validation rules.
     */
    public function testAcceptedData(): void
    {
        /**
         * Arrange - Given
         */
        $request = new TransferRequest();

        $dataSet = [
            [
                'value' => 10,
                'payer' => 15,
                'payee' => 3,
            ],
            [
                'value' => '19.99',
                'payer' => 15,
                'payee' => 3,
            ],
        ];

        foreach ($dataSet as $item) {
            /**
             * Act - When
             */
            $validator = Validator::make($item, $request->rules(), $request->messages());

            /**
             * Assert - Then
             */
            $this->assertFalse($validator->fails(), 'Data not accepted.');
        }
    }

    /**
     * @param string $fieldName
     * @param array<string, mixed> $dataRequest
     * @param string $expectedMessage
     * @return void
     * @test
     * @dataProvider valueNotAccepted
     * @dataProvider payerNotAccepted
     * @dataProvider payeeNotAccepted
     * The following checks are made:
     * - The data should not be accepted by the validation rules.
     * - The error message should match the expected message.
     */
    public function testDataNotAccepted(string $fieldName, array $dataRequest, string $expectedMessage): void
    {
        /**
         * Arrange - Given
         */
        $transferRequest = new TransferRequest();
        $validator = Validator::make($dataRequest, $transferRequest->rules(), $transferRequest->messages());

        /**
         * Act - When
         */
        $validated = $validator->fails();
        $firstMessage = $validator->messages()->get($fieldName)[0];

        /**
         * Assert - Then
         */
        $this->assertTrue($validated, "The data should not be accepted - '{$fieldName}'");
        $this->assertSame($firstMessage, $expectedMessage, "Wrong error message for '{$fieldName}'");
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function valueNotAccepted(): array
    {
        $value1 = [
            'value' => '',
            'payer' => 15,
            'payee' => 3,
        ];

        $value2 = [
            'value' => 'invalid',
            'payer' => 15,
            'payee' => 3,
        ];

        $value3 = [
            'payer' => 15,
            'payee' => 3,
        ];

        return [
            "TransferRequest - value = ''" => ['value', $value1, 'É necessário informar o valor da transferência!'],
            'TransferRequest - value = invalid format' => ['value', $value2, 'O valor da transferência deve ser um número válido!'],
            'TransferRequest - value not informed' => ['value', $value3, 'É necessário informar o valor da transferência!'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function payerNotAccepted(): array
    {
        $payer1 = [
            'value' => 10,
            'payer' => '',
            'payee' => 15,
        ];

        $payer2 = [
            'value' => 10,
            'payer' => 999999,
            'payee' => 15,
        ];

        $payer3 = [
            'value' => 10,
            'payer' => 3,
            'payee' => 15,
        ];

        $payer4 = [
            'value' => 10,
            'payee' => 15,
        ];

        return [
            "TransferRequest - payer = ''" => ['payer', $payer1, 'É necessário informar a conta que fará à transferência!'],
            'TransferRequest - payer not exists' => ['payer', $payer2, 'A conta de origem informada não existe!'],
            'TransferRequest - payer is juristic account' => ['payer', $payer3, 'A conta de origem deve ser do tipo física!'],
            'TransferRequest - payer not informed' => ['payer', $payer4, 'É necessário informar a conta que fará à transferência!'],
        ];
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public function payeeNotAccepted(): array
    {
        $payee1 = [
            'value' => 10,
            'payer' => 15,
            'payee' => '',
        ];

        $payee2 = [
            'value' => 10,
            'payer' => 15,
            'payee' => 'invalid',
        ];

        $payee3 = [
            'value' => 10,
            'payer' => 15,
        ];

        return [
            "TransferRequest - payee = ''" => ['payee', $payee1, 'É necessário informar a conta de destino da transferência!'],
            'TransferRequest - payee = invalid format' => ['payee', $payee2, 'A conta de destino está inválida!'],
            'TransferRequest - payee not informed' => ['payee', $payee3, 'É necessário informar a conta de destino da transferência!'],
        ];
    }
}

