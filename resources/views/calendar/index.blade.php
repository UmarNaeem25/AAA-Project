@extends('layouts.app')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <style>
        #calendar {
            width: 100% !important;
            max-width: 100% !important;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .fc-toolbar-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .fc-event {
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 2px 4px;
            font-size: 12px;
        }

        .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 18px;
            height: 18px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Shift Calendar</h4>
        </div>

        <div class="legend">
            <div class="legend-item">
                <div class="legend-color" style="background-color:#e2af4a;"></div><small>Published</small>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background-color:#57a8cd;"></div><small>Open Shift</small>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mb-4 align-items-start">
            <div style="min-width: 200px;">
                <label class="form-label mb-1 fw-semibold text-muted small">Location</label>
                <input type="text" id="locationFilter" class="form-control shadow-sm"
                    placeholder="Search by location name">
            </div>
            <div style="min-width: 200px;">
                <label class="form-label mb-1 fw-semibold text-muted small">User</label>
                <input type="text" id="userFilter" class="form-control shadow-sm" placeholder="Search by user name">
            </div>

            <button id="bulkAssignBtn" class="btn btn-primary mt-4">Assign Open Shifts</button>
        </div>

        <div id="calendar"></div>
    </div>

    <div class="modal fade" id="shiftModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body p-0" id="shiftFormContainer"></div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        let calendar;
        let filters = {
            location: '',
            user: ''
        };

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    let params = new URLSearchParams();
                    if (filters.location) params.append('location', filters.location);
                    if (filters.user) params.append('user', filters.user);

                    fetch(`/calendar/events?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                },
                dateClick: function(info) {
                    axios.get("/shifts/create", {
                            params: {
                                date: info.dateStr
                            }
                        })
                        .then(res => {
                            document.getElementById('shiftFormContainer').innerHTML = res.data;
                            initShiftForm();
                            new bootstrap.Modal(document.getElementById('shiftModal')).show();
                        });
                },
                eventClick: function(info) {
                    axios.get("/shifts/create", {
                            params: {
                                id: info.event.id
                            }
                        })
                        .then(res => {
                            document.getElementById('shiftFormContainer').innerHTML = res.data;
                            initShiftForm();
                            new bootstrap.Modal(document.getElementById('shiftModal')).show();
                        });
                }
            });

            calendar.render();

            document.getElementById('locationFilter').addEventListener('input', function(e) {
                filters.location = e.target.value;
                calendar.refetchEvents();
            });

            document.getElementById('userFilter').addEventListener('input', function(e) {
                filters.user = e.target.value;
                calendar.refetchEvents();
            });

            document.getElementById('bulkAssignBtn').addEventListener('click', function() {
                let date = new Date().toISOString().split('T')[0]; 

                this.disabled = true;
                this.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Processing...`;

                axios.post('/calendar/assign-all-open-shifts', {
                        date: date
                    })
                    .then(res => {
                        Swal.fire('Success', `${res.data.assignedShifts.length} shifts assigned!`, 'success');
                        calendar.refetchEvents();
                        this.disabled = false;
                        this.innerHTML = 'Assign Open Shifts';
                    })
                    .catch(err => {
                        Swal.fire('Error', err.response?.data?.message || 'Error occurred', 'error');
                        this.disabled = false;
                        this.innerHTML = 'Assign Open Shifts';
                    });
            });

        });

        function initShiftForm() {
            const form = document.getElementById('shiftForm');
            if (!form) return;
            const submitBtn = form.querySelector('.shift_button');
            const from = form.querySelector('#fromTime');
            const to = form.querySelector('#toTime');
            const duration = form.querySelector('#duration');

            function setWorking(isWorking) {
                if (!submitBtn) return;
                submitBtn.disabled = isWorking;
                if (isWorking) submitBtn.innerHTML =
                    `<span class="spinner-border spinner-border-sm me-1"></span>Processing...`;
            }

            function calculateDuration() {
                if (!from.value || !to.value) return;
                let [fh, fm] = from.value.split(':').map(Number);
                let [th, tm] = to.value.split(':').map(Number);
                let start = new Date(0, 0, 0, fh, fm);
                let end = new Date(0, 0, 0, th, tm);
                if (end < start) end.setDate(end.getDate() + 1);
                duration.value = ((end - start) / 1000 / 60 / 60).toFixed(2);
            }

            if (from && to) {
                from.addEventListener('change', calculateDuration);
                to.addEventListener('change', calculateDuration);
                calculateDuration();
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(form);
                setWorking(true);

                axios.post(form.action, formData)
                    .then(res => {
                        Swal.fire('Success', res.data.message, 'success');
                        bootstrap.Modal.getInstance(document.getElementById('shiftModal')).hide();
                        calendar.refetchEvents();
                        setWorking(false);
                    })
                    .catch(err => {
                        Swal.fire('Error', err.response?.data?.message || 'Error occurred', 'error');
                        setWorking(false);
                    });
            });
        }
    </script>
@endpush
