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
            @foreach($items as $k => $v)
                <tr>
                    <td>{{ $k }}</td>
                    <td>{{ $v['count'] }}</td>
                    <td>{{ $v['price'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
