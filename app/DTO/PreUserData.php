<?php
namespace App\DTO;

use App\Consts\State;
use App\Enums\Status;

class PreUserData
{
    public function __construct(
        public string $id,
        public string $firstname,
        public string $lastname,
        public string $pin,
        public string $primaryPhone,
        public array  $phones,
        public array  $docDetails,
        public array  $beneficiaries,
        public string $trace,
        public int $status
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            firstname: $data['firstname'] ?? '',
            lastname: $data['lastname'] ?? '',
            pin: $data['pin'] ?? '',
            primaryPhone: $data['primaryphone'] ?? '',
            phones: $data['phones'] ?? [],
            docDetails: $data['docdetails'] ?? [],
            beneficiaries: $data['beneficiaries'] ?? [],
            trace: $data['traceid'] ?? '',
            status: $data['status'] ?? Status::P_USER_PENDING
        );
    }
}