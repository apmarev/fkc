<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <x-reports.completedTasks :items="$completedTasks" />
            </div>
            <div class="col-6">
                <x-reports.createdTasks :items="$createdTasks" />
            </div>
            <div class="col-6">
                <x-reports.closedTasksByManagers :items="$closedTasksByManagers" />
            </div>
            <div class="col-6">
{{--                <x-reports.createdNotesForManagers :items="$createdNotesForManagers" />--}}
            </div>
        </div>
    </div>
</x-layout>
