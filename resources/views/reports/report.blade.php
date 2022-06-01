<x-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <x-reports.salesAnalysis :items="$salesAnalysis" />
            </div>
            <div class="col-6">
                <x-reports.transactionSources :items="$transactionSources" />
            </div>
            <div class="col-6"></div>
            <div class="col-6"></div>
            <div class="col-6"></div>
            <div class="col-6"></div>
        </div>
    </div>
</x-layout>
