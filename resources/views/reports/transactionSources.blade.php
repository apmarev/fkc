<x-layout>
    <div class="cell-wrapper">
        <div class="cell-wrapper__caption">
            Источники сделок (Клиенты в активной работе )
        </div>

        <table class="table">
            <thead>
            <tr>
                <th scope="col">Источник</th>
                <th scope="col">Кол-во</th>
                <th scope="col">Сумма</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['count'] }}</td>
                    <td>{{ $item['price'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
