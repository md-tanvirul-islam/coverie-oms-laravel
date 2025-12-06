<div class="container-fluid">
    {{-- Filters --}}
    <div class="card mb-3">
        <div class="card-body row g-3">

            <div class="col-md-2">
                <label for="mode-dp" class="form-label">Mode</label>
                <select id="mode-dp" wire:model.live="mode" class="form-select">
                    <option value="days">Days</option>
                    <option value="months">Months</option>
                </select>
            </div>

            <div class="col-md-2" @if ($mode === 'months') style="display:none" @endif>
                <label class="form-label">From</label>
                <input type="date" wire:model.live="from" class="form-control" />
            </div>

            <div class="col-md-2" @if ($mode === 'months') style="display:none" @endif>
                <label class="form-label">To</label>
                <input type="date" wire:model.live="to" class="form-control" />
            </div>

            <div class="col-md-2" @if ($mode !== 'months') style="display:none" @endif>
                <label class="form-label">Year</label>
                <select wire:model.live="year" class="form-select">
                    <option value="">Select One</option>
                    @foreach (range(2024, now()->year) as $yr)
                        <option value="{{ $yr }}">{{ $yr }}</option>
                    @endforeach
                </select>
            </div>

        </div>
    </div>

    {{-- Chart --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="chart-container" style="position: relative; height: 40vh; min-height: 300px; width: 100%;">
                <canvas id="collectChart"></canvas>
            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chartInstance = null;

        function renderChart(labels, values) {
            const ctx = document.getElementById('collectChart');

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Collected Amount',
                        data: values,
                        backgroundColor: 'rgba(13,110,253,0.7)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refreshChart', (data) => {
                renderChart(data.labels, data.values);
            });
        });
    </script>
@endpush
