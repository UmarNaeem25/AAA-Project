<div class="p-4">
    <h5 class="modal-title mb-3">{{ isset($shift) ? 'Edit Shift' : 'Create Shift' }}</h5>

    <form id="shiftForm" action="{{ route('shifts.store') }}" method="POST">
        @csrf
        @if (isset($shift))
            <input type="hidden" name="id" value="{{ $shift->id }}">
        @endif

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Date *</label>
                <input type="date" name="date" class="form-control" value="{{ $date ?? ($shift->date ?? '') }}"
                    required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">From Time *</label>
                <input type="time" id="fromTime" name="from" class="form-control"
                    value="{{ $shift->from ?? '09:00' }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">To Time *</label>
                <input type="time" id="toTime" name="to" class="form-control"
                    value="{{ $shift->to ?? '17:00' }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Duration (hours) *</label>
                <input type="number" id="duration" name="duration" class="form-control" step="0.01"
                    value="{{ $shift->duration ?? '8.00' }}" required readonly>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Break Time (hours) *</label>
                <input type="number" name="break_time" class="form-control" step="0.01"
                    value="{{ $shift->break_time ?? '1.00' }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Location *</label>
                <select name="location_id" class="form-control" required>
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}"
                            {{ isset($shift) && $shift->location_id == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Role *</label>
                <select name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ isset($shift) && $shift->role_id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Assign User</label>
                <select name="user_id" class="form-control">
                    <option value="">Open Shift (No user assigned)</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ isset($shift) && $shift->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->weekly_hours_limit }}h/week)
                        </option>
                    @endforeach
                </select>
            </div>

            @if (isset($shift))
                <div class="col-12 mb-3 d-none">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="open" {{ $shift->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="unpublished" {{ $shift->status == 'unpublished' ? 'selected' : '' }}>Unpublished
                        </option>
                        <option value="published" {{ $shift->status == 'published' ? 'selected' : '' }}>Published
                        </option>
                    </select>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary shift_button">
                {{ isset($shift) ? 'Update Shift' : 'Create Shift' }}
            </button>
        </div>
    </form>
</div>
