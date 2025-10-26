<?php

namespace Tobuli\Helpers;

use Illuminate\Support\Facades\Validator;
use Tobuli\Entities\User;

class UserHomeViewService
{
    public function __construct(
        private User $user
    ) {}

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function validate(array $data): array
    {
        return Validator::validate($data, [
            'zoom' => 'required|integer',
            'lat' => 'required|lat',
            'lon' => 'required|lng',
        ]);
    }

    public function set(array $data): void
    {
        $homeView = $this->validate($data);

        $this->user->setSettings('home_view', [
            'zoom' => $homeView['zoom'],
            'center' => [
                'lat' => $homeView['lat'],
                'lon' => $homeView['lon'],
            ],
        ]);
    }

    public function get(): ?array
    {
        return $this->user->getSettings('home_view');
    }
}