<div>

    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <button class="btn btn-outline-secondary me-2" wire:click="backToProjects">
                    {{ __('messages.back') }}
                </button>
            </div>
            <h1 class="mb-0">{{ $project->name }}</h1>
            <div style="width: 100px;"></div> <!-- Spacer for alignment -->
        </div>

        <!-- Project Details Card -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            {{ __('messages.information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">{{ __('messages.start_date') }}</label>
                                <h5>{{ \Carbon\Carbon::parse($project->start_date)->format('Y/m/d') }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">{{ __('messages.end_date') }}</label>
                                <h5>{{ \Carbon\Carbon::parse($project->end_date)->format('Y/m/d') }}</h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">{{ __('messages.duration') }}</label>
                                <h5>
                                    @php
                                        $duration = \Carbon\Carbon::parse($project->start_date)
                                            ->diffInDays($project->end_date);
                                    @endphp
                                    {{ $duration }} {{ __('messages.days') }}
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">{{ __('messages.status') }}</label>
                                <h5>
                                    @php
                                        $now = \Carbon\Carbon::now();
                                        $start = \Carbon\Carbon::parse($project->start_date);
                                        $end = \Carbon\Carbon::parse($project->end_date);

                                        if ($now < $start) {
                                            $statusText = __('messages.not_started');
                                            $statusClass = 'bg-secondary';
                                        } elseif ($now > $end) {
                                            $statusText = __('messages.completed');
                                            $statusClass = 'bg-success';
                                        } else {
                                            $statusText = __('messages.in_progress');
                                            $statusClass = 'bg-warning';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-2">{{ $statusText }}</span>
                                </h5>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        @if($now >= $start && $now <= $end)
                            @php
                                $totalDays = $start->diffInDays($end);
                                $daysPassed = $start->diffInDays($now);
                                $progress = min(100, round(($daysPassed / max(1, $totalDays)) * 100));
                                $daysRemaining = $end->diffInDays($now);
                            @endphp
                            <div class="mt-4">
                                <h6>{{ __('messages.project_progress') }}</h6>
                                <div class="progress mb-2" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                         role="progressbar"
                                         style="width: {{ $progress }}%">
                                        {{ $progress }}%
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        {{ __('messages.days_passed') }}: {{ round($daysPassed) }}
                                    </small>
                                    <small class="text-muted">
                                        {{ __('messages.days_remaining') }}: {{ round($daysRemaining) }}
                                    </small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- اطمینان از وجود مقادیر قبل از ارسال -->
                @php
                    $activeFolder = $activeFolder ?? null;
                @endphp

                @livewire('tasks', [
                'filterByProjectId' => $project->id,
                'filterByFolderId' => $activeFolder
                ], key($project->id . '-' . $activeFolder))
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-1 border-light-subtle mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            👤 {{ __('messages.project_owner') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div
                                    class="bg-light text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    {{ substr($project->owner->name ?? 'U', 0, 1) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $project->owner->name ?? 'Unknown' }}</h6>
                                <small class="text-muted">{{ $project->owner->email ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-1 border-light-subtle mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            🗓️ {{ __('messages.timeline') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item pb-3">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">{{ __('messages.created_at') }}</h6>
                                    <small class="text-muted">
                                        {{ $project->created_at->format('Y/m/d') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-1 border-light-subtle">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            📦 {{$project->name}}
                        </h6>
                    </div>
                    <div class="card-body"  wire:lo>
                        @if(!empty($tasks))
                            <ul style="list-style-type: none;">
                                <li class="pointer-event" wire:click="setActiveFolder()">🏠 {{__('messages.root')}}</li>
                                @foreach($tasks as $task)
                                    <li>📄 {{$task}}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!empty($folders))
                            <ul style="list-style-type: none;">
                                @foreach($folders as $folder)
                                    <li>
                                        @include('livewire.folder-item', ['folder' => $folder,'activeFolder'=>$activeFolder])
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-marker {
            position: absolute;
            left: -30px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            top: 5px;
        }

        .timeline-content {
            padding-left: 10px;
        }

        li {
            cursor: pointer;
        }
    </style>
</div>
