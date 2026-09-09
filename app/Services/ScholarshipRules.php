<?php

namespace App\Services;

use App\Models\ScholarshipApplication;
use App\Models\ScholarshipExamPeriod;
use App\Models\ScholarshipExamSession;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use LogicException;

/** Shared validation and lock order for every scholarship write entry point. */
class ScholarshipRules
{
    public static function transaction(Closure $operation): mixed
    {
        // An outer transaction may already hold an old repeatable-read snapshot.
        // MariaDB can abort that entire transaction on a later locking read.
        // Write commands therefore own their transaction and its retry boundary.
        if (DB::transactionLevel() !== 0) {
            throw new LogicException('Bursluluk yazma servisleri kendi transaction sınırından çağrılmalıdır.');
        }

        return DB::transaction($operation, 3);
    }

    public static function actor(User $actor, bool $admin = false): User
    {
        $current = $actor->exists ? User::query()->find($actor->getKey()) : null;

        if ($current === null || ($admin && $current->type !== 'admin')) {
            throw new AuthorizationException;
        }

        return $current;
    }

    public static function validate(array $data, array $rules, array $attributes = []): array
    {
        foreach (array_diff(array_keys($data), array_keys($rules)) as $field) {
            self::fail((string) $field, __('scholarship.field_read_only'));
        }

        return Validator::make($data, $rules, [], $attributes)->validate();
    }

    public static function integer(): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail): void {
            if (! is_int($value) && (! is_string($value) || ! preg_match('/^-?\d+$/D', $value))) {
                $fail(__('scholarship.integer_required'));
            }
        };
    }

    public static function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([$field => $message]);
    }

    public static function period(int $id): ScholarshipExamPeriod
    {
        // First locking read in each transaction: a consistent-read snapshot must
        // not precede this lock, otherwise occupancy can be stale on MySQL RR.
        return ScholarshipExamPeriod::query()->lockForUpdate()->findOrFail($id);
    }

    public static function application(int $id, Closure $operation): mixed
    {
        // Period identity is immutable. Resolve it BEFORE opening the transaction.
        $periodId = ScholarshipApplication::query()->whereKey($id)->firstOrFail(['period_id'])->period_id;

        return self::transaction(function () use ($id, $periodId, $operation): mixed {
            $period = self::period($periodId);
            $application = ScholarshipApplication::query()->lockForUpdate()->findOrFail($id);

            return $operation($application, $period);
        });
    }

    public static function owns(User $actor, ScholarshipApplication $application): void
    {
        if ((int) $application->user_id !== (int) $actor->getKey()) {
            throw new AuthorizationException;
        }
    }

    public static function memberBlock(ScholarshipApplication $application, ScholarshipExamPeriod $period, ScholarshipExamSession $session): ?string
    {
        if ($session->archived_at !== null) {
            return 'archived';
        }
        if (! $session->is_active) {
            return 'suspended';
        }
        if (! $period->acceptsApplications()) {
            return 'applications_closed';
        }
        if ($application->status !== 'pending') {
            // Do not disclose an unpublished approval through an error message.
            return 'read_only';
        }

        return null;
    }

    public static function memberWritable(ScholarshipApplication $application, ScholarshipExamPeriod $period, ScholarshipExamSession $session): void
    {
        if (self::memberBlock($application, $period, $session) !== null) {
            self::fail('application', 'Bu başvuruda şu anda düzenleme veya silme yapılamaz.');
        }
    }

    public static function phase(string $phase): void
    {
        if (! in_array($phase, ['application', 'result'], true)) {
            self::fail('phase', 'Geçersiz iletişim/yayın aşaması.');
        }
    }
}
