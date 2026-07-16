@extends('layouts.admin')

@section('title', 'Business Hours')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Weekly Hours</strong></div>
                <div class="card-body">
                    <form action="{{ route('admin.hours.update') }}" method="post">
                        @csrf @method('PUT')
                        @foreach($hours as $day => $hour)
                            <div class="row g-2 align-items-center mb-2">
                                <input type="hidden" name="hours[{{ $day }}][day_of_week]" value="{{ $day }}">
                                <div class="col-3 fw-bold">{{ $hour->dayName() }}</div>
                                <div class="col-3">
                                    <label class="visually-hidden" for="open-{{ $day }}">Open time for {{ $hour->dayName() }}</label>
                                    <input id="open-{{ $day }}" class="form-control form-control-sm" type="time" name="hours[{{ $day }}][open_time]"
                                           value="{{ old("hours.$day.open_time", $hour->open_time ? substr($hour->open_time, 0, 5) : '') }}">
                                </div>
                                <div class="col-3">
                                    <label class="visually-hidden" for="close-{{ $day }}">Close time for {{ $hour->dayName() }}</label>
                                    <input id="close-{{ $day }}" class="form-control form-control-sm" type="time" name="hours[{{ $day }}][close_time]"
                                           value="{{ old("hours.$day.close_time", $hour->close_time ? substr($hour->close_time, 0, 5) : '') }}">
                                </div>
                                <div class="col-3 form-check">
                                    <input type="hidden" name="hours[{{ $day }}][is_closed]" value="0">
                                    <input class="form-check-input" type="checkbox" name="hours[{{ $day }}][is_closed]" value="1"
                                           id="closed-{{ $day }}" @checked(old("hours.$day.is_closed", $hour->is_closed))>
                                    <label class="form-check-label" for="closed-{{ $day }}">Closed</label>
                                </div>
                            </div>
                        @endforeach
                        <button class="btn btn-primary mt-2" type="submit">Save Hours</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="card mb-3">
                <div class="card-header bg-white"><strong>Holiday / Special Hours</strong></div>
                <ul class="list-group list-group-flush">
                    @forelse($holidays as $holiday)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ \Carbon\Carbon::parse($holiday->date)->format('M j, Y') }}</strong> — {{ $holiday->label }}
                                <div class="small text-body-secondary">
                                    {{ $holiday->is_closed ? 'Closed' : \Carbon\Carbon::parse($holiday->open_time)->format('g:i A').' – '.\Carbon\Carbon::parse($holiday->close_time)->format('g:i A') }}
                                </div>
                            </div>
                            <form action="{{ route('admin.hours.holidays.destroy', $holiday) }}" method="post" data-confirm="Remove this holiday entry?">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                            </form>
                        </li>
                    @empty
                        <li class="list-group-item text-body-secondary">No holiday hours set.</li>
                    @endforelse
                </ul>
                <div class="card-body">
                    <form action="{{ route('admin.hours.holidays.store') }}" method="post">
                        @csrf
                        <div class="row g-2">
                            <div class="col-12">
                                <label class="visually-hidden" for="h-date">Date</label>
                                <input id="h-date" class="form-control" type="date" name="date" required>
                            </div>
                            <div class="col-12">
                                <label class="visually-hidden" for="h-label">Label</label>
                                <input id="h-label" class="form-control" name="label" placeholder="e.g. Thanksgiving Day" required maxlength="190">
                            </div>
                            <div class="col-6">
                                <label class="visually-hidden" for="h-open">Open time</label>
                                <input id="h-open" class="form-control" type="time" name="open_time">
                            </div>
                            <div class="col-6">
                                <label class="visually-hidden" for="h-close">Close time</label>
                                <input id="h-close" class="form-control" type="time" name="close_time">
                            </div>
                            <div class="col-12 form-check">
                                <input type="hidden" name="is_closed" value="0">
                                <input class="form-check-input" type="checkbox" name="is_closed" value="1" id="h-closed" checked>
                                <label class="form-check-label" for="h-closed">Closed all day</label>
                            </div>
                            <div class="col-12"><button class="btn btn-outline-primary w-100" type="submit">Add Holiday</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
