<div class="cell-wrapper">
    <div class="cell-wrapper__caption">
        <p class="caps">Сделки по менеджерам</p>
        <p class="caps">Клиенты в активной работе</p>
    </div>

    <table class="table">
        <thead>
        <tr>
            <th scope="col">Менеджер</th>
            <th scope="col">Кол-во</th>
            <th scope="col">Сумма</th>
            <th scope="col">Бюджет</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items['items'] as $item)
            @if(sizeof($item['users']) > 0)
                <tr>
                    <td colspan="4">
                        {{ $item['name'] }}
                    </td>
                </tr>
                @foreach($item['users'] as $user)
                    <tr>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['count'] }}</td>
                        <td>{{ $user['price'] }}</td>
                        <td>{{ $user['budget'] }}</td>
                    </tr>
                @endforeach
            @endif
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
