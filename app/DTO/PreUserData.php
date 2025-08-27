<?php
namespace App\DTO;

use App\Consts\State;
use App\Enums\Status;

class PreUserData
{
    public function __construct(
        public ?string $id = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $pin = null,
        public ?string $primaryPhone = null,
        public ?array  $phones = null,
        public ?array  $docDetails = null,
        public ?array  $beneficiaries = null,
        public ?string $trace = null,
        public ?int $status = null
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
            status: $data['status'] ?? Status::PENDING->value
        );
    }

    public function toArray(): array
    {
        return array_filter(get_object_vars($this), function ($value) {
            return $value !== null && $value != '' && $value != [];
        });
    }

    public function filteredArray() : array 
    {
        $preUserData = [
            'first_name' => $this->firstname,
            'last_name' => $this->lastname,
            'pin' => $this->pin,
            'primary_phone' => $this->primaryPhone,
            'phones' => json_encode($this->phones),
            'doc_details' => json_encode($this->docDetails),
            'beneficiaries' => json_encode($this->beneficiaries),
            'status' => $this->status
        ];

        $preUserData = array_filter($preUserData, function ($value) {
            return $value !== null && $value !== '' && $value !== '[]';
        });
        
        return $preUserData;
    }
}