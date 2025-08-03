@if($isAdmin && !empty($heatmapData['programmes']) && $currentSession)
    <!-- Heatmap Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>
                            Course Mappings Overview
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-calendar-week me-1"></i>
                                {{ $currentSession->name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 heatmap-table">
                            <thead>
                                <tr>
                                    <th class="text-start ps-3">Programme</th>
                                    @foreach($heatmapData['levels'] as $level)
                                        <th>{{ $level }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($heatmapData['programmes'] as $programme)
                                <tr>
                                    <td class="heatmap-programme" title="{{ $programme['name'] }}">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 p-1 rounded me-2">
                                                <i class="bi bi-journal-bookmark text-primary"></i>
                                            </div>
                                            <div class="text-truncate">
                                                <div class="fw-bold">{{ $programme['code'] }}</div>
                                                <small class="text-muted d-block">
                                                    {{ $programme['name'] }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($heatmapData['levels'] as $level)
                                        @php
                                            $count = $programme['levels'][$level]['count'] ?? 0;
                                            $mappings = $programme['levels'][$level]['mappings'] ?? [];
                                        @endphp
                                        <td class="heatmap-cell" 
                                            data-programme-id="{{ $programme['id'] }}" 
                                            data-level="{{ $level }}"
                                            data-count="{{ $count }}">
                                            <div class="heatmap-count"></div>
                                            <div class="heatmap-tooltip">
                                                {{ $programme['code'] }} - Level {{ $level }}
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Legend -->
                    <div class="p-3 border-top">
                        <div class="heatmap-legend">
                            <span class="me-3">
                                <small><strong>Courses per level:</strong></small>
                            </span>
                            @php
                                $maxCount = $heatmapData['maxCount'];
                                $steps = min(5, $maxCount);
                                $stepSize = $maxCount > 0 ? $maxCount / $steps : 1;
                                
                                // Define color scale for server-side rendering
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
                                    
                                    // Calculate index based on count relative to max count
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
        </div>
    </div>
    
    <style>
        .heatmap-container {
            width: 100%;
            overflow-x: auto;
        }
        
        .heatmap-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
        }
        
        .heatmap-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: #343a40;
        }
        
        .heatmap-table {
            width: 100%;
            margin-bottom: 0;
        }
        
        .heatmap-table th, 
        .heatmap-table td {
            text-align: center;
            vertical-align: middle;
            padding: 0.5rem;
        }
        
        .heatmap-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            white-space: nowrap;
            border-bottom: 2px solid #dee2e6;
        }
        
        .heatmap-programme {
            text-align: left !important;
            max-width: 300px;
            min-width: 250px;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6 !important;
        }
        
        .heatmap-cell {
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 60px;
            height: 60px;
            border-radius: 8px;
            border: 10px solid white;
            margin: -5px;
        }
        
        .heatmap-cell:hover {
            transform: scale(1.02);
            z-index: 5;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .heatmap-count {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .heatmap-tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #343a40;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            z-index: 10;
            pointer-events: none;
        }
        
        .heatmap-cell:hover .heatmap-tooltip {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 5px);
        }
        
        .heatmap-legend {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            font-size: 0.8rem;
        }
        
        .heatmap-legend-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .heatmap-legend-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips for heatmap cells if they exist
            if (typeof bootstrap !== 'undefined') {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
            
            // Function to get color for heatmap cell based on count and max count
            function getHeatmapColor(count, maxCount) {
                if (count === 0) return '#f8f9fa';
                
                // Define color scale from light to dark blue
                const colors = [
                    '#e6f2ff', // lightest
                    '#b3d7ff',
                    '#80bdff',
                    '#4da3ff',
                    '#1a88ff',
                    '#0066e0', // darkest
                ];
                
                // Calculate index based on count relative to max count
                const index = Math.min(
                    Math.floor((count / maxCount) * (colors.length - 1)),
                    colors.length - 1
                );
                
                return colors[index];
            }
            
            // Initialize heatmap if data is available
            @if(isset($heatmapData) && $isAdmin && !empty($heatmapData['programmes']))
                const heatmapData = @json($heatmapData);
                
                // Apply colors to heatmap cells
                document.querySelectorAll('.heatmap-cell').forEach(cell => {
                    const count = parseInt(cell.dataset.count) || 0;
                    const maxCount = heatmapData.maxCount;
                    const color = getHeatmapColor(count, maxCount);
                    
                    cell.style.backgroundColor = color;
                    
                    // Add click handler if needed
                    cell.addEventListener('click', function() {
                        const programmeId = this.dataset.programmeId;
                        const level = this.dataset.level;
                        const programme = heatmapData.programmes.find(p => p.id == programmeId);
                        
                        if (programme) {
                            // You can add custom click behavior here
                            console.log(`Clicked on ${programme.code} - Level ${level}`);
                            console.log('Mappings:', programme.levels[level]?.mappings || []);
                        }
                    });
                });
            @endif
        });
    </script>
@endif