<div class="testimonial-item text-center h-100 w-100 d-flex flex-column">
    <div class="d-flex align-items-center justify-content-center bg-light rounded-circle mx-auto mb-4" style="width: 100px; height: 100px;">
        <i class="fa fa-user text-primary" style="font-size: 2.2rem;"></i>
    </div>
    <div class="testimonial-text bg-light rounded text-center p-4 flex-grow-1 d-flex flex-column">
        <p>{{ $review->content }}</p>
        <div class="mb-2 mt-auto">
            @for ($i = 1; $i <= 5; $i++)
                <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }} me-1"></i>
            @endfor
        </div>
        <h5 class="mb-1">{{ $review->user?->name ?? __('dictt.member_fallback') }}</h5>
        <span class="fst-italic">{{ $review->branchLabel() }} · {{ $review->created_at?->format('d.m.Y') }}</span>
    </div>
</div>
