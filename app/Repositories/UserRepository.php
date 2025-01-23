<?php

namespace App\Repositories;

use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class UserRepository
{
    /**
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        $user = new User();

        $user->name = $data['name'];

        $user->save();

        return $user;
    }

    /**
     * @param User $user
     * @param int $points
     * @return User
     */
    public function addPoints(User $user, int $points): User
    {
        $user->score += $points;

        $scoreData = $this->getUserScoreData($user->id);
        if ($scoreData === null) {
            $default = [];
            for ($i = 1; $i < 31; $i++) {
                $default[$i] = 0;
            }

            $day           = Carbon::now()->day;
            $default[$day] += $points;
            $this->storeUserScoreData($user->id, $default);
        } else {
            $scoreDataArr = json_decode($scoreData->scores_data, true);
            $day          = Carbon::now()->day;

            $scoreDataArr[$day] += $points;

            $this->updateUserScoreData($user->id, $scoreDataArr);
        }

        $user->save();

        return $user;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllUsersScoreData()
    {
        return DB::table('user_scores_by_day')->select('user_id', 'scores_data')->get();
    }

    /**
     * @param int $userId
     * @return \stdClass
     */
    public function getUserScoreData(int $userId)
    {
        return DB::table('user_scores_by_day')->select('scores_data')->where(['user_id' => $userId])->get()->first();
    }

    /**
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function storeUserScoreData(int $userId, array $data): void
    {
        DB::table('user_scores_by_day')->insert(['user_id' => $userId, 'scores_data' => json_encode($data)]);
    }

    /**
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function updateUserScoreData(int $userId, array $data): void
    {
        DB::table('user_scores_by_day')->where('user_id', '=', $userId)->update(['scores_data' => json_encode($data)]);
    }

    /**
     * @param array $ids
     * @return Collection
     */
    public function getUsersByIds(array $ids): Collection
    {
        return User::query()->whereIn('id', $ids)->get();
    }
}
