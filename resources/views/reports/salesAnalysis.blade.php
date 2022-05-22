<x-layout>
    <div class="cell-wrapper">
        <div class="cell-wrapper__caption">
            Текущее кол-во сделок в активных статусах (Клиенты в активной работе )
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Статус</th>
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
                <tr>
                    <td>Всего:</td>
                    <td>{{ $size['count'] }}</td>
                    <td>{{ $size['price'] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-layout>
