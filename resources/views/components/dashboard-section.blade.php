@props([
    'key',
    'data'
])

@php
    $hasIssueData = ($data['lewatCount'] ?? 0) > 0 || ($data['terbengkalaiCount'] ?? 0) > 0 || ($data['kemaskiniCount'] ?? 0) > 0;
    $hasAuditData = ($data['jumlahKeseluruhan'] ?? 0) > 0;
@endphp

<div class="space-y-12">
    <!-- Row 1: Charts -->
    <div class="flex flex-col md:flex-row gap-8">
        @if($hasAuditData)
            <div class="w-full md:w-1/2 flex justify-center items-center">
                <div style="position: relative; height:400px; width:100%; max-width:400px;">
                    <canvas id="auditPieChart-{{ $key }}"></canvas>
                </div>
            </div>
        @endif
        @if($hasIssueData)
            <div class="w-full md:w-1/2 flex justify-center items-center">
                <div style="position: relative; height:400px; width:100%; max-width:400px;">
                    <canvas id="statusPieChart-{{ $key }}"></canvas>
                </div>
            </div>
        @endif
    </div>

    <!-- Row 2: Tables -->
    <div class="w-full flex flex-col gap-4">
        <x-collapsible-table 
            title="Edaran Kertas Siasatan Lewat 48 Jam" 
            :collection="$data['ksLewat']" 
            bgColor="bg-red-50 dark:bg-red-900/20" 
            :pageName="$key.'_lewat_page'"
            issueType="lewat"
        />
        <x-collapsible-table 
            title="Kertas Siasatan Terbengkalai Melebihi 3 Bulan" 
            :collection="$data['ksTerbengkalai']" 
            bgColor="bg-yellow-50 dark:bg-yellow-900/20" 
            :pageName="$key.'_terbengkalai_page'"
            issueType="terbengkalai"
        />
        <x-collapsible-table 
            title="KS Terbengkalai / Baru Dikemaskini Selepas Semboyan" 
            :collection="$data['ksBaruKemaskini']" 
            bgColor="bg-green-50 dark:bg-green-900/20" 
            :pageName="$key.'_kemaskini_page'"
            issueType="kemaskini"
        />
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            // Delay initialization to ensure the correct tab is visible
            setTimeout(() => {
                @if($hasAuditData)
                    const auditCtx_{{ $key }} = document.getElementById('auditPieChart-{{ $key }}').getContext('2d');
                    new Chart(auditCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            labels: ['Jumlah Diperiksa (KS)', 'Jumlah Belum Diperiksa (KS)'],
                            datasets: [{
                                data: [{{ $data['jumlahDiperiksa'] }}, {{ $data['jumlahBelumDiperiksa'] }}],
                                backgroundColor: ['#0ea5e9', '#cbd5e1'],
                                borderColor: '#FFFFFF', borderWidth: 2,
                            }]
                        },
                        options: {
                           responsive: true, maintainAspectRatio: false,
                           plugins: { title: { display: true, text: 'Peratusan Status Pemeriksaan ({{ $data["jumlahKeseluruhan"] }} Keseluruhan)' } }
                        }
                    });
                @endif

                @if($hasIssueData)
                    const statusCtx_{{ $key }} = document.getElementById('statusPieChart-{{ $key }}').getContext('2d');
                    new Chart(statusCtx_{{ $key }}, {
                        type: 'pie',
                        data: {
                            labels: ['KS Lewat Edar (> 48 Jam)', 'KS Terbengkalai (> 3 Bulan)', 'KS Baru Dikemaskini'],
                            datasets: [{
                                data: [{{ $data['lewatCount'] }}, {{ $data['terbengkalaiCount'] }}, {{ $data['kemaskiniCount'] }}],
                                backgroundColor: ['#F87171', '#FBBF24', '#34D399'],
                                borderColor: '#FFFFFF', borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { title: { display: true, text: 'Ringkasan Status Isu Siasatan' } }
                        }
                    });
                @endif
            }, 100);
        });
    </script>
</div>