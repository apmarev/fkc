<div class="cell-wrapper">
    <div class="cell-wrapper__caption">
        <p class="caps">Текущее кол-во сделок в активных статусах</p>
        <p class="caps">Клиенты в активной работе</p>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Статус</th>
            <th scope="col">Кол-во</th>
            <th scope="col">Сумма</th>
            <th scope="col">Бюджет</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items['items'] as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['count'] }}</td>
                <td>{{ $item['price'] }}</td>
                <td>{{ $item['budget'] }}</td>
            </tr>
        @endforeach
        <tr>
            <td><b>Всего:</b></td>
            <td><b>{{ $items['size']['count'] }}</b></td>
            <td><b>{{ $items['size']['price'] }}</b></td>
            <td><b>{{ $items['size']['budget'] }}</b></td>
        </tr>
        </tbody>
    </table>
</div>

