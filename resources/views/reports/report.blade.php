<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <x-reports.salesAnalysis :items="$salesAnalysis" />
            </div>
            <div class="col-6">
                <x-reports.transactionSources :items="$transactionSources" />
            </div>
            <div class="col-6">
                <x-reports.dealsByManager :items="$dealsByManager" />
            </div>
            <div class="col-6">
                <x-reports.salesByManager :items="$salesByManager" />
            </div>
        </div>
    </div>
</x-layout>
