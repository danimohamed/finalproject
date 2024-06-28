@extends(backpack_view('blank'))

@php
use Illuminate\Support\Facades\DB;

// Use the DB facade to get the count of users
$userCount = DB::table('users')->count();
$teacherCount = DB::table('teachers')->count();
$groupCount = DB::table('groups')->count();
$roomCount = DB::table('rooms')->count();
$scheduleCount = DB::table('time_schedules')->count();

// Find the maximum count to set as 100% mark for progress bars
$maxCount = max($userCount, $teacherCount, $groupCount, $roomCount, $scheduleCount);

// Helper function to calculate the progress percentage
$calculateProgress = function ($count) use ($maxCount) {
return ($maxCount > 0) ? ($count / $maxCount) * 100 : 0;
};

// Prepare widgets array with progress bars for each category
$widgets['before_content'] = [

];
Widget::add()
->to('before_content')
->type('div')
->class('row mt-3')
->content([
// Widget::make()
// ->type('progress')
// ->class('card mb-3')
// ->statusBorder('start')
// ->accentColor('primary')
// ->ribbon(['top','la-users'])
// ->progressClass('progress-bar')
// ->value($userCount)
// ->description('Registered Users.')
// ->progress($calculateProgress($userCount)),
Widget::make()
->type('progress')
->class('card mb-3')
->statusBorder('start')
->accentColor('success')
->ribbon(['top','la-shield'])
->progressClass('progress-bar')
->value($teacherCount)
->description('Registered Teachers.')
->progress($calculateProgress($teacherCount)),
Widget::make()
->type('progress')
->class('card mb-3')
->statusBorder('start')
->accentColor('warning')
->ribbon(['top','la-university'])
->progressClass('progress-bar')
->value($roomCount)
->description('Registered Rooms.')
->progress($calculateProgress($roomCount)),
Widget::make()
->type('progress')
->class('card mb-3')
->statusBorder('start')
->accentColor('danger')
->ribbon(['top','la-calendar'])
->progressClass('progress-bar')
->value($scheduleCount)
->description('Registered Schedules.')
->progress($calculateProgress($scheduleCount)),
Widget::make()
->type('progress')
->class('card mb-3')
->statusBorder('start')
->accentColor('info')
->ribbon(['top','la-users'])
->progressClass('progress-bar')
->value($groupCount)
->description('Registered Groups.')
->progress($calculateProgress($groupCount)),
]);
@endphp

@section('content')
@php

use Carbon\Carbon;

$today = Carbon::now()->format('l'); // Gets the current day of the week
$schedules = DB::table('time_schedules')
->join('teachers', 'time_schedules.teacher', '=', 'teachers.id')
->join('rooms', 'time_schedules.room', '=', 'rooms.id')
->join('groups', 'time_schedules.group', '=', 'groups.id')
->where('time_schedules.day', '=', $today)
->select(
'teachers.teacher_name as teacher_name',
'rooms.room_name as room_name',
'groups.group_name as group_name',
'time_schedules.period'
)
->distinct() // To avoid duplication in case of multiple entries
->get();
@endphp

<div class="row">
    <div class="col-md-12 col-lg-12" style="margin-bottom:15px;">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title">Teachers, Groups and Rooms with schedule today</h3>
            </div>
            <div class="card-body">
                <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                <path d="M21 21l-6 -6"></path>
                            </svg>
                        </span>

                        <input type="search" class="form-control" id="searchInput" onkeyup="filterTable()"
                            placeholder="Search..." aria-controls="crudTable">
                    </div>
                </div>
                <!-- <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search" class="form-control"> -->
            </div>
            <div>
                <div>
                    <table id="scheduleTable" class="table card-table table-vcenter">
                        <thead>
                            <tr>
                                <th>TEACHER NAME</th>
                                <th>GROUP NAME</th>
                                <th>ROOM NAME</th>
                                <th>PERIOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->teacher_name }}</td>
                                <td>{{ $schedule->group_name }}</td>
                                <td>{{ $schedule->room_name }}</td>
                                <td>
                                    @php
                                    $periods = [
                                    'M' => '08:30 - 13:30',
                                    'M1' => '08:30 - 11:00',
                                    'M2' => '11:00 - 13:30',
                                    'A' => '13:30 - 18:30',
                                    'A1' => '13:30 - 16:00',
                                    'A2' => '16:00 - 18:30'
                                    ];
                                    @endphp
                                    {{ $periods[$schedule->period] ?? 'Undefined' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Teachers Section -->

</div>
<script>
function filterTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.querySelector(".table");
    tr = table.getElementsByTagName("tr");

    // Start the loop at 1 to skip the header row
    for (i = 1; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td");
        var foundInRow = false;
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    foundInRow = true;
                    break;
                }
            }
        }
        if (foundInRow) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}
</script>


@endsection