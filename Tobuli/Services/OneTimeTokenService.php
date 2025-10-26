<?php

namespace Tobuli\Services;

use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tobuli\Entities\User;

class OneTimeTokenService
{
    private Connection $redis;

    public function __construct(RedisFactory $redisFactory)
    {
        $this->redis = $redisFactory->connection();
    }

    public function create(User $user): ?string
    {
        do {
            $ott = $this->generate();

            $existingUserId = $this->find($ott);
        } while ($existingUserId !== null && $existingUserId !== $user->id);

        $this->redis->set("ott.$ott", $user->id, 'ex', 300);

        return $ott;
    }

    public function login(string $ott): false|User
    {
        $userId = $this->find($ott);

        if ($userId === null) {
            return false;
        }

        $this->delete($ott);

        return Auth::loginUsingId($userId);
    }

    private function find(string $ott): ?int
    {
        return $this->redis->get("ott.$ott");
    }

    private function delete(string $ott): bool
    {
        return (bool)$this->redis->del("ott.$ott");
    }

    private function generate(): string
    {
        return Str::random(32);
    }
}