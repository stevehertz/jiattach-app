<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTwoFactorSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_enabled',
        'method',
        'secret',
        'recovery_codes',
        'enabled_at'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'recovery_codes' => 'array',
        'enabled_at' => 'datetime'
    ];

    protected $hidden = [
        'secret',
        'recovery_codes'
    ];

    /**
     * Get the user that owns the 2FA setting
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(substr(md5(uniqid()), 0, 8));
        }

        $this->recovery_codes = $codes;
        $this->save();

        return $codes;
    }

    /**
     * Verify recovery code
     */
    public function verifyRecoveryCode($code)
    {
        if (empty($this->recovery_codes)) {
            return false;
        }

        $index = array_search($code, $this->recovery_codes);

        if ($index !== false) {
            // Remove used code
            $codes = $this->recovery_codes;
            unset($codes[$index]);
            $this->recovery_codes = array_values($codes);
            $this->save();

            return true;
        }

        return false;
    }
}
