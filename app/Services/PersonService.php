<?php

namespace App\Services;

use App\Models\Person;

class PersonService
{
    /**
     * Mendapatkan atau membuat Person individu.
     */
    public function findOrCreate(array $data): Person
    {
        if (!empty($data['email'])) {
            $person = Person::where('email', $data['email'])->first();
            if ($person) {
                $person->update(array_filter([
                    'name'  => $data['name'] ?? $person->name,
                    'phone' => $data['phone'] ?? $person->phone,
                ]));
                return $person;
            }
        }

        return Person::create([
            'name'  => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);
    }
}
