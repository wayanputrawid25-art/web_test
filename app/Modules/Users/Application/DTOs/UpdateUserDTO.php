<?php

namespace App\Modules\Users\Application\DTOs;

class UpdateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly string $status = 'active',
        public readonly array $roles = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            status: $data['status'] ?? 'active',
            roles: $data['roles'] ?? [],
        );
    }

    public function toArray(): array
    {
        $result = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
        ];

        if ($this->password) {
            $result['password'] = $this->password;
        }

        return $result;
    }
}