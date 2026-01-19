<?php

use App\Helpers\Helpers;
use App\Helpers\SystemHelper;

if (!function_exists('getDashboardRoute')) {
    function getDashboardRoute(): string
    {
        return Helpers::getUserDashboardRoute();
    }
}

if (!function_exists('isStudent')) {
    function isStudent(): bool
    {
        return Helpers::isStudent();
    }
}

if (!function_exists('isNonStudent')) {
    function isNonStudent(): bool
    {
        return Helpers::isNonStudent();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return Helpers::isAdmin();
    }
}

if (!function_exists('isEmployer')) {
    function isEmployer(): bool
    {
        return Helpers::isEmployer();
    }
}

if (!function_exists('isMentor')) {
    function isMentor(): bool
    {
        return Helpers::isMentor();
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, string $format = 'M d, Y'): string
    {
        return Helpers::formatDate($date, $format);
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($date, string $format = 'M d, Y h:i A'): string
    {
        return Helpers::formatDateTime($date, $format);
    }
}

if (!function_exists('formatCurrency')) {
    function formatCurrency(?float $amount, bool $showCurrency = true): string
    {
        return Helpers::formatCurrency($amount, $showCurrency);
    }
}

if (!function_exists('truncateText')) {
    function truncateText(string $text, int $length = 100): string
    {
        return Helpers::truncateText($text, $length);
    }
}

if (!function_exists('getApplicationStatusBadge')) {
    function getApplicationStatusBadge(string $status): string
    {
        return Helpers::getApplicationStatusBadge($status);
    }
}

if (!function_exists('getOpportunityStatusBadge')) {
    function getOpportunityStatusBadge(string $status): string
    {
        return Helpers::getOpportunityStatusBadge($status);
    }
}

if (!function_exists('getMentorshipStatusBadge')) {
    function getMentorshipStatusBadge(string $status): string
    {
        return Helpers::getMentorshipStatusBadge($status);
    }
}

if (!function_exists('daysFromNow')) {
    function daysFromNow($date): int
    {
        return Helpers::daysFromNow($date);
    }
}

if (!function_exists('getDaysRemainingText')) {
    function getDaysRemainingText($date): string
    {
        return Helpers::getDaysRemainingText($date);
    }
}

if (!function_exists('getInitials')) {
    function getInitials(string $name): string
    {
        return Helpers::getInitials($name);
    }
}

if (!function_exists('getUserAvatar')) {
    function getUserAvatar($user, int $size = 32): string
    {
        return Helpers::getUserAvatar($user, $size);
    }
}

if (!function_exists('getKenyanCounties')) {
    function getKenyanCounties(): array
    {
        return Helpers::getKenyanCounties();
    }
}

if (!function_exists('getCourseLevels')) {
    function getCourseLevels(): array
    {
        return Helpers::getCourseLevels();
    }
}

if (!function_exists('getInstitutionTypes')) {
    function getInstitutionTypes(): array
    {
        return Helpers::getInstitutionTypes();
    }
}

if (!function_exists('getOpportunityTypes')) {
    function getOpportunityTypes(): array
    {
        return Helpers::getOpportunityTypes();
    }
}

if (!function_exists('getEmploymentTypes')) {
    function getEmploymentTypes(): array
    {
        return Helpers::getEmploymentTypes();
    }
}

if (!function_exists('getMeetingTypes')) {
    function getMeetingTypes(): array
    {
        return Helpers::getMeetingTypes();
    }
}

if (!function_exists('getCommonSkills')) {
    function getCommonSkills(): array
    {
        return Helpers::getCommonSkills();
    }
}

if (!function_exists('getIndustries')) {
    function getIndustries(): array
    {
        return Helpers::getIndustries();
    }
}

if (!function_exists('calculatePercentage')) {
    function calculatePercentage(int $part, int $whole): float
    {
        return Helpers::calculatePercentage($part, $whole);
    }
}

if (!function_exists('formatFileSize')) {
    function formatFileSize(int $bytes): string
    {
        return Helpers::formatFileSize($bytes);
    }
}

if (!function_exists('getRandomColor')) {
    function getRandomColor(): string
    {
        return Helpers::getRandomColor();
    }
}

if (!function_exists('sanitizeInput')) {
    function sanitizeInput(string $input): string
    {
        return Helpers::sanitizeInput($input);
    }
}

if (!function_exists('generateSlug')) {
    function generateSlug(string $title, string $model = null): string
    {
        return Helpers::generateSlug($title, $model);
    }
}

if (!function_exists('formatPhoneNumber')) {
    function formatPhoneNumber(string $phone): string
    {
        return Helpers::formatPhoneNumber($phone);
    }
}

if (!function_exists('isValidPhoneNumber')) {
    function isValidPhoneNumber(string $phone): bool
    {
        return Helpers::isValidPhoneNumber($phone);
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($date): string
    {
        return Helpers::timeAgo($date);
    }
}

if (!function_exists('getPlatformStats')) {
    function getPlatformStats(): array
    {
        return Helpers::getPlatformStats();
    }
}

if (!function_exists('isDeadlineApproaching')) {
    function isDeadlineApproaching($deadline, int $days = 3): bool
    {
        return Helpers::isDeadlineApproaching($deadline, $days);
    }
}

if (!function_exists('getMonthNames')) {
    function getMonthNames(): array
    {
        return Helpers::getMonthNames();
    }
}

if (!function_exists('getAcademicYears')) {
    function getAcademicYears(): array
    {
        return Helpers::getAcademicYears();
    }
}

if (!function_exists('getYearOfStudyOptions')) {
    function getYearOfStudyOptions(): array
    {
        return Helpers::getYearOfStudyOptions();
    }
}

if (!function_exists('getCgpaOptions')) {
    function getCgpaOptions(): array
    {
        return Helpers::getCgpaOptions();
    }
}

if (!function_exists('getSessionDurationOptions')) {
    function getSessionDurationOptions(): array
    {
        return Helpers::getSessionDurationOptions();
    }
}

if (!function_exists('getRatingStars')) {
    function getRatingStars(float $rating, int $maxStars = 5): string
    {
        return Helpers::getRatingStars($rating, $maxStars);
    }
}

if (!function_exists('getBreadcrumbs')) {
    function getBreadcrumbs(): array
    {
        return Helpers::getBreadcrumbs();
    }
}

if (!function_exists('getPaginationSummary')) {
    function getPaginationSummary($paginator): string
    {
        return Helpers::getPaginationSummary($paginator);
    }
}

if (!function_exists('getNotificationCount')) {
    function getNotificationCount(): int
    {
        return Helpers::getNotificationCount();
    }
}

if (!function_exists('getUserDisplayName')) {
    function getUserDisplayName($user, bool $showRole = true): string
    {
        return Helpers::getUserDisplayName($user, $showRole);
    }
}

// Add to app/Helpers/Helpers.php or create app/Helpers/SettingsHelper.php
if (!function_exists('setting')) {
    /**
     * Get or set a setting value.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed|\App\Services\SettingsService
     */
    function setting($key = null, $default = null)
    {
        $settings = app(\App\Services\SettingsService::class);
        
        if (is_null($key)) {
            return $settings;
        }
        
        return $settings->get($key, $default);
    }
}

if (!function_exists('site_config')) {
    /**
     * Get site configuration.
     *
     * @return array
     */
    function site_config(): array
    {
        return app(\App\Services\SettingsService::class)->getSiteConfig();
    }
}

if (!function_exists('is_maintenance_mode')) {
    /**
     * Check if maintenance mode is enabled.
     *
     * @return bool
     */
    function is_maintenance_mode(): bool
    {
        return app(\App\Services\SettingsService::class)->isMaintenanceMode();
    }
}

if (!function_exists('getAdministratorStats')) {
    function getAdministratorStats(): array
    {
        return Helpers::getAdministratorStats();
    }
}

if (!function_exists('getAdministratorRoleBadge')) {
    function getAdministratorRoleBadge(string $role): string
    {
        return Helpers::getAdministratorRoleBadge($role);
    }
}

if (!function_exists('checkPasswordStrength')) {
    function checkPasswordStrength(string $password): array
    {
        return Helpers::checkPasswordStrength($password);
    }
}

if (!function_exists('getRoleBadge')) {
    function getRoleBadge(string $role): string
    {
        return Helpers::getRoleBadge($role);
    }
}

if (!function_exists('isRoleProtected')) {
    function isRoleProtected(string $roleName): bool
    {
        return Helpers::isRoleProtected($roleName);
    }
}

if (!function_exists('getUserRoleBadges')) {
    function getUserRoleBadges($user): string
    {
        return Helpers::getUserRoleBadges($user);
    }
}

if (!function_exists('system_health_status')) {
    function system_health_status(): array
    {
        return SystemHelper::getHealthStatus();
    }
}

if (!function_exists('system_uptime')) {
    function system_uptime(): string
    {
        return SystemHelper::getUptime();
    }
}

if (!function_exists('system_load_average')) {
    function system_load_average(): array
    {
        return SystemHelper::getLoadAverage();
    }
}


