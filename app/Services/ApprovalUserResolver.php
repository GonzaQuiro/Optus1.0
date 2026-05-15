<?php

namespace App\Services;

use App\Models\User;

class ApprovalUserResolver
{
    public static function findByRoleArea($customerCompanyId, $role, $area = null)
    {
        if (!$customerCompanyId || !$role) {
            return null;
        }

        $requested = self::requestedRole($role, $area);
        $bestUser = null;
        $bestScore = 0;

        $users = User::where('customer_company_id', $customerCompanyId)
            ->whereNull('deleted_at')
            ->get();

        foreach ($users as $user) {
            $score = self::scoreUser($user, $requested);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestUser = $user;
            }
        }

        return $bestUser;
    }

    public static function findByApproval($customerCompanyId, $approval)
    {
        if (!$approval) {
            return null;
        }

        return self::findByRoleArea($customerCompanyId, $approval->role, $approval->area);
    }

    public static function assignApprovalUser($approval, $customerCompanyId)
    {
        if (!$approval || $approval->user_id) {
            return null;
        }

        $user = self::findByApproval($customerCompanyId, $approval);

        if (!$user) {
            return null;
        }

        $approval->user_id = $user->id;
        $approval->user_name = self::formatUserName($user);
        $approval->save();

        return $user;
    }

    public static function syncPendingApprovalUser($approval, $customerCompanyId)
    {
        if (!$approval || $approval->status !== 'pending') {
            return null;
        }

        $user = self::findByApproval($customerCompanyId, $approval);

        if (!$user) {
            if ($approval->user_id || $approval->user_name) {
                $approval->user_id = null;
                $approval->user_name = null;
                $approval->save();
            }

            return null;
        }

        if ((int) $approval->user_id !== (int) $user->id || $approval->user_name !== self::formatUserName($user)) {
            $approval->user_id = $user->id;
            $approval->user_name = self::formatUserName($user);
            $approval->save();
        }

        return $user;
    }

    public static function userMatchesApproval($approval, $user)
    {
        if (!$approval || !$user) {
            return false;
        }

        $resolvedUser = self::findByApproval($user->customer_company_id, $approval);

        return $resolvedUser && (int) $resolvedUser->id === (int) $user->id;
    }

    public static function formatUserName($user)
    {
        if (!$user) {
            return null;
        }

        $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));

        return $name !== '' ? $name : $user->username;
    }

    private static function requestedRole($role, $area = null)
    {
        $roleNorm = self::normalize($role);
        $areaNorm = self::normalize($area);
        $baseRole = self::roleBase($roleNorm);
        $fullRole = $roleNorm;

        if ($areaNorm && strpos($roleNorm, ' de ') === false && $baseRole !== 'gerente general') {
            $fullRole = $baseRole . ' de ' . $areaNorm;
        }

        return [
            'role' => $roleNorm,
            'base_role' => $baseRole,
            'area' => $areaNorm,
            'full_role' => $fullRole,
        ];
    }

    private static function scoreUser($user, array $requested)
    {
        $userRole = self::normalize($user->rol);
        $userArea = self::normalize($user->area);

        if ($userRole === '') {
            return 0;
        }

        $userBaseRole = self::roleBase($userRole);

        if (!$requested['area']) {
            if ($userRole === $requested['role'] || $userBaseRole === $requested['base_role']) {
                return 100;
            }

            return 0;
        }

        if ($userArea === $requested['area'] && $userRole === $requested['full_role']) {
            return 120;
        }

        if ($userArea === $requested['area'] && $userRole === $requested['role']) {
            return 115;
        }

        if ($userArea === $requested['area'] && $userBaseRole === $requested['base_role']) {
            return 100;
        }

        return 0;
    }

    private static function roleBase($role)
    {
        $role = self::normalize($role);

        if ($role === 'gerente general') {
            return 'gerente general';
        }

        if (strpos($role, 'jefe') === 0) {
            return 'jefe';
        }

        if (strpos($role, 'gerente') === 0) {
            return 'gerente';
        }

        return $role;
    }

    private static function normalize($value)
    {
        $value = trim((string) $value);

        if ($value === '') {
            return '';
        }

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

            if ($converted !== false) {
                $value = $converted;
            }
        }

        $value = strtolower($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return trim($value);
    }
}
