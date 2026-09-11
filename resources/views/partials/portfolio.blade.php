{{-- Portfolio Section --}}
<section class="portfolio section" id="portfolio">
    <div class="container">
        <div class="section__header reveal">
            <span class="section__badge">Portfolio</span>
            <h2 class="section__title">{{ __('Proyek') }} <span class="text-gradient">{{ __('Unggulan') }}</span></h2>
            <p class="section__subtitle">{{ __('Beberapa proyek wisata dan pengembangan destinasi yang telah kami kerjakan.') }}</p>
        </div>

        <div class="portfolio__grid">
            @foreach($portfolios as $index => $project)
            <div class="portfolio__card reveal{{ $index > 0 ? ' reveal--delay-' . min($index, 2) : '' }}" 
                 onclick="openPortfolioModal({{ json_encode($project->title) }}, {{ json_encode($project->category) }}, {{ json_encode($project->content ?? $project->description) }}, {{ json_encode(asset('images/' . ($project->image ?? 'placeholder.webp'))) }})">
                <div class="portfolio__card-image">
                    <img src="{{ asset('images/' . ($project->image ?? 'placeholder.webp')) }}" alt="{{ $project->title }}" loading="lazy">
                    <div class="portfolio__card-overlay">
                        <span class="portfolio__card-category">{{ $project->category }}</span>
                        <h3 class="portfolio__card-title">{{ $project->title }}</h3>
                        <p class="portfolio__card-desc">{{ Str::limit($project->description, 80) }}</p>
                        <span style="color: var(--teal); font-size: 0.8rem; font-weight: 600; margin-top: 12px; display: inline-flex; align-items: center; gap: 4px;">
                            {{ __('Lihat Detail') }} <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Portfolio Modal --}}
        <div class="portfolio-modal" id="portfolioModal">
            <div class="portfolio-modal__overlay" onclick="closePortfolioModal()"></div>
            <div class="portfolio-modal__content">
                <button class="portfolio-modal__close" onclick="closePortfolioModal()">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                <div class="portfolio-modal__image-wrapper">
                    <img id="modalImage" src="" alt="Project Image">
                </div>
                <div class="portfolio-modal__body">
                    <span class="portfolio__card-category" id="modalCategory" style="display:inline-block; margin-bottom: 8px;"></span>
                    <h3 class="section__title" id="modalTitle" style="font-size: 1.8rem; margin-bottom: 16px;"></h3>
                    <div class="portfolio-modal__desc" id="modalContent" style="color: var(--text-secondary); line-height: 1.8;"></div>
                </div>
            </div>
        </div>
    </div>
</section>
