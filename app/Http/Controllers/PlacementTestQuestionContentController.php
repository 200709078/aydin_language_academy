<?php

namespace App\Http\Controllers;

use App\Models\PlacementTestLevel;
use App\Models\PlacementTestQuestionContent;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;
use RuntimeException;

class PlacementTestQuestionContentController extends Controller
{
    private const MEDIA_DISK = 'local';

    /**
     * @var list<string>
     */
    private const TYPES = ['text', 'audio', 'image', 'video'];

    /**
     * Display common content groups together with their usage information.
     */
    public function index(): View
    {
        $contents = PlacementTestQuestionContent::query()
            ->with('level')
            ->withCount('questions')
            ->orderBy(
                PlacementTestLevel::query()
                    ->select('sequence')
                    ->whereColumn('placement_test_levels.id', 'placement_test_question_contents.placement_test_level_id'),
            )
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('admin.placement-test.question-contents.index', compact('contents'));
    }

    /**
     * Show the create form for a shared text or media group.
     */
    public function create(): View
    {
        $defaultLevelId = PlacementTestQuestionContent::query()
            ->latest('id')
            ->value('placement_test_level_id')
            ?? PlacementTestLevel::query()
                ->where('code', 'A1')
                ->value('id');

        return view('admin.placement-test.question-contents.create', [
            'levels' => $this->examLevels(),
            'defaultLevelId' => $defaultLevelId,
        ]);
    }

    /**
     * Store a new shared content group and its optional server-side media file.
     */
    public function store(Request $request): RedirectResponse
    {
        $attributes = $this->validatedNewContent($request);

        $content = new PlacementTestQuestionContent;
        $content->fill($attributes);
        $content->save();

        return redirect()
            ->route('placement_test_question_contents_list')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Ortak İçerik']))
            ->with('modalSuccessContent', 'Ortak içerik eklendi.');
    }

    /**
     * Show a shared content group for safe editing.
     */
    public function edit(PlacementTestQuestionContent $placementTestQuestionContent): View
    {
        $placementTestQuestionContent->load('level');

        return view('admin.placement-test.question-contents.edit', compact('placementTestQuestionContent'));
    }

    /**
     * Update the content payload without changing its level or type.
     */
    public function update(Request $request, PlacementTestQuestionContent $placementTestQuestionContent): RedirectResponse
    {
        $attributes = $this->validatedExistingContent($request, $placementTestQuestionContent);

        $placementTestQuestionContent->update($attributes);

        return redirect()
            ->route('placement_test_question_contents_list')
            ->with('modalSuccessTitle', __('dictt.updatesuccesstitle', ['type' => 'Ortak İçerik']))
            ->with('modalSuccessContent', 'Ortak içerik güncellendi.');
    }

    /**
     * Delete only unassigned source content. Media files intentionally remain.
     */
    public function destroy(PlacementTestQuestionContent $placementTestQuestionContent): RedirectResponse
    {
        if ($placementTestQuestionContent->questions()->exists()) {
            return redirect()
                ->route('placement_test_question_contents_list')
                ->with('error', 'Bu ortak içeriğe bağlı sorular olduğu için silinemez. Önce soruları farklı bir içeriğe bağlayın veya pasife alın.');
        }

        try {
            $placementTestQuestionContent->delete();
        } catch (QueryException) {
            return redirect()
                ->route('placement_test_question_contents_list')
                ->with('error', 'Bu ortak içerik geçmiş sınav kayıtlarıyla ilişkili olduğu için silinemedi.');
        }

        return redirect()
            ->route('placement_test_question_contents_list')
            ->with('modalSuccessTitle', __('dictt.savesuccesstitle', ['type' => 'Ortak İçerik']))
            ->with('modalSuccessContent', 'Ortak içerik silindi. Geçmiş kayıtları korumak için varsa medya dosyası sunucuda bırakıldı.');
    }

    /**
     * Stream a server-stored media file to an authenticated administrator.
     */
    public function media(PlacementTestQuestionContent $placementTestQuestionContent)
    {
        if (
            $placementTestQuestionContent->type === 'text'
            || $placementTestQuestionContent->media_disk !== self::MEDIA_DISK
            || $placementTestQuestionContent->media_path === null
        ) {
            abort(404);
        }

        $disk = Storage::disk(self::MEDIA_DISK);

        if (! $disk->exists($placementTestQuestionContent->media_path)) {
            abort(404);
        }

        return $disk->response($placementTestQuestionContent->media_path);
    }

    /**
     * @return \Illuminate\Support\Collection<int, PlacementTestLevel>
     */
    private function examLevels()
    {
        return PlacementTestLevel::query()
            ->where('has_exam', true)
            ->orderBy('sequence')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedNewContent(Request $request): array
    {
        $base = $request->validate([
            'placement_test_level_id' => [
                'required',
                'integer',
                Rule::exists('placement_test_levels', 'id')->where('has_exam', true),
            ],
            'type' => ['required', Rule::in(self::TYPES)],
            'is_active' => ['required', 'boolean'],
        ], [
            'placement_test_level_id.required' => 'Seviye seçimi zorunludur.',
            'placement_test_level_id.exists' => 'Yalnız A1–C1 seviyelerinden biri seçilebilir.',
            'type.required' => 'İçerik türü zorunludur.',
            'type.in' => 'Geçerli bir içerik türü seçin.',
        ]);

        $type = $base['type'];
        $attributes = [
            'placement_test_level_id' => $base['placement_test_level_id'],
            'type' => $type,
            'is_active' => $request->boolean('is_active'),
        ];

        return [
            ...$attributes,
            ...$this->payloadForNewContent($request, $type),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedExistingContent(
        Request $request,
        PlacementTestQuestionContent $placementTestQuestionContent,
    ): array {
        $isActive = $request->boolean('is_active');

        if (! $isActive && $placementTestQuestionContent->questions()->where('is_active', true)->exists()) {
            throw ValidationException::withMessages([
                'is_active' => 'Aktif sorulara bağlı ortak içerik pasifleştirilemez. Önce bu soruları pasife alın.',
            ]);
        }

        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $attributes = [
            'is_active' => $isActive,
        ];

        if ($placementTestQuestionContent->type === 'text') {
            $validated = $request->validate($this->textRules());
            $textContent = $this->sanitizeTextContent($validated['text_content']);

            return [
                ...$attributes,
                'text_content' => $textContent,
                'media_disk' => null,
                'media_path' => null,
            ];
        }

        $validated = $request->validate([
            'media_file' => ['nullable', ...$this->mediaRules($placementTestQuestionContent->type)],
        ], $this->mediaMessages());

        if (! array_key_exists('media_file', $validated) || $validated['media_file'] === null) {
            return $attributes;
        }

        return [
            ...$attributes,
            'text_content' => null,
            'media_disk' => self::MEDIA_DISK,
            'media_path' => $this->storeMediaFile($request, $placementTestQuestionContent->type),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadForNewContent(Request $request, string $type): array
    {
        if ($type === 'text') {
            $validated = $request->validate($this->textRules());
            $textContent = $this->sanitizeTextContent($validated['text_content']);

            return [
                'text_content' => $textContent,
                'media_disk' => null,
                'media_path' => null,
            ];
        }

        $request->validate([
            'media_file' => ['required', ...$this->mediaRules($type)],
        ], $this->mediaMessages());

        return [
            'text_content' => null,
            'media_disk' => self::MEDIA_DISK,
            'media_path' => $this->storeMediaFile($request, $type),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private function textRules(): array
    {
        return [
            'text_content' => [
                'required',
                'string',
                static function (string $attribute, mixed $value, \Closure $fail): void {
                    if (trim((string) $value) === '') {
                        $fail('Ortak içerik metni boş olamaz.');
                    }
                },
            ],
        ];
    }

    private function sanitizeTextContent(string $textContent): string
    {
        $cleanContent = trim(Purifier::clean($textContent, 'quill'));

        if (trim(strip_tags($cleanContent)) === '') {
            throw ValidationException::withMessages([
                'text_content' => 'Ortak içerik metni boş olamaz.',
            ]);
        }

        return $cleanContent;
    }

    /**
     * @return list<string>
     */
    private function mediaRules(string $type): array
    {
        return match ($type) {
            'audio' => ['file', 'mimes:mp3,wav,ogg,m4a,aac', 'max:10240'],
            'image' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'video' => ['file', 'mimes:mp4,webm,ogv', 'max:10240'],
            default => throw new RuntimeException('Geçersiz medya türü.'),
        };
    }

    /**
     * @return array<string, string>
     */
    private function mediaMessages(): array
    {
        return [
            'media_file.required' => 'Bu içerik türü için dosya yüklemek zorunludur.',
            'media_file.file' => 'Geçerli bir dosya yükleyin.',
            'media_file.image' => 'Geçerli bir JPG, PNG veya WebP görseli yükleyin.',
            'media_file.mimes' => 'Seçilen içerik türü için desteklenen dosya biçimini yükleyin.',
            'media_file.max' => 'Dosya en fazla 10 MB olabilir.',
            'media_file.uploaded' => 'Dosya yüklenemedi. Sunucunun tek dosya limiti şu an 10 MB olarak ayarlanmalıdır.',
        ];
    }

    private function storeMediaFile(Request $request, string $type): string
    {
        $path = $request->file('media_file')->store("placement-test/question-contents/{$type}", self::MEDIA_DISK);

        if ($path === false) {
            throw new RuntimeException('Medya dosyası sunucuya kaydedilemedi.');
        }

        return $path;
    }
}
