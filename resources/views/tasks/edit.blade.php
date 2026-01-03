@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>Edit Task</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tasks.index') }}">Tasks</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Task Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Task Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" 
                                           value="{{ old('title', $task->title) }}" required>
                                    @error('title')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority">Priority *</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority" required>
                                        <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="critical" {{ old('priority', $task->priority) == 'critical' ? 'selected' : '' }}>Critical</option>
                                    </select>
                                    @error('priority')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="assigned_to">Assign To</label>
                                    <select class="form-control @error('assigned_to') is-invalid @enderror" 
                                            id="assigned_to" name="assigned_to">
                                        <option value="">Unassigned</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" 
                                                {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                           id="due_date" name="due_date" 
                                           value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                                    @error('due_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status" required>
                                        <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="on_hold" {{ old('status', $task->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                                        <option value="cancelled" {{ old('status', $task->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_id">Project</label>
                                    <select class="form-control @error('project_id') is-invalid @enderror" 
                                            id="project_id" name="project_id">
                                        <option value="">No Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" 
                                                {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="task_type">Task Type</label>
                                    <select class="form-control @error('task_type') is-invalid @enderror" 
                                            id="task_type" name="task_type">
                                        <option value="general" {{ old('task_type', $task->task_type) == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="bug" {{ old('task_type', $task->task_type) == 'bug' ? 'selected' : '' }}>Bug Fix</option>
                                        <option value="feature" {{ old('task_type', $task->task_type) == 'feature' ? 'selected' : '' }}>Feature</option>
                                        <option value="maintenance" {{ old('task_type', $task->task_type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="documentation" {{ old('task_type', $task->task_type) == 'documentation' ? 'selected' : '' }}>Documentation</option>
                                        <option value="meeting" {{ old('task_type', $task->task_type) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                                    </select>
                                    @error('task_type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="estimated_hours">Estimated Hours</label>
                                    <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror" 
                                           id="estimated_hours" name="estimated_hours" min="0" step="0.5"
                                           value="{{ old('estimated_hours', $task->estimated_hours) }}">
                                    @error('estimated_hours')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="actual_hours">Actual Hours</label>
                                    <input type="number" class="form-control @error('actual_hours') is-invalid @enderror" 
                                           id="actual_hours" name="actual_hours" min="0" step="0.5"
                                           value="{{ old('actual_hours', $task->actual_hours) }}">
                                    @error('actual_hours')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="progress">Progress (%)</label>
                                    <input type="number" class="form-control @error('progress') is-invalid @enderror" 
                                           id="progress" name="progress" min="0" max="100"
                                           value="{{ old('progress', $task->progress) }}">
                                    @error('progress')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="completion_date">Completion Date</label>
                                    <input type="date" class="form-control @error('completion_date') is-invalid @enderror" 
                                           id="completion_date" name="completion_date" 
                                           value="{{ old('completion_date', $task->completion_date ? $task->completion_date->format('Y-m-d') : '') }}">
                                    @error('completion_date')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dependencies">Dependencies (Task IDs)</label>
                            <input type="text" class="form-control @error('dependencies') is-invalid @enderror" 
                                   id="dependencies" name="dependencies" 
                                   value="{{ old('dependencies', $task->dependencies) }}"
                                   placeholder="Enter task IDs separated by commas">
                            @error('dependencies')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tags">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" 
                                   value="{{ old('tags', $task->tags) }}"
                                   placeholder="Enter tags separated by commas">
                            @error('tags')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_milestone" name="is_milestone" 
                                               value="1" {{ old('is_milestone', $task->is_milestone) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_milestone">Mark as Milestone</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" 
                                               id="is_billable" name="is_billable" 
                                               value="1" {{ old('is_billable', $task->is_billable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_billable">Billable Task</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes', $task->notes) }}</textarea>
                            @error('notes')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Task
                            </button>
                            <a href="{{ route('tasks.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <a href="{{ route('tasks.show', $task->id) }}" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> View Task
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Task Information</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Task ID:</dt>
                        <dd class="col-sm-7">#{{ str_pad($task->id, 6, '0', STR_PAD_LEFT) }}</dd>
                        
                        <dt class="col-sm-5">Created By:</dt>
                        <dd class="col-sm-7">{{ $task->creator->name ?? 'System' }}</dd>
                        
                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">{{ $task->created_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Last Updated:</dt>
                        <dd class="col-sm-7">{{ $task->updated_at->format('d M Y, H:i') }}</dd>
                        
                        <dt class="col-sm-5">Time Spent:</dt>
                        <dd class="col-sm-7">{{ $task->time_entries_sum_hours ?? 0 }} hours</dd>
                    </dl>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Task Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($task->status != 'completed')
                        <a href="{{ route('tasks.complete', $task->id) }}" 
                           class="btn btn-success mb-2" onclick="return confirm('Mark this task as completed?');">
                            <i class="fas fa-check"></i> Mark Complete
                        </a>
                        @endif
                        
                        <a href="{{ route('tasks.time.create', $task->id) }}" 
                           class="btn btn-warning mb-2">
                            <i class="fas fa-clock"></i> Log Time
                        </a>
                        
                        <button type="button" class="btn btn-info mb-2" onclick="duplicateTask()">
                            <i class="fas fa-copy"></i> Duplicate Task
                        </button>
                        
                        <a href="{{ route('tasks.subtask.create', $task->id) }}" 
                           class="btn btn-secondary mb-2">
                            <i class="fas fa-plus"></i> Add Sub-Task
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">Related Items</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Sub-Tasks:</strong>
                        @if($task->subtasks->count() > 0)
                            <ul class="list-group mt-2">
                                @foreach($task->subtasks as $subtask)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('tasks.show', $subtask->id) }}">{{ $subtask->title }}</a>
                                    <span class="badge bg-{{ $subtask->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($subtask->status) }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mt-2 mb-0">No sub-tasks</p>
                        @endif
                    </div>
                    
                    <div>
                        <strong>Attachments:</strong>
                        @if($task->attachments->count() > 0)
                            <ul class="list-group mt-2">
                                @foreach($task->attachments as $attachment)
                                <li class="list-group-item">
                                    <a href="{{ Storage::url($attachment->path) }}" target="_blank">
                                        {{ $attachment->original_name }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted mt-2 mb-0">No attachments</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function duplicateTask() {
    if(confirm('Duplicate this task?')) {
        window.location.href = "{{ route('tasks.duplicate', $task->id) }}";
    }
}
</script>
@endpush