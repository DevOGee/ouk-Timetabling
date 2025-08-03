@if(($isAdmin || $isTimetabler) && !empty($heatmapData['programmes']) && $currentSession)
    <!-- Heatmap Section -->
    <div class="heatmap-container">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>
                    @if(auth()->user()->hasRole('timetabler'))
                        My School's Programme Mapping
                    @else
                        Course Unit Mapping Overview
                    @endif
                </h5>
                <div class="d-flex align-items-center gap-3">
                    @if(auth()->user()->hasRole('timetabler') && !empty($heatmapData['programmes']))
                        <div class="text-muted small">
                            <i class="bi bi-building me-1"></i> {{ $heatmapData['programmes'][0]['school'] ?? 'My School' }}
                        </div>
                    @endif
                    <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                        <i class="bi bi-calendar-week me-1"></i>
                        {{ $currentSession->name }}
                    </span>
                </div>
            </div>
            <div>
                <!-- Heatmap Grid -->
                <div class="heatmap-grid">
                    <!-- Header Row -->
                    <div class="heatmap-header-cell heatmap-programme-header"></div>
                    @foreach($heatmapData['levels'] as $level)
                        <div class="heatmap-header-cell">{{ $level }}</div>
                    @endforeach

                    <!-- Programme Rows -->
                    @foreach($heatmapData['programmes'] as $programme)
                        <div class="heatmap-programme-cell" title="{{ $programme['name'] }}">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="bi bi-journal-bookmark text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $programme['code'] }}</div>
                                        <small class="text-muted d-block">{{ $programme['name'] }}</small>
                                    </div>
                                </div>
                                <a href="/admin/academic-sessions/{{ $currentSession->id }}/programmes/{{ $programme['id'] }}/map-course-units" 
                                   class="btn btn-sm btn-outline-primary ms-2"
                                   title="Map Course Units for {{ $programme['code'] }}"
                                   data-bs-toggle="tooltip">
                                    <i class="bi bi-diagram-3"></i>
                                    <span class="d-none d-md-inline">Map</span>
                                </a>
                            </div>
                        </div>
                        @foreach($heatmapData['levels'] as $level)
                            @php
                                $count = $programme['levels'][$level]['count'] ?? 0;
                                $mappings = $programme['levels'][$level]['mappings'] ?? [];
                            @endphp
                            <div class="heatmap-cell"
                                 data-programme-id="{{ $programme['id'] }}"
                                 data-level="{{ $level }}"
                                 data-count="{{ $count }}">
                                <div class="heatmap-tooltip">
                                    {{ $programme['code'] }} - Level {{ $level }}
                                    <br>Courses: {{ $count }}
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="heatmap-legend mt-4">
                    <span class="me-3 fw-bold">Courses per level:</span>
                    @php
                        $maxCount = $heatmapData['maxCount'];
                        $steps = min(5, $maxCount);
                        $stepSize = $maxCount > 0 ? $maxCount / $steps : 1;
                        $colors = [
                            '#e6f2ff', // lightest
                            '#b3d7ff',
                            '#80bdff',
                            '#4da3ff',
                            '#1a88ff',
                            '#0066e0', // darkest
                        ];

                        function getServerHeatmapColor($count, $maxCount, $colors) {
                            if ($count === 0) return '#f8f9fa';
                            $index = min(
                                floor(($count / $maxCount) * (count($colors) - 1)),
                                count($colors) - 1
                            );
                            return $colors[$index];
                        }
                    @endphp

                    @for($i = 0; $i <= $steps; $i++)
                        @php
                            $count = floor($i * $stepSize);
                            $color = $i === 0 ? '#f8f9fa' : getServerHeatmapColor($count, $maxCount, $colors);
                            $nextCount = $i < $steps ? floor(($i + 1) * $stepSize) : '';
                            $label = $i === 0 ? '0' : ($i === $steps ? $maxCount . '+' : $count . (($i < $steps && $nextCount > $count + 1) ? '-' . ($nextCount - 1) : ''));
                        @endphp
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-color" style="background-color: {{ $color }};"></div>
                            <span>{{ $label }}</span>
                        </div>
                        @if($i < $steps)
                            <div class="heatmap-legend-item">→</div>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <style>
        .heatmap-container {
            width: 100%;
            overflow-x: auto;
            padding: 0.5rem;
        }

        .heatmap-grid {
            display: grid;
            grid-template-columns: auto repeat({{ count($heatmapData['levels']) }}, 80px);
            gap: 4px;
            padding: 8px;
        }

        .heatmap-header-cell {
            font-weight: 600;
            color: #495057;
            padding: 1rem;
            text-align: center;
            background-color: #f1f3f5;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .heatmap-programme-header {
            background-color: transparent !important;
        }

        .heatmap-programme-cell {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            min-height: 80px;
            box-shadow: inset 0 0 2px rgba(0, 0, 0, 0.05);
        }

        .heatmap-cell {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 6px;
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .heatmap-cell:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .heatmap-tooltip {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 50%;
            transform: translateX(-50%);
            background-color: #1a252f;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
            pointer-events: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .heatmap-cell:hover .heatmap-tooltip {
            opacity: 1;
            visibility: visible;
        }

        .heatmap-legend {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            font-size: 0.9rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .heatmap-legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .heatmap-legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Apply colors to heatmap cells
            const heatmapCells = document.querySelectorAll('.heatmap-cell');
            const maxCount = {{ $heatmapData['maxCount'] }};
            const colors = [
                '#e6f2ff', // lightest
                '#b3d7ff',
                '#80bdff',
                '#4da3ff',
                '#1a88ff',
                '#0066e0', // darkest
            ];

            function getHeatmapColor(count, max) {
                if (count === 0) return '#f8f9fa';
                const index = Math.min(
                    Math.floor((count / max) * (colors.length - 1)),
                    colors.length - 1
                );
                return colors[index];
            }

            heatmapCells.forEach(cell => {
                const count = parseInt(cell.dataset.count || 0);
                const color = getHeatmapColor(count, maxCount);
                cell.style.backgroundColor = color;
                cell.style.border = '1px solid rgba(0, 0, 0, 0.1)';
            });

            // Initialize tooltips for heatmap cells if Bootstrap is available
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            // Function to get color for heatmap cell based on count and max count
            function getHeatmapColor(count, maxCount) {
                if (count === 0) return '#f8f9fa';
                const colors = [
                    '#e6f2ff', // lightest
                    '#b3d7ff',
                    '#80bdff',
                    '#4da3ff',
                    '#1a88ff',
                    '#0066e0', // darkest
                ];
                const index = Math.min(
                    Math.floor((count / maxCount) * (colors.length - 1)),
                    colors.length - 1
                );
                return colors[index];
            }

            // Initialize heatmap if data is available
            @if(isset($heatmapData) && $isAdmin && !empty($heatmapData['programmes']))
                const heatmapData = @json($heatmapData);
                
                // Apply colors and animations to heatmap cells
                document.querySelectorAll('.heatmap-cell').forEach(cell => {
                    const count = parseInt(cell.dataset.count) || 0;
                    const maxCount = heatmapData.maxCount;
                    const color = getHeatmapColor(count, maxCount);
                    
                    cell.style.backgroundColor = color;
                    
                    // Add click handler
                    cell.addEventListener('click', function() {
                        const programmeId = this.dataset.programmeId;
                        const level = this.dataset.level;
                        const programme = heatmapData.programmes.find(p => p.id == programmeId);
                        
                        if (programme) {
                            console.log(`Clicked on ${programme.code} - Level ${level}`);
                            console.log('Mappings:', programme.levels[level]?.mappings || []);
                        }
                    });

                    // Add subtle animation on load
                    cell.style.opacity = '0';
                    cell.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        cell.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                        cell.style.opacity = '1';
                        cell.style.transform = 'scale(1)';
                    }, 100 * (parseInt(cell.dataset.count) || 1));
                });
            @endif
        });
    </script>
@endif