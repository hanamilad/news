<?php

namespace App\GraphQL\Mutations;

use App\Services\Client\ClientService;

class ClientMutator
{
    public function __construct(protected ClientService $service) {}

    public function requestOtp($_, array $args)
    {
        $phone = $args['phone'] ?? null;
        $validator = validator(['phone' => $phone], [
            'phone' => ['required', 'phone:EG,JO,SA,AE']
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        return $this->service->requestOtp($phone);
    }

    public function verifyOtp($_, array $args)
    {
        $phone = $args['phone'] ?? null;
        $otp = $args['otp'] ?? null;
        $validator = validator(['phone' => $phone, 'otp' => $otp], [
            'phone' => ['required', 'phone:EG,JO,SA,AE'],
            'otp' => ['required', 'string', 'size:6']
        ]);
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        return $this->service->verifyOtp($phone, $otp);
    }
}
