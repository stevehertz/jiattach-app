<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\AttachmentOpportunity;
use App\Models\Application;
use App\Models\Mentorship;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Helpers
{
    /**
     * Get the dashboard route based on user role.
     */
    public static function getUserDashboardRoute(): string
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->hasRole('student')) {
                return 'student.dashboard';
            } else {
                // All other roles go to admin dashboard
                return 'admin.dashboard';
            }
        }

        return 'login';
    }

    /**
     * Check if current user is a student.
     */
    public static function isStudent(): bool
    {
        return Auth::check() && Auth::user()->hasRole('student');
    }

    /**
     * Check if current user is NOT a student (admin, employer, mentor).
     */
    public static function isNonStudent(): bool
    {
        return Auth::check() && !Auth::user()->hasRole('student');
    }

    /**
     * Check if current user is an admin.
     */
    public static function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    /**
     * Check if current user is an employer.
     */
    public static function isEmployer(): bool
    {
        return auth()->check() && auth()->user()->hasRole('employer');
    }

    /**
     * Check if current user is a mentor.
     */
    public static function isMentor(): bool
    {
        return auth()->check() && auth()->user()->hasRole('mentor');
    }

    /**
     * Get user's display name with role badge.
     */
    public static function getUserDisplayName(User $user, bool $showRole = true): string
    {
        $name = $user->full_name;

        if ($showRole) {
            $role = $user->getRoleNames()->first();
            if ($role) {
                $badgeColor = match ($role) {
                    'student' => 'success',
                    'admin' => 'danger',
                    'employer' => 'primary',
                    'mentor' => 'warning',
                    default => 'secondary'
                };

                $name .= ' <span class="badge badge-' . $badgeColor . '">' . ucfirst($role) . '</span>';
            }
        }

        return $name;
    }

    /**
     * Format date for display.
     */
    public static function formatDate($date, string $format = 'M d, Y'): string
    {
        if (!$date) {
            return 'N/A';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }

    /**
     * Format date with time.
     */
    public static function formatDateTime($date, string $format = 'M d, Y h:i A'): string
    {
        if (!$date) {
            return 'N/A';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->format($format);
    }

    /**
     * Format currency (Kenyan Shillings).
     */
    public static function formatCurrency(?float $amount, bool $showCurrency = true): string
    {
        if ($amount === null || $amount === 0) {
            return 'Unpaid';
        }

        $formatted = number_format($amount, 2);

        if ($showCurrency) {
            return 'KSh ' . $formatted;
        }

        return $formatted;
    }

    /**
     * Truncate text with ellipsis.
     */
    public static function truncateText(string $text, int $length = 100): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }

    /**
     * Get application status badge.
     */
    public static function getApplicationStatusBadge(string $status): string
    {
        $statusColors = [
            'draft' => 'secondary',
            'submitted' => 'info',
            'under_review' => 'primary',
            'shortlisted' => 'warning',
            'interview_scheduled' => 'purple',
            'interview_completed' => 'indigo',
            'offer_sent' => 'teal',
            'offer_accepted' => 'success',
            'offer_rejected' => 'orange',
            'rejected' => 'danger',
            'withdrawn' => 'dark',
            'hired' => 'success',
        ];

        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'shortlisted' => 'Shortlisted',
            'interview_scheduled' => 'Interview Scheduled',
            'interview_completed' => 'Interview Completed',
            'offer_sent' => 'Offer Sent',
            'offer_accepted' => 'Offer Accepted',
            'offer_rejected' => 'Offer Rejected',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            'hired' => 'Hired',
        ];

        $color = $statusColors[$status] ?? 'secondary';
        $label = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get opportunity status badge.
     */
    public static function getOpportunityStatusBadge(string $status): string
    {
        $statusColors = [
            'draft' => 'secondary',
            'pending_approval' => 'warning',
            'published' => 'success',
            'closed' => 'info',
            'filled' => 'primary',
            'cancelled' => 'danger',
        ];

        $statusLabels = [
            'draft' => 'Draft',
            'pending_approval' => 'Pending Approval',
            'published' => 'Published',
            'closed' => 'Closed',
            'filled' => 'Filled',
            'cancelled' => 'Cancelled',
        ];

        $color = $statusColors[$status] ?? 'secondary';
        $label = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Get mentorship status badge.
     */
    public static function getMentorshipStatusBadge(string $status): string
    {
        $statusColors = [
            'requested' => 'info',
            'pending_approval' => 'warning',
            'active' => 'success',
            'paused' => 'secondary',
            'completed' => 'primary',
            'cancelled' => 'danger',
            'rejected' => 'dark',
        ];

        $statusLabels = [
            'requested' => 'Requested',
            'pending_approval' => 'Pending Approval',
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected',
        ];

        $color = $statusColors[$status] ?? 'secondary';
        $label = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Calculate days difference from now.
     */
    public static function daysFromNow($date): int
    {
        if (!$date) {
            return 0;
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return now()->diffInDays($date, false);
    }

    /**
     * Get days remaining text with color.
     */
    public static function getDaysRemainingText($date): string
    {
        $days = self::daysFromNow($date);

        if ($days > 30) {
            $color = 'success';
            $text = $days . ' days';
        } elseif ($days > 7) {
            $color = 'warning';
            $text = $days . ' days';
        } elseif ($days > 0) {
            $color = 'danger';
            $text = $days . ' days';
        } elseif ($days == 0) {
            $color = 'danger';
            $text = 'Today';
        } else {
            $color = 'dark';
            $text = 'Expired';
        }

        return '<span class="badge badge-' . $color . '">' . $text . '</span>';
    }

    /**
     * Generate initials from name.
     */
    public static function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';

        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get user avatar URL or initials.
     */
    public static function getUserAvatar(User $user, int $size = 32): string
    {
        // If user has a profile picture, use it
        if ($user->profile_picture) {
            return '<img src="' . asset($user->profile_picture) . '" alt="' . e($user->full_name) . '" class="img-circle" style="width: ' . $size . 'px; height: ' . $size . 'px;">';
        }

        // Otherwise, create initials avatar
        $initials = self::getInitials($user->full_name);
        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        $color = $colors[crc32($user->email) % count($colors)];

        return '<div class="avatar-initials bg-' . $color . '" style="width: ' . $size . 'px; height: ' . $size . 'px; line-height: ' . $size . 'px; border-radius: 50%; text-align: center; color: white; font-weight: bold;">' . $initials . '</div>';
    }

    /**
     * Get Kenyan counties array.
     */
    public static function getKenyanCounties(): array
    {
        return [
            'Mombasa',
            'Kwale',
            'Kilifi',
            'Tana River',
            'Lamu',
            'Taita–Taveta',
            'Garissa',
            'Wajir',
            'Mandera',
            'Marsabit',
            'Isiolo',
            'Meru',
            'Tharaka-Nithi',
            'Embu',
            'Kitui',
            'Machakos',
            'Makueni',
            'Nyandarua',
            'Nyeri',
            'Kirinyaga',
            'Murang\'a',
            'Kiambu',
            'Turkana',
            'West Pokot',
            'Samburu',
            'Trans-Nzoia',
            'Uasin Gishu',
            'Elgeyo-Marakwet',
            'Nandi',
            'Baringo',
            'Laikipia',
            'Nakuru',
            'Narok',
            'Kajiado',
            'Kericho',
            'Bomet',
            'Kakamega',
            'Vihiga',
            'Bungoma',
            'Busia',
            'Siaya',
            'Kisumu',
            'Homa Bay',
            'Migori',
            'Kisii',
            'Nyamira',
            'Nairobi'
        ];
    }

    /**
     * Get course levels array.
     */
    public static function getCourseLevels(): array
    {
        return [
            'certificate' => 'Certificate',
            'diploma' => 'Diploma',
            'bachelor' => 'Bachelor\'s Degree',
            'masters' => 'Master\'s Degree',
            'phd' => 'PhD',
        ];
    }

    /**
     * Get institution types array.
     */
    public static function getInstitutionTypes(): array
    {
        return [
            'university' => 'University',
            'college' => 'College',
            'polytechnic' => 'Polytechnic',
            'technical' => 'Technical Institute',
        ];
    }

    /**
     * Get opportunity types array.
     */
    public static function getOpportunityTypes(): array
    {
        return [
            'internship' => 'Internship',
            'attachment' => 'Attachment',
            'volunteer' => 'Volunteer',
            'research' => 'Research',
            'part_time' => 'Part-time',
            'full_time' => 'Full-time',
        ];
    }

    /**
     * Get employment types array.
     */
    public static function getEmploymentTypes(): array
    {
        return [
            'contract' => 'Contract',
            'permanent' => 'Permanent',
            'temporary' => 'Temporary',
            'seasonal' => 'Seasonal',
        ];
    }

    /**
     * Get meeting types array.
     */
    public static function getMeetingTypes(): array
    {
        return [
            'video' => 'Video Call',
            'phone' => 'Phone Call',
            'in_person' => 'In Person',
            'hybrid' => 'Hybrid',
        ];
    }

    /**
     * Get skills array (common skills for Kenya).
     */
    public static function getCommonSkills(): array
    {
        return [
            'PHP',
            'Laravel',
            'JavaScript',
            'Vue.js',
            'React',
            'Node.js',
            'Python',
            'Django',
            'Java',
            'Spring Boot',
            'C#',
            '.NET',
            'Mobile Development',
            'Flutter',
            'React Native',
            'UI/UX Design',
            'Graphic Design',
            'Digital Marketing',
            'SEO',
            'Content Writing',
            'Project Management',
            'Agile Methodology',
            'Scrum',
            'Data Analysis',
            'Excel',
            'Accounting',
            'QuickBooks',
            'Customer Service',
            'Sales',
            'Marketing',
            'Communication',
            'Leadership',
            'Teamwork',
            'Problem Solving',
            'Critical Thinking',
            'Research',
            'Report Writing',
            'Public Speaking',
            'Networking',
        ];
    }

    /**
     * Get industries array.
     */
    public static function getIndustries(): array
    {
        return [
            'Technology',
            'Finance',
            'Healthcare',
            'Education',
            'Agriculture',
            'Manufacturing',
            'Retail',
            'Hospitality',
            'Telecommunications',
            'Energy',
            'Transportation',
            'Construction',
            'Real Estate',
            'Media & Entertainment',
            'Non-profit',
            'Government',
            'Consulting',
            'Legal',
            'Marketing & Advertising',
        ];
    }

    /**
     * Calculate percentage.
     */
    public static function calculatePercentage(int $part, int $whole): float
    {
        if ($whole === 0) {
            return 0;
        }

        return round(($part / $whole) * 100, 2);
    }

    /**
     * Format file size.
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Generate random color for badges/charts.
     */
    public static function getRandomColor(): string
    {
        $colors = [
            'primary',
            'secondary',
            'success',
            'danger',
            'warning',
            'info',
            'dark',
            'light',
            'indigo',
            'purple',
            'pink',
            'teal',
            'orange'
        ];

        return $colors[array_rand($colors)];
    }

    /**
     * Sanitize input for safe display.
     */
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate unique slug.
     */
    public static function generateSlug(string $title, string $model = null): string
    {
        $slug = Str::slug($title);

        // If model is provided, ensure uniqueness
        if ($model && class_exists($model)) {
            $count = 1;
            $originalSlug = $slug;

            while ($model::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count;
                $count++;
            }
        }

        return $slug;
    }

    /**
     * Get Kenyan phone number format.
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Check if it's a Kenyan phone number
        if (strlen($phone) === 9) {
            // Add leading 0
            $phone = '0' . $phone;
        } elseif (strlen($phone) === 12 && str_starts_with($phone, '254')) {
            // Convert 254 to 0
            $phone = '0' . substr($phone, 3);
        }

        // Format: XXX XXX XXX
        if (strlen($phone) === 10) {
            return preg_replace('/(\d{3})(\d{3})(\d{3,4})/', '$1 $2 $3', $phone);
        }

        return $phone;
    }

    /**
     * Validate Kenyan phone number.
     */
    public static function isValidPhoneNumber(string $phone): bool
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Kenyan phone numbers: 254XXXXXXXXX or 07XXXXXXXX or 01XXXXXXXX
        $patterns = [
            '/^2547[0-9]{8}$/', // Safaricom
            '/^2541[0-9]{8}$/', // Airtel
            '/^2547[4-5][0-9]{7}$/', // Telkom
            '/^07[0-9]{8}$/', // Safaricom with 0
            '/^01[0-9]{8}$/', // Airtel with 0
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $phone)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get time ago string.
     */
    public static function timeAgo($date): string
    {
        if (!$date) {
            return 'Never';
        }

        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return $date->diffForHumans();
    }

    /**
     * Get platform statistics.
     */
    public static function getPlatformStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_students' => User::role('student')->count(),
            'total_employers' => User::role('employer')->count(),
            'total_mentors' => User::role('mentor')->count(),
            'total_opportunities' => AttachmentOpportunity::count(),
            'active_opportunities' => AttachmentOpportunity::where('status', 'published')->count(),
            'total_applications' => Application::count(),
            'pending_applications' => Application::where('status', 'submitted')->count(),
            'successful_placements' => Application::where('status', 'hired')->count(),
            'active_mentorships' => Mentorship::where('status', 'active')->count(),
        ];
    }

    /**
     * Check if opportunity deadline is approaching.
     */
    public static function isDeadlineApproaching($deadline, int $days = 3): bool
    {
        if (!$deadline) {
            return false;
        }

        if (is_string($deadline)) {
            $deadline = Carbon::parse($deadline);
        }

        return now()->diffInDays($deadline, false) <= $days && now()->diffInDays($deadline, false) >= 0;
    }

    /**
     * Get month names array.
     */
    public static function getMonthNames(): array
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }

    /**
     * Get academic years array.
     */
    public static function getAcademicYears(): array
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[$year] = $year;
        }

        return $years;
    }

    /**
     * Get year of study array.
     */
    public static function getYearOfStudyOptions(): array
    {
        return [
            1 => 'First Year',
            2 => 'Second Year',
            3 => 'Third Year',
            4 => 'Fourth Year',
            5 => 'Fifth Year',
            6 => 'Sixth Year',
        ];
    }

    /**
     * Get CGPA options.
     */
    public static function getCgpaOptions(): array
    {
        $options = [];

        for ($i = 10; $i <= 40; $i += 5) {
            $cgpa = $i / 10;
            $options[$cgpa] = number_format($cgpa, 1);
        }

        return $options;
    }

    /**
     * Get session duration options.
     */
    public static function getSessionDurationOptions(): array
    {
        return [
            15 => '15 minutes',
            30 => '30 minutes',
            45 => '45 minutes',
            60 => '1 hour',
            90 => '1.5 hours',
            120 => '2 hours',
        ];
    }

    /**
     * Get rating stars HTML.
     */
    public static function getRatingStars(float $rating, int $maxStars = 5): string
    {
        $html = '';
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;

        for ($i = 1; $i <= $maxStars; $i++) {
            if ($i <= $fullStars) {
                $html .= '<i class="fas fa-star text-warning"></i>';
            } elseif ($i == $fullStars + 1 && $halfStar) {
                $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
            } else {
                $html .= '<i class="far fa-star text-warning"></i>';
            }
        }

        return $html;
    }

    /**
     * Get breadcrumb items for current route.
     */
    public static function getBreadcrumbs(): array
    {
        $route = request()->route();
        $breadcrumbs = [];

        if ($route) {
            $name = $route->getName();
            $params = $route->parameters();

            // Split route name into segments
            $segments = explode('.', $name);

            $url = '';
            foreach ($segments as $segment) {
                $url .= ($url ? '.' : '') . $segment;
                $breadcrumbs[] = [
                    'name' => ucfirst(str_replace('-', ' ', $segment)),
                    'url' => route($url, $params),
                    'active' => $url === $name
                ];
            }
        }

        return $breadcrumbs;
    }

    /**
     * Get pagination summary text.
     */
    public static function getPaginationSummary($paginator): string
    {
        $from = ($paginator->currentPage() - 1) * $paginator->perPage() + 1;
        $to = min($paginator->currentPage() * $paginator->perPage(), $paginator->total());

        return "Showing {$from} to {$to} of {$paginator->total()} entries";
    }

    /**
     * Get notification count for current user.
     */
    public static function getNotificationCount(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        $user = User::findOrFail(Auth::user()->id);

        // Count pending applications
        $pendingApps = $user->applications()
            ->whereIn('status', ['submitted', 'under_review'])
            ->count();

        // Count upcoming interviews
        $interviews = $user->applications()
            ->where('status', 'interview_scheduled')
            ->count();

        // Count approaching deadlines for student
        if ($user->hasRole('student')) {
            $deadlines = AttachmentOpportunity::where('application_deadline', '>=', now())
                ->where('application_deadline', '<=', now()->addDays(3))
                ->where('status', 'published')
                ->count();
        } else {
            $deadlines = 0;
        }

        return $pendingApps + $interviews + $deadlines;
    }

    // Add to your Helpers class
    public static function getAdministratorStats(): array
    {
        return [
            'total' => User::role(['admin', 'super_admin'])->count(),
            'active' => User::role(['admin', 'super_admin'])->where('is_active', true)->count(),
            'inactive' => User::role(['admin', 'super_admin'])->where('is_active', false)->count(),
            'super_admins' => User::role('super_admin')->count(),
            'admins' => User::role('admin')->count(),
            'moderators' => User::role('moderator')->count(),
        ];
    }

    public static function getAdministratorRoleBadge(string $role): string
    {
        $badgeColors = [
            'super_admin' => 'danger',
            'admin' => 'success',
            'moderator' => 'info',
        ];

        $roleLabels = [
            'super_admin' => 'Super Admin',
            'admin' => 'Administrator',
            'moderator' => 'Moderator',
        ];

        $color = $badgeColors[$role] ?? 'secondary';
        $label = $roleLabels[$role] ?? ucfirst(str_replace('_', ' ', $role));

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Check password strength.
     */
    public static function checkPasswordStrength(string $password): array
    {
        $strength = 0;
        $feedback = [];

        if (strlen($password) >= 8) $strength++;
        if (preg_match('/[A-Z]/', $password)) $strength++;
        if (preg_match('/[a-z]/', $password)) $strength++;
        if (preg_match('/[0-9]/', $password)) $strength++;
        if (preg_match('/[^A-Za-z0-9]/', $password)) $strength++;

        if ($strength <= 2) {
            $feedback[] = 'Weak password. Include uppercase, lowercase, numbers, and symbols.';
        } elseif ($strength <= 4) {
            $feedback[] = 'Medium strength password.';
        } else {
            $feedback[] = 'Strong password.';
        }

        return [
            'score' => $strength,
            'strength' => match ($strength) {
                1, 2 => 'Weak',
                3, 4 => 'Medium',
                5 => 'Strong',
                default => 'Very Weak'
            },
            'feedback' => $feedback,
        ];
    }

    /**
     * Get role badge with color coding.
     */
    public static function getRoleBadge(string $role): string
    {
        $badgeColors = [
            'super-admin' => 'danger',
            'admin' => 'success',
            'moderator' => 'info',
            'student' => 'primary',
            'employer' => 'warning',
            'mentor' => 'purple',
            'entrepreneur' => 'teal',
        ];

        $roleLabels = [
            'super-admin' => 'Super Admin',
            'admin' => 'Administrator',
            'moderator' => 'Moderator',
            'student' => 'Student',
            'employer' => 'Employer',
            'mentor' => 'Mentor',
            'entrepreneur' => 'Entrepreneur',
        ];

        $color = $badgeColors[$role] ?? 'secondary';
        $label = $roleLabels[$role] ?? ucfirst(str_replace('-', ' ', $role));

        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }

    /**
     * Check if role is protected (system role).
     */
    public static function isRoleProtected(string $roleName): bool
    {
        $protectedRoles = [
            'super-admin',
            'admin',
            'moderator',
            'system-admin',
            'system-manager',
        ];

        return in_array($roleName, $protectedRoles) || Str::startsWith($roleName, 'system-');
    }

    /**
     * Get user roles as badges.
     */
    public static function getUserRoleBadges(User $user): string
    {
        $roles = $user->getRoleNames();
        $badges = '';

        foreach ($roles as $role) {
            $badges .= self::getRoleBadge($role) . ' ';
        }

        return $badges ?: '<span class="badge badge-secondary">No Role</span>';
    }

    // Add to your Helpers class
    public static function getAnalyticsData($startDate = null, $endDate = null, $viewType = 'monthly'): array
    {
        $startDate = $startDate ? Carbon::parse($startDate) : now()->subMonth();
        $endDate = $endDate ? Carbon::parse($endDate) : now();

        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'view_type' => $viewType,
            ],
            'summary' => self::getAnalyticsSummary($startDate, $endDate),
            'charts' => self::getAnalyticsCharts($startDate, $endDate, $viewType),
        ];
    }

    public static function getAnalyticsSummary($startDate, $endDate): array
    {
        return [
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_opportunities' => AttachmentOpportunity::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_applications' => Application::whereBetween('submitted_at', [$startDate, $endDate])->count(),
            'placements' => Application::whereBetween('submitted_at', [$startDate, $endDate])
                ->where('status', 'hired')
                ->count(),
            'avg_application_time' => self::calculateAvgApplicationTime($startDate, $endDate),
            'top_skills' => self::getTopSkills($startDate, $endDate),
        ];
    }

    public static function getAnalyticsCharts($startDate, $endDate, $viewType): array
    {
        return [
            'user_growth' => self::generateUserGrowthChart($startDate, $endDate, $viewType),
            'application_trends' => self::generateApplicationTrendsChart($startDate, $endDate, $viewType),
            'opportunity_distribution' => self::generateOpportunityDistributionChart($startDate, $endDate),
            'institution_performance' => self::generateInstitutionPerformanceChart($startDate, $endDate),
        ];
    }


    /**
     * Get resource color for badges
     */
    public static function getResourceColor(string $resource): string
    {
        return match ($resource) {
            'students' => 'success',
            'employers' => 'primary',
            'mentors' => 'warning',
            'administrators' => 'danger',
            'roles' => 'info',
            'opportunities' => 'warning',
            'applications' => 'info',
            'exchange-programs' => 'purple',
            'mentorships' => 'primary',
            'reports' => 'success',
            'settings' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get action color for badges
     */
    public static function getActionColor(string $action): string
    {
        return match ($action) {
            'active', 'verified', 'completed', 'hired' => 'success',
            'pending', 'seeking', 'open' => 'warning',
            'featured', 'available' => 'info',
            'on-attachment', 'ongoing' => 'primary',
            default => 'secondary'
        };
    }

    /**
     * Check if user can access sidebar item
     */
    public static function canAccessSidebarItem($user, $permissionName): bool
    {
        return $user->can($permissionName);
    }
}
